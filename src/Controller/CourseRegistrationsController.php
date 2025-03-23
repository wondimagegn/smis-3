<?php

namespace App\Controller;

use Cake\Core\Configure;
use Cake\Event\Event;

class CourseRegistrationsController extends AppController
{

    public $name = 'CourseRegistrations';

    public $menuOptions = array(
        'parent' => 'registrations',
        'exclude' => array(
            'get_course_registered_grade_list',
            'get_course_registered_grade_result',
            'get_course_category_combo',
            'search',
            'show_course_registred_students',
            'get_section_combo',
            'get_section_combo_for_view',
            'getIndividualRegistration',
            'manage_missing_registration',
            'update_missing_registration'
        ),
        'alias' => array(
            'index' => 'View All Registration',
            'cancel_individual_registration' => 'Cancel Student\'s Registration',
            'cancel_registration' => 'Cancel Section\'s Registration',
            'register_individual_course' => 'Register Courses By Section',
        )
    );

    public function initialize()
    {

        parent::initialize();
        $this->loadComponent('AcademicYear');
        $this->loadComponent('Paginator'); // Ensure Paginator is loaded

        $this->viewBuilder()->setHelpers(['Xls', 'Media.Media']);
    }

    public function beforeFilter(Event $event)
    {

        parent::beforeFilter($event);
        $this->Auth->Allow(
            'show_course_registred_students',
            'get_course_registered_grade_list',
            'get_course_registered_grade_result',
            'search',
            'get_course_category_combo',
            //'registration_view',
            'get_section_combo',
            'get_section_combo_for_view'
        //'manage_missing_registration',
        //'getIndividualRegistration',
        //'update_missing_registration'
        );

        if ($this->role_id == ROLE_STUDENT) {
            $students = ClassRegistry::init('Student')->find(
                'first',
                array('conditions' => array('Student.id' => $this->student_id), 'recursive' => -1)
            );
            if (empty($students['Student']['ecardnumber']) && isset($user["User"]['id']) && !empty($user["User"]['id']) && strcasecmp(
                    $this->request->params['controller'],
                    'students'
                ) != 0 && strcasecmp($this->request->params['action'], 'change') != 0) {
                return $this->redirect(array('controller' => 'students', 'action' => 'change'));
            }
        }
    }

    public function beforeRender(Event $event)
    {

        parent::beforeRender($event);
        //$acyear_array_data = $this->AcademicYear->acyear_array();

        ///////////////////// DONOT EDIT /////////////////////

        $defaultacademicyear = $this->AcademicYear->currentAcademicYear();

        if (is_numeric(ACY_BACK_COURSE_REGISTRATION) && ACY_BACK_COURSE_REGISTRATION) {
            $acyear_array_data = $this->AcademicYear->academicYearInArray(
                ((explode('/', $defaultacademicyear)[0]) - ACY_BACK_COURSE_REGISTRATION),
                (explode('/', $defaultacademicyear)[0])
            );

            $acYearMinuSeparated = $this->AcademicYear->acYearMinuSeparated(
                ((explode('/', $defaultacademicyear)[0]) - ACY_BACK_COURSE_REGISTRATION),
                (explode('/', $defaultacademicyear)[0])
            );
            $defaultacademicyearMinusSeparted = str_replace('/', '-', $defaultacademicyear);
        } else {
            $acyear_array_data[$defaultacademicyear] = $defaultacademicyear;

            $defaultacademicyearMinusSeparted = str_replace('/', '-', $defaultacademicyear);
            $acYearMinuSeparated[$defaultacademicyearMinusSeparted] = $defaultacademicyearMinusSeparted;
        }


        $this->set('defaultacademicyear', $defaultacademicyear);
        $this->set('defaultacademicyearMinusSeparted', $defaultacademicyearMinusSeparted);

        ///////////////////// END DONOT EDIT /////////////////////

        $programs = ClassRegistry::init('Program')->find(
            'list',
            array('conditions' => array('Program.id' => $this->program_ids))
        );
        $program_types = $programTypes = ClassRegistry::init('ProgramType')->find(
            'list',
            array('conditions' => array('ProgramType.id' => $this->program_type_ids))
        );

        $yearLevels = $this->year_levels;

        $this->set(
            compact(
                'acyear_array_data',
                'program_types',
                'programs',
                'programTypes',
                'acYearMinuSeparated',
                'defaultacademicyearMinusSeparted',
                'yearLevels'
            )
        );
    }

    public function search()
    {

        $url['action'] = 'index';

        if (isset($this->request->data) && !empty($this->request->data)) {
            foreach ($this->request->data as $k => $v) {
                if (!empty($v)) {
                    foreach ($v as $kk => $vv) {
                        if (!empty($vv) && is_array($vv)) {
                            foreach ($vv as $kkk => $vvv) {
                                $url[$k . '.' . $kk . '.' . $kkk] = str_replace('/', '-', trim($vvv));
                            }
                        } else {
                            $url[$k . '.' . $kk] = str_replace('/', '-', trim($vv));
                        }
                    }
                }
            }
        }

        return $this->redirect($url, null, true);
    }


    public function __init_search_index()
    {

        if (!empty($this->request->data['Search'])) {
            $this->Session->write('search_data_index', $this->request->data['Search']);
        } else {
            if ($this->Session->check('search_data_index')) {
                $this->request->data['Search'] = $this->Session->read('search_data_index');
            }
        }
    }

    // Function to allow students to register for the published coures

    public function register()
    {

        if ($this->student_id) {
            if ($this->Session->read('Auth.User')['role_id'] == ROLE_STUDENT && !empty(
                $this->Session->read(
                    'Auth.User'
                )['id']
                )
                //&& strcasecmp($this->request->params['controller'], 'students') != 0
                && strcasecmp($this->request->params['action'], 'profile') != 0
                && (ClassRegistry::init('StudentStatusPattern')->isEligibleForExitExam(
                        $this->student_id
                    ) || FORCE_ALL_STUDENTS_TO_FILL_BASIC_PROFILE == 1)
                //&& !$user['User']['force_password_change'] && !$password_duration_expired
                && strcasecmp($this->request->params['controller'], 'users') != 0
                && strcasecmp($this->request->params['action'], 'changePwd') != 0) {
                if (!ClassRegistry::init('StudentStatusPattern')->completedFillingProfileInfomation(
                    $this->student_id
                )) {
                    $this->Flash->warning(
                        'Dear ' . $this->Session->read(
                            'Auth.User'
                        )['first_name'] . ', you are required to complete your basic profile before proceeding. If you encounter an error or unable to update your profile by your own or need further assistance, please report to the registrar record officer who is assigned to your department.'
                    );
                    return $this->redirect(array('controller' => 'students', 'action' => 'profile'));
                }
            }

            //check students are allowed to register based on their academic status.
            $getRegistrationDeadLine = false;
            $get_student_acadamic_status = null;
            $latestSemester = null;

            $latest_academic_year = $this->AcademicYear->current_academicyear();

            $student_section1 = $this->CourseRegistration->Student->student_academic_detail($this->student_id);
            //debug($student_section1);

            if (empty($student_section1['Section']) && !empty($student_section1['StudentsSection'])) {
                $this->Flash->info(
                    'Your previous semester is archieved and you are not assiged to a section for the current semester. Make sure you have a proper section assignment for the this semester before trying to register.'
                );
                $this->redirect(
                    array('controller' => 'students', 'action' => 'student_academic_profile', $this->student_id)
                );
            } else {
                if (empty($student_section1['Section']) && empty($student_section1['StudentsSection'])) {
                    $this->Flash->info(
                        'You are not assiged to any section for ' . (isset($latest_academic_year) && !empty($latest_academic_year) ? $latest_academic_year : $this->AcademicYear->current_academicyear(
                        )) . ' academic year. Communicate your department and make sure you have a proper section assignment in ' . (isset($latest_academic_year) && !empty($latest_academic_year) ? $latest_academic_year : $this->AcademicYear->current_academicyear(
                        )) . ' before trying to register.'
                    );
                    $this->redirect(
                        array('controller' => 'students', 'action' => 'student_academic_profile', $this->student_id)
                    );
                }
            }

            $studentDetails = $this->CourseRegistration->Student->find(
                'first',
                array('conditions' => array('Student.id' => $this->student_id), 'recursive' => -1)
            );

            $getCourseNotRegistered = ClassRegistry::init(
                'StudentsSection'
            )->getMostRecentSectionPublishedCourseNotRegistered($this->student_id);
            //debug($getCourseNotRegistered);

            $isThereFxInPrevAcademicStatus = $this->CourseRegistration->Student->StudentExamStatus->checkFxPresenseInStatus(
                $this->student_id
            );

            if (!empty($getCourseNotRegistered)) {
                $latest_academic_year = $getCourseNotRegistered[0]['PublishedCourse']['academic_year'];
            } else {
                //already Registered or no published course to register
                $this->Flash->info('You alrady registered or no published courses to register for .');
                $this->redirect(array('action' => 'index'));
            }

            $latestAcSemester = $this->CourseRegistration->getLastestStudentSemesterAndAcademicYear(
                $this->student_id,
                $latest_academic_year
            );
            //debug($latestAcSemester);

            $latestSemester = $latestAcSemester['semester'];
            $paymentRequired = $this->CourseRegistration->Student->Payment->paidPayment(
                $this->student_id,
                $latestAcSemester
            );
            $passed_or_failed = $this->CourseRegistration->Student->StudentExamStatus->get_student_exam_status(
                $this->student_id,
                $latest_academic_year,
                $latestSemester
            );

            //debug($passed_or_failed);
            //debug($paymentRequired);

            if (($passed_or_failed == 1 || $passed_or_failed == 3) && ($isThereFxInPrevAcademicStatus == 1) && $paymentRequired) {
                //debug($latest_academic_year);
                //debug($latestSemester);

                $get_student_acadamic_status = $this->CourseRegistration->Student->StudentExamStatus->getStudentAcadamicStatus(
                    $this->student_id,
                    $latest_academic_year,
                    $latestSemester
                );
                $student_section = $this->CourseRegistration->Student->student_academic_detail(
                    $this->student_id,
                    $latest_academic_year
                );

                //debug($get_student_acadamic_status);
                //debug($student_section);


                if (!empty($this->department_id) && !empty($student_section['Section'])) {
                    $year_level_id = $this->CourseRegistration->YearLevel->field(
                        'name',
                        array('id' => $student_section['Section'][0]['year_level_id'])
                    );

                    $getRegistrationDeadLine = $this->CourseRegistration->AcademicCalendar->check_registration(
                        $latest_academic_year,
                        $latestSemester,
                        $this->department_id,
                        $year_level_id,
                        $studentDetails['Student']['program_id'],
                        $studentDetails['Student']['program_type_id']
                    );
                } else {
                    if (!empty($this->college_id) && !empty($student_section['Section'])) {
                        $getRegistrationDeadLine = $this->CourseRegistration->AcademicCalendar->check_registration(
                            $latest_academic_year,
                            $latestSemester,
                            'pre_' . $this->college_id,
                            0,
                            $studentDetails['Student']['program_id'],
                            $studentDetails['Student']['program_type_id']
                        );
                    } else {
                        $this->Flash->info(
                            'You are not assiged to any section for ' . (isset($latest_academic_year) && !empty($latest_academic_year) ? $latest_academic_year : $this->AcademicYear->current_academicyear(
                            )) . ' academic year. Communicate your department and make sure you have a proper section assignment in ' . (isset($latest_academic_year) && !empty($latest_academic_year) ? $latest_academic_year : $this->AcademicYear->current_academicyear(
                            )) . ' before trying to register.'
                        );
                        $this->redirect(
                            array('controller' => 'students', 'action' => 'student_academic_profile', $this->student_id)
                        );
                    }
                }

                debug($getRegistrationDeadLine);

                $registration_start_date = '';

                if (/* $getRegistrationDeadLine == 0 ||  */ $getRegistrationDeadLine == 1) {
                    // $getRegistrationDeadLine=1;//TODO remove imported for the purpose of backregistration
                    //$this->redirect(array('action' => 'index'));
                } else {
                    $registration_start_date = $getRegistrationDeadLine;

                    if (!empty($registration_start_date) && $this->__isDate($registration_start_date)) {
                        $this->Flash->info(
                            'Course registration will start on ' . (date(
                                'M d, Y',
                                strtotime($registration_start_date)
                            )) . '  for ' . ($latestSemester == 'I' ? '1st' : ($latestSemester == 'II' ? '2nd' : '3rd')) . ' semester of ' . $latest_academic_year . ' academic year. Please come back and Register on the date specified.'
                        );
                    } else {
                        if (!$getRegistrationDeadLine) {
                            $this->Flash->default(
                                'Course registration start and end date is not defined for ' . ($latestSemester == 'I' ? '1st' : ($latestSemester == 'II' ? '2nd' : '3rd')) . ' semester of ' . $latest_academic_year . ' academic year. You can <a href="' . BASE_URL_HTTPS . 'pages/academic_calender" target="_blank">check Academic Calendar here</a> and come back later when it is defined.',
                                array(
                                    'params' => array('type' => 'Info', 'class' => 'info-box info-message'),
                                    'escape' => false
                                )
                            );
                        } else {
                            $deadlinepassed = true;
                            $this->set(compact('deadlinepassed'));
                            $this->Flash->warning(
                                'Course registration deadline is passed for ' . ($latestSemester == 'I' ? '1st' : ($latestSemester == 'II' ? '2nd' : '3rd')) . ' semester of ' . $latest_academic_year . ' academic year. Please advise the registrar.'
                            );
                        }
                    }

                    $this->redirect(
                        array('action' => 'index', (str_replace('/', '-', $latest_academic_year)), $latestSemester)
                    );
                }


                ////TODO why i deleted previously ?, introduced after bettycomment/////////////////////////////////////////////////////////////
                $not_registered = $this->CourseRegistration->alreadyRegistred(
                    $latestSemester,
                    $latest_academic_year,
                    $this->student_id
                );

                debug($not_registered);

                if ($not_registered > 0) {
                    $this->Flash->info(
                        'You have already registered  for ' . ($latestSemester == 'I' ? '1st' : ($latestSemester == 'II' ? '2nd' : '3rd')) . ' semester of ' . $latest_academic_year . ' academic year. You can view the courses you have registered here.'
                    );
                    $this->redirect(
                        array('action' => 'index', (str_replace('/', '-', $latest_academic_year)), $latestSemester)
                    );
                }

                if (!empty($this->request->data)) {
                    //check students has already registered
                    debug($this->request->data);
                    $not_registered = $this->CourseRegistration->alreadyRegistred(
                        $this->request->data['CourseRegistration'][1]['semester'],
                        $latest_academic_year,
                        $this->request->data['CourseRegistration'][1]['student_id']
                    );

                    if (!$not_registered) {
                        //Save course registration.

                        // check for maximum allowed credit/ECTS from General Settings
                        //$overMaximumCreditAllowed = $this->CourseRegistration->Student->checkAllowedMaxCreditLoadPerSemester($this->request->data['CourseRegistration'][1]['student_id']);
                        //debug($overMaximumCreditAllowed);

                        if (!empty($this->request->data['CourseRegistration']) && isset($this->request->data['registerCourses'])) {
                            foreach ($this->request->data['CourseRegistration'] as $eek => &$eev) {
                                if ($eek > 0) {
                                    /* if (!isset($eev['gp'])) {
										//
									} else if ($eev['gp'] == 0) {
										unset($this->request->data['CourseRegistration'][$eek]);
									} */

                                    if (isset($eev['elective_course']) && !empty($eev['elective_course']) && $eev['elective_course'] == 1 && isset($eev['gp']) && $eev['gp'] == 0) {
                                        // remove not selected elective courses;
                                        //debug($this->request->data['CourseRegistration'][$eek]);
                                        unset($this->request->data['CourseRegistration'][$eek]);
                                        continue;
                                    } else {
                                        if (isset($eev['gp']) && empty($eev['gp'])) {
                                            unset($this->request->data['CourseRegistration'][$eek]);
                                            continue;
                                        } else {
                                            if (empty($eev['published_course_id']) || empty($eev['student_id'])) {
                                                // remove entries without published_id or student_id
                                                unset($this->request->data['CourseRegistration'][$eek]);
                                                continue;
                                            }
                                        }
                                    }

                                    if (empty($eev['year_level_id'])) {
                                        $eev['year_level_id'] = null;
                                        //$this->request->data['CourseRegistration'][$eek]['year_level_id'] = NULL;
                                    }
                                }
                                $this->request->data['CourseRegistration'][$eek]['cafeteria_consumer'] = $this->request->data['CourseRegistration'][0]['cafeteria_consumer'];
                            }

                            unset($this->request->data['CourseRegistration'][0]); // cafe non cafe field
                            debug($this->request->data['CourseRegistration']);

                            //exit();

                            if (!empty($this->request->data['CourseRegistration'])) {
                                if ($this->CourseRegistration->saveAll(
                                    $this->request->data['CourseRegistration'],
                                    array('validate' => false)
                                )) {
                                    $registered_courses_count = count($this->request->data['CourseRegistration']);
                                    $this->Flash->success(
                                        'You have successfully registered ' . ($registered_courses_count == 1 ? '1 course' : $registered_courses_count . ' courses') . ' for ' . ($latestSemester == 'I' ? '1st' : ($latestSemester == 'II' ? '2nd' : '3rd')) . ' semester of ' . $latest_academic_year . ' academic year.'
                                    );
                                    $this->redirect(
                                        array(
                                            'action' => 'index',
                                            (str_replace('/', '-', $latest_academic_year)),
                                            $latestSemester
                                        )
                                    );
                                }
                            } else {
                                $this->Flash->error(
                                    'Please select the courses you want to register for ' . ($latestSemester == 'I' ? '1st' : ($latestSemester == 'II' ? '2nd' : '3rd')) . ' semester of ' . $latest_academic_year . ' academic year.'
                                );
                            }
                        }
                    } else {
                        $this->Flash->error(
                            'You have already registered for ' . ($latestSemester == 'I' ? '1st' : ($latestSemester == 'II' ? '2nd' : '3rd')) . ' semester of ' . $latest_academic_year . ' academic year.'
                        );
                        $this->redirect(
                            array('action' => 'index', (str_replace('/', '-', $latest_academic_year)), $latestSemester)
                        );
                    }
                }

                if (!empty($student_section)) {
                    if (count($student_section['Section']) > 0) {
                        if (empty($student_section['Student']['department_id'])) {
                            $published_courses = $this->CourseRegistration->PublishedCourse->find('all', array(
                                'conditions' => array(
                                    'PublishedCourse.department_id is null',
                                    'PublishedCourse.section_id' => $student_section['Section'][0]['id'],
                                    //'PublishedCourse.year_level_id' => 0,
                                    'OR' => array(
                                        'PublishedCourse.year_level_id = 0',
                                        'PublishedCourse.year_level_id = ""',
                                        'PublishedCourse.year_level_id IS NULL'
                                    ),
                                    'PublishedCourse.add' => 0,
                                    'PublishedCourse.drop' => 0,
                                    'PublishedCourse.academic_year LIKE ' => $latest_academic_year . '%',
                                    'PublishedCourse.semester' => $latestSemester,
                                    'PublishedCourse.college_id' => $student_section['Student']['college_id'],
                                ),
                                'contain' => array(
                                    'Course' => array(
                                        'Prerequisite' => array(
                                            'id',
                                            'prerequisite_course_id',
                                            'co_requisite'
                                        ),
                                        'Curriculum' => array('id', 'name', 'type_credit', 'year_introduced', 'active'),
                                        'fields' => array(
                                            'Course.id',
                                            'Course.course_code',
                                            'Course.course_title',
                                            'Course.lecture_hours',
                                            'Course.tutorial_hours',
                                            'Course.laboratory_hours',
                                            'Course.credit'
                                        )
                                    )
                                )
                            ));
                        } else {
                            $published_courses = $this->CourseRegistration->PublishedCourse->find('all', array(
                                'conditions' => array(
                                    'PublishedCourse.department_id' => $this->department_id,
                                    'PublishedCourse.section_id' => $student_section['Section'][0]['id'],
                                    'PublishedCourse.year_level_id' => $student_section['Section'][0]['year_level_id'],
                                    'PublishedCourse.add' => 0,
                                    'PublishedCourse.drop' => 0,
                                    'PublishedCourse.academic_year LIKE ' => $latest_academic_year . '%',
                                    'PublishedCourse.semester' => $latestSemester
                                ),
                                'contain' => array(
                                    'Course' => array(
                                        'Prerequisite' => array(
                                            'id',
                                            'prerequisite_course_id',
                                            'co_requisite'
                                        ),
                                        'Curriculum' => array('id', 'name', 'type_credit', 'year_introduced', 'active'),
                                        'fields' => array(
                                            'Course.id',
                                            'Course.course_code',
                                            'Course.course_title',
                                            'Course.lecture_hours',
                                            'Course.tutorial_hours',
                                            'Course.laboratory_hours',
                                            'Course.credit'
                                        )
                                    )
                                )
                            ));
                        }

                        debug($published_courses);

                        $published_courses = $this->CourseRegistration->getRegistrationType(
                            $published_courses,
                            $this->student_id,
                            $get_student_acadamic_status
                        );
                        $previous_status_semester = $this->CourseRegistration->Student->StudentExamStatus->getPreviousSemester(
                            $latest_academic_year,
                            $latestSemester
                        );
                        $latest_status_year_semester = $this->CourseRegistration->Student->StudentExamStatus->studentYearAndSemesterLevelOfStatusDisplay(
                            $this->student_id,
                            $latest_academic_year,
                            $previous_status_semester['semester']
                        );
                        $student_section_exam_status = $this->CourseRegistration->Student->get_student_section(
                            $this->student_id,
                            $latest_academic_year,
                            $latest_status_year_semester['semester']
                        );
                        $this->set(compact('published_courses', 'student_section', 'student_section_exam_status'));
                    }
                }
            } else {
                if ($passed_or_failed == 2) {
                    $this->Flash->info(
                        'Your academic status for the previous semester is not yet determined due to incomplete grade submission of registered courses. For now, you can not register for ' . ($latestSemester == 'I' ? '1st' : ($latestSemester == 'II' ? '2nd' : '3rd')) . ' semester of ' . $latest_academic_year . ' academic year. Please come back later and check!'
                    );
                } else {
                    if ($passed_or_failed == 4) {
                        $this->Flash->info(
                            'Your academic status for the previous semester is dismissed. You can not register for ' . ($latestSemester == 'I' ? '1st' : ($latestSemester == 'II' ? '2nd' : '3rd')) . ' semester of ' . $latest_academic_year . ' academic year. If you fulfill the requirements for readmission,  don\'t forget to apply to be readmitted for the next academic year.'
                        );
                    } else {
                        if ($isThereFxInPrevAcademicStatus == 0) {
                            $this->Flash->info(
                                'You have invalid grade(Fx, or NG) in your last registration, please fix those grade problems first and come back for ' . ($latestSemester == 'I' ? '1st' : ($latestSemester == 'II' ? '2nd' : '3rd')) . ' semester of ' . $latest_academic_year . ' academic year registration.'
                            );
                        } else {
                            if ($paymentRequired == 0) {
                                $this->Flash->info(
                                    'Payment is required for registration for ' . ($latestSemester == 'I' ? '1st' : ($latestSemester == 'II' ? '2nd' : '3rd')) . ' semester of ' . $latest_academic_year . ' academic year. Please communicate with registrar about the issues.'
                                );
                            }
                        }
                    }
                }

                $this->redirect(
                    array('action' => 'index', (str_replace('/', '-', $latest_academic_year)), $latestSemester)
                );
            }

            if (empty($published_courses)) {
                $this->Flash->info(
                    'There is no published course for ' . ($latestSemester == 'I' ? '1st' : ($latestSemester == 'II' ? '2nd' : '3rd')) . ' semester of ' . $latest_academic_year . ' academic year that requires your registration for now. You can check it later.'
                );
                $this->redirect(
                    array('action' => 'index', (str_replace('/', '-', $latest_academic_year)), $latestSemester)
                );
            }
        } else {
            $this->Flash->info('You need to login or must have a student role to get registered for courses');
            $this->redirect('/');
        }
    }

    public function _student_list_not_registred($data = null)
    {

        $options = array();
        $options['fields'] = array('PublishedCourse.id');
        $search_conditions = array();

        //$search_conditions['conditions'][] = array('Student.id NOT IN (select student_id from graduate_lists)');
        $search_conditions['conditions'][] = array('Student.graduated = 0)');

        $search_conditions['fields'] = array('Student.id', 'Student.studentnumber', 'Student.full_name');
        $search_conditions['limit'] = 20;
        $search_conditions['order'] = array('Student.full_name');

        $search_conditions['contain'] = array(
            'Section' => array('id', 'year_level_id'),
            'StudentsSection.archive = 0',
            'Program' => array('fields' => array('id', 'name')),
            'ProgramType' => array('fields' => array('id', 'name')),
            'Department' => array('fields' => array('id', 'name'))
        );

        $organized_students = array();
        $published_course_ids = array();

        if (isset($data['Student']['academicyear'])) {
            $latest_semester_academic_year = $this->CourseRegistration->latest_academic_year_semester(
                $data['Student']['academicyear']
            );
        } else {
            $latest_semester_academic_year = $this->CourseRegistration->latest_academic_year_semester(
                $this->AcademicYear->current_academicyear()
            );
        }

        if (!empty($latest_semester_academic_year)) {
            $options['conditions'][] = array(
                'PublishedCourse.academic_year like ' => $latest_semester_academic_year['academic_year'] . '%',
                'PublishedCourse.add' => 0
            );

            if (!empty($data['Student']['department_id'])) {
                $options['conditions'][] = array('PublishedCourse.department_id' => $data['Student']['department_id']);
            }

            if (empty($data['Student']['department_id']) || empty($data['Student']['college_id'])) {
                if (!empty($this->department_ids)) {
                    $options['conditions'][] = array('PublishedCourse.department_id' => $this->department_ids);
                } else {
                    if (!empty($this->college_ids)) {
                        $options['conditions'][] = array('PublishedCourse.college_id' => $this->college_ids);
                        //debug($this->college_ids);
                    }
                }
            }

            if (!empty($data['Student']['program_id'])) {
                $options['conditions'][] = array('PublishedCourse.program_id' => $data['Student']['program_id']);
            }

            if (!empty($data['Student']['program_type_id'])) {
                $options['conditions'][] = array('PublishedCourse.program_type_id' => $data['Student']['program_type_id']);
            }

            if (!empty($data['Student']['semester'])) {
                $options['conditions'][] = array('PublishedCourse.semester' => $data['Student']['semester']);
                $this->request->data['Student']['semester'] = $data['Student']['semester'];
            }

            $published_course_ids = $this->CourseRegistration->PublishedCourse->find('list', $options);

            if (empty($published_course_ids)) {
                return array();
            }

            // why the following $options variable is set to array(); ? it resets all conditions set above to null without any reason?? I commented it out
            //$options = array();
        }

        if (!empty($data) && !empty($published_course_ids)) {
            if (!empty($data['Student']['program_id'])) {
                $search_conditions['conditions'][] = array('Student.program_id' => $data['Student']['program_id']);
            }

            if (!empty($data['Student']['program_type_id'])) {
                $search_conditions['conditions'][] = array('Student.program_type_id' => $data['Student']['program_type_id']);
            }

            if (!empty($data['Student']['department_id'])) {
                $department_ids = $this->_givenPublisheCourseReturnDept($published_course_ids);
                if (in_array($data['Student']['department_id'], $department_ids['dept'])) {
                    $search_conditions['conditions'][] = array('Student.department_id' => $data['Student']['department_id']);
                }
            }

            if (!empty($data['Student']['college_id'])) {
                $search_conditions['conditions'][] = array('Student.college_id' => $data['Student']['college_id']);
                $search_conditions['conditions'][] = array('Student.department_id is null');
            }

            if (!empty($data['Student']['studentnumber'])) {
                $search_conditions['conditions'][] = array(
                    'Student.studentnumber like ' => trim(
                        $data['Student']['studentnumber']
                    )
                );
            }

            if (!empty($this->department_ids) && empty($data['Student']['department_id'])) {
                $search_conditions['conditions'][] = array('Student.department_id' => $this->department_ids);
            } else {
                if (!empty($this->college_ids) && empty($data['Student']['college_id'])) {
                    $college_ids = $this->_givenPublisheCourseReturnDept($published_course_ids);
                    $search_conditions['conditions'][] = array(
                        'Student.college_id' => $college_ids['college'],
                        'Student.department_id is null'
                    );
                }
            }
        } else {
            if (!empty($this->department_ids)) {
                $department_ids = $this->_givenPublisheCourseReturnDept($published_course_ids);
                $search_conditions['conditions'][] = array('Student.department_id' => $department_ids['dept']);
            } else {
                if (!empty($this->college_ids)) {
                    $college_ids = $this->_givenPublisheCourseReturnDept($published_course_ids);
                    $search_conditions['conditions'][] = array('Student.department_id is null');
                    $search_conditions['conditions'][] = array('Student.college_id' => $college_ids['college']);
                }
            }

            /*
            $department_ids = $this->_givenPublisheCourseReturnDept($published_course_ids);
            $conditions['Student.department_id'] = $department_ids;
            */
            //$conditions['Student.department_id'] = $this->department_ids;
        }

        $section_ids = $this->CourseRegistration->PublishedCourse->find(
            'list',
            array('conditions' => array('PublishedCourse.id' => $published_course_ids), 'fields' => 'section_id')
        );
        $sections_students = ClassRegistry::init('StudentsSection')->find(
            'list',
            array('conditions' => array('section_id' => $section_ids, 'archive' => 0), 'fields' => 'student_id')
        );
        $search_conditions['conditions'][] = array('Student.id ' => $sections_students);

        $this->CourseRegistration->Student->bindModel(
            array(
                'hasMany' => array(
                    'StudentsSection' => array(
                        'className' => 'StudentsSection',
                    )
                )
            )
        );

        $students = $this->CourseRegistration->Student->find('all', $search_conditions);

        if (!empty($students)) {
            $students_list_not_registred = array();
            // student by student
            foreach ($students as $id => &$detail) {
                $registred_all_published_course = 0;
                foreach ($published_course_ids as $pidd => $pvv) {
                    $check = $this->CourseRegistration->find('count', array(
                        'conditions' => array(
                            'CourseRegistration.student_id' => $detail['Student']['id'],
                            'CourseRegistration.published_course_id' => $pvv
                        )
                    ));

                    if ($check > 0) {
                        // $students_list_not_registred[]=$detail;
                        $registred_all_published_course++;
                    }
                }
                //unset
                if ($registred_all_published_course > 0) {
                    unset($students[$id]);
                    $registred_all_published_course = 0;
                }
            }
        }

        //organize by program, program type, year_level,and section
        if (!empty($students)) {
            foreach ($students as $student_key => $student_value) {
                if (!empty($student_value['StudentsSection']) && count($student_value['StudentsSection']) > 0) {
                    $year_level_found = null;
                    foreach ($student_value['Section'] as $sect_index => $sect_value) {
                        if ($student_value['StudentsSection'][0]['section_id'] == $sect_value['id']) {
                            $year_level_found = $sect_value['year_level_id'];
                        }
                    }
                    $organized_students[$student_value['Program']['name']][$student_value['ProgramType']['name']][$year_level_found][$student_value['StudentsSection'][0]['section_id']][] = $student_value;
                }
            }
            return $organized_students;
        }

        return $organized_students;
        //return $students;
    }

    /*
	function maintain_registration($student_id = null, $register_selected_section = null)
	{
		//read from session selected academic year
		$academicYearSelected = $this->Session->read('search_data_registration');

		if (isset($academicYearSelected)) {
			$latest_academic_year = $academicYearSelected['academicyear'];
			// $this->request->data['Student'] = $academicYearSelected;
		} else {
			if (isset($this->request->data['Student']['academicyear'])) {
				$latest_academic_year = $this->request->data['Student']['academicyear'];
				$this->request->data['continue'] = true;
			} else {
				$latest_academic_year = $this->AcademicYear->current_academicyear();
			}
		}

		if ($student_id == 0 && !empty($register_selected_section)) {

			debug($academicYearSelected);
		}

		$breaker_detail = ClassRegistry::init('User')->find('first', array(
			'conditions' => array(
				'User.id' => $this->Auth->user('id')
			),
			'contain' => array(
				'Staff',
				'Student'
			)
		));

		if ($student_id) {
			$this->request->data['Student']['studentnumber'] = $this->CourseRegistration->Student->field('Student.studentnumber', array('Student.id' => $student_id));
			$this->request->data['Student']['academicyear'] = $latest_academic_year;

			if (!empty($this->department_ids)) {
				$elegible_registrar_responsibility = $this->CourseRegistration->Student->find('count', array('conditions' => array('Student.id' => $student_id, 'Student.department_id' => $this->department_ids)));
			} else if (!empty($this->college_ids)) {
				$elegible_registrar_responsibility = $this->CourseRegistration->Student->find('count', array('conditions' => array('Student.id' => $student_id, 'Student.college_id' => $this->college_ids, 'Student.department_id is null'), 'contain' => array()));
			} else if (!empty($this->department_id)) {
				$elegible_registrar_responsibility = $this->CourseRegistration->Student->find('count', array('conditions' => array('Student.id' => $student_id, 'Student.department_id' => $this->department_id)));
			} else if (!empty($this->college_id)) {
				$elegible_registrar_responsibility = $this->CourseRegistration->Student->find('count', array('conditions' => array('Student.id' => $student_id, 'Student.college_id' => $this->college_id)));
			}

			if ($elegible_registrar_responsibility == 0) {
				$this->Flash->error('You do not have the privilage to register the selected student. Your action is logged and reported to the system administrators.');
				$details = null;
				if (isset($breaker_detail['Staff']) && !empty($breaker_detail['Staff'])) {
					$details .= $breaker_detail['Staff'][0]['first_name'] . ' ' . $breaker_detail['Staff'][0]['middle_name'] . ' ' . $breaker_detail['Staff'][0]['last_name'] . ' (' . $breaker_detail['User']['username'] . ')';
				} else if (isset($breaker_detail['Student']) && !empty($breaker_detail['Student'])) {
					$details .= $breaker_detail['Student'][0]['first_name'] . ' ' . $breaker_detail['Student'][0]['middle_name'] . ' ' . $breaker_detail['Student'][0]['last_name'] . ' (' . $breaker_detail['User']['username'] . ')';
				}
				ClassRegistry::init('AutoMessage')->sendPermissionManagementBreakAttempt(Configure::read('User.user'), '<u>' . $details . '</u> is trying to register students without assigned privilage. Please give appropriate warning.');
				$this->request->data['Student']['studentnumber'] = null;
			} else {
				$this->request->data['continue'] = true;
			}
		}

		// The system asks the user to enter student identification number OR to make selection for step number
		if (!empty($this->request->data) && isset($this->request->data['continue'])) {
			$this->__init_maintain_academic_year();
			$students = $this->_student_list_not_registred($this->request->data);

			if (empty($students) && $student_id == "") {
				if (!empty($this->request->data)) {
					$this->Flash->info('There is no result in the given criteria that needs course registration maintaince for ' . $this->request->data['Student']['academicyear'] . ' Academic Year.');
				} else {
					$this->Flash->info('There is no result in the given criteria that needs course registration maintaince for ' . $this->AcademicYear->current_academicyear() . ' Academic Year.');
				}
				//$students=$this->_student_list_not_registred();
			}

			if (!empty($this->request->data['Student']['studentnumber'])) {
				//$latest_academic_year=$this->AcademicYear->current_academicyear();
				//debug($this->Session->read('search_data_registration'));
				$stud_id = $this->CourseRegistration->Student->field('Student.id', array('Student.studentnumber like ' => trim($this->request->data['Student']['studentnumber'])));
				$latestAcSemester = $this->CourseRegistration->getLastestStudentSemesterAndAcademicYear($stud_id, $latest_academic_year);
				$latestSemester = $latestAcSemester['semester'];
				$student_section = $this->CourseRegistration->Student->student_academic_detail($stud_id, $latest_academic_year);
				$published_courses = $this->CourseRegistration->registerSingleStudent($stud_id, $latest_academic_year);

				if ($published_courses['passed'] === false) {
					$this->Flash->info('Student academic status is dismissed you can not register for semester ' . $latestSemester . '/' . $latest_academic_year . '.');
					$dismissed = true;
					$this->set(compact('dismissed'));
				}

				$previous_status_semester = $this->CourseRegistration->Student->StudentExamStatus->getPreviousSemester($latest_academic_year, $latestSemester);
				$latest_status_year_semester = $this->CourseRegistration->Student->StudentExamStatus->studentYearAndSemesterLevelOfStatusDisplay($stud_id, $latest_academic_year, $previous_status_semester['semester']);
				$student_section_exam_status = $this->CourseRegistration->Student->get_student_section($stud_id, $latest_academic_year, $latest_status_year_semester['semester']);
				$published_courses = $published_courses['register'];

				if (empty($published_courses)) {
					$this->Flash->error('There is no courses publisehd for the selected students for the current academic year, or you do not have the privilage to register this students. Please contact his/her department.');
				}

				$this->set('hide_search', true);
				$this->set(compact(
					'published_courses',
					'student_section',
					'year_level_name',
					'student_section_exam_status'
				));
			}
			$this->set(compact('students'));
		}

		if (!empty($this->request->data) && isset($this->request->data['register'])) {
			//check students has already registered
			// debug($this->Session->read('search_data_registration'));
			$semester = $this->request->data['CourseRegistration'][1]['semester'];
			$not_registered = $this->CourseRegistration->alreadyRegistred($this->request->data['CourseRegistration'][1]['semester'], $latest_academic_year, $this->request->data['CourseRegistration'][1]['student_id']);

			if ($not_registered == 0) {
				//Save course registration.
				if (!empty($this->request->data['CourseRegistration'])) {
					if ($this->CourseRegistration->saveAll($this->request->data['CourseRegistration'], array('validate' => false))) {
						foreach ($this->request->data['CourseRegistration'] as $nn => $namevalue) {
							$student_id = $namevalue['student_id'];
							break;
						}
						$student_name = $this->CourseRegistration->Student->field('full_name', array('Student.id' => $student_id));
						$this->Flash->success('You have successfully registered ' . $student_name . ' for ' . $latest_academic_year . ' of  semester ' . $semester . '');
						unset($this->request->data['Student']['studentnumber']);
						$this->__init_maintain_academic_year();
						//$this->redirect(array('action'=>'maintain_registration'));
					}
					//debug($this->CourseRegistration->invalidFields());
				}
			} else {
				$this->Flash->error('The student has already registered for ' . $this->AcademicYear->current_academicyear() . ' academic year of  semester ' . $semester . '');
				//$this->redirect(array('action'=>'maintain_registration'));
			}
		}


		if (empty($this->request->data)) {
			// $students=$this->_student_list_not_registred();
			// $this->set(compact('students'));
		}
		if ($this->role_id == ROLE_REGISTRAR) {
			$yearLevels = $this->CourseRegistration->YearLevel->distinct_year_level();
			$programs = $this->CourseRegistration->Student->Program->find('list', array('conditions' => array('Program.id' => $this->program_id)));
			$departments = $this->CourseRegistration->Student->Department->find('list', array('conditions' => array('Department.id' => $this->department_ids)));
			$programTypes = $this->CourseRegistration->Student->ProgramType->find('list');
			$this->set(compact('departments', 'yearLevels', 'programs', 'programTypes'));
		}

		$latest_semester_academic_year = $this->CourseRegistration->latest_academic_year_semester($this->AcademicYear->current_academicyear());

		//debug($this->AcademicYear->current_academicyear());
		if (!empty($this->department_ids)) {
			$departments = $this->CourseRegistration->Student->Department->find('list', array('conditions' => array('Department.id' => $this->department_ids)));
			$sections = $this->CourseRegistration->PublishedCourse->Section->find('list', array('conditions' => array('Section.department_id' => $this->department_ids)));
			$yearLevels = $this->CourseRegistration->PublishedCourse->YearLevel->find('list', array('conditions' => array('YearLevel.department_id' => $this->department_ids)));
		} else if (!empty($this->college_ids)) {
			$colleges = $this->CourseRegistration->Student->College->find('list', array('conditions' => array('College.id' => $this->college_ids)));
			$sections = $this->CourseRegistration->PublishedCourse->Section->find('list', array('conditions' => array('Section.college_id' => $this->college_ids)));
		} else if (!empty($this->department_id)) {
			$departments = $this->CourseRegistration->Student->Department->find('list', array('conditions' => array('Department.id' => $this->department_id)));
			$sections = $this->CourseRegistration->PublishedCourse->Section->find('list', array('conditions' => array('Section.department_id' => $this->department_id)));
			$yearLevels = $this->CourseRegistration->PublishedCourse->YearLevel->find('list', array('conditions' => array('YearLevel.department_id' => $this->department_id)));
			$programs = $this->CourseRegistration->Student->Program->find('list');
			$programTypes = $this->CourseRegistration->Student->ProgramType->find('list');
			$this->set(compact('programs', 'programTypes'));
		} else if (!empty($this->college_id)) {
			$colleges = $this->CourseRegistration->Student->College->find('list', array('conditions' => array('College.id' => $this->college_id)));
			$sections = $this->CourseRegistration->PublishedCourse->Section->find('list', array('conditions' => array('Section.college_id' => $this->college_id)));
			$programs = $this->CourseRegistration->Student->Program->find('list');
			$programTypes = $this->CourseRegistration->Student->ProgramType->find('list');
			$this->set(compact('programs', 'programTypes'));
		}

		$this->set(compact('departments', 'colleges', 'latest_semester_academic_year', 'sections', 'yearLevels'));
	}
	*/

    public function maintain_registration($student_id = null, $register_selected_section = null)
    {

        $this->__register_student($student_id, $register_selected_section);
    }

    private function __register_student($student_id = null, $register_selected_section = null)
    {

        //read from session selected academic year
        //$academicYearSelected = $this->Session->read('search_data_registration');
        //debug($academicYearSelected);

        /* if (!empty($academicYearSelected)) {
			unset($this->request->data['CourseRegistration']);
			$this->request->data['continue'] = true;
		}

		if (!empty($academicYearSelected)) {
			if (!empty($academicYearSelected['academic_year'])) {
				$latest_academic_year = $academicYearSelected['academic_year'];
			} else if (isset($academicYearSelected['academicyear'])) {
				$latest_academic_year = $academicYearSelected['academicyear'];
			}
		} else {
			if (!empty($this->request->data['Student']['academicyear'])) {
				$latest_academic_year = $this->request->data['Student']['academicyear'];
				$this->request->data['continue'] = true;
			} else {
				$latest_academic_year = $this->AcademicYear->current_academicyear();
			}
		} */


        if (isset($this->request->data['Student']) && !empty($this->request->data['Student'])) {
            debug($this->request->data['Student']);
        }

        $current_acy_and_semester = $this->AcademicYear->current_acy_and_semester();
        $latest_academic_year = $academicYearSelected = $current_acy_and_semester['academic_year'];


        if (!empty($this->request->data['Student'])) {
            $academicYearSelected = $this->request->data['Student']['academicyear'];
            $latest_academic_year = $this->request->data['Student']['academicyear'];

            if (empty($this->request->data['Student']['semester'])) {
                $this->request->data['Student']['semester'] = $current_acy_and_semester['semester'];
            }
        } else {
            if (!empty($this->request->data['CourseRegistration'])) {
                debug($this->request->data['CourseRegistration']);
                $academicYearSelected = $this->request->data['CourseRegistration'][1]['academic_year'];
                $latest_academic_year = $this->request->data['CourseRegistration'][1]['academic_year'];
            }
        }

        debug($academicYearSelected);

        if (empty($student_id) && !empty($register_selected_section)) {
            //check elegibility
            $this->request->data['Student']['academicyear'] = $academicYearSelected;
            $this->request->data['continue'] = true;
            debug($this->request->data['Student']);

            $students_list = $this->CourseRegistration->Section->getSectionActiveStudentsId($register_selected_section);

            if (!empty($this->department_ids)) {
                $elegible_registrar_responsibility = $this->CourseRegistration->Student->find('count', array(
                    'conditions' => array(
                        'Student.id' => $students_list,
                        'Student.department_id' => $this->department_ids,
                        'Student.program_type_id' => $this->program_type_ids,
                        'Student.program_id' => $this->program_ids
                    )
                ));
            } else {
                if (!empty($this->college_ids)) {
                    $elegible_registrar_responsibility = $this->CourseRegistration->Student->find('count', array(
                        'conditions' => array(
                            'Student.id' => $students_list,
                            'Student.college_id' => $this->college_ids,
                            'Student.program_type_id' => $this->program_type_ids,
                            'Student.program_id' => $this->program_ids,
                            'Student.department_id is null'
                        ),
                        'contain' => array()
                    ));
                } else {
                    if (!empty($this->department_id)) {
                        $elegible_registrar_responsibility = $this->CourseRegistration->Student->find('count', array(
                            'conditions' => array(
                                'Student.id' => $students_list,
                                'Student.department_id' => $this->department_id
                            )
                        ));
                    } else {
                        if (!empty($this->college_id)) {
                            $elegible_registrar_responsibility = $this->CourseRegistration->Student->find(
                                'count',
                                array(
                                    'conditions' => array(
                                        'Student.id' => $students_list,
                                        'Student.college_id' => $this->college_id
                                    )
                                )
                            );
                        }
                    }
                }
            }

            if ($elegible_registrar_responsibility == 0) {
                $this->Flash->error(
                    'You do not have the privilage to register the selected student. Your action is logged and reported to the system administrators.'
                );
                $details = null;

                $breaker_detail = ClassRegistry::init('User')->find(
                    'first',
                    array(
                        'conditions' => array('User.id' => $this->Auth->user('id')),
                        'contain' => array('Staff', 'Student')
                    )
                );

                if (isset($breaker_detail['Staff']) && !empty($breaker_detail['Staff'])) {
                    $details .= $breaker_detail['Staff'][0]['first_name'] . ' ' . $breaker_detail['Staff'][0]['middle_name'] . ' ' . $breaker_detail['Staff'][0]['last_name'] . ' (' . $breaker_detail['User']['username'] . ')';
                } else {
                    if (isset($breaker_detail['Student']) && !empty($breaker_detail['Student'])) {
                        $details .= $breaker_detail['Student'][0]['first_name'] . ' ' . $breaker_detail['Student'][0]['middle_name'] . ' ' . $breaker_detail['Student'][0]['last_name'] . ' (' . $breaker_detail['User']['username'] . ')';
                    }
                }
                ClassRegistry::init('AutoMessage')->sendPermissionManagementBreakAttempt(
                    Configure::read('User.user'),
                    '<u>' . $details . '</u> is trying to register students without assigned privilage. Please give appropriate warning.'
                );
            } else {
                $isRegistered = $this->CourseRegistration->massRegisterStudent(
                    $register_selected_section,
                    $academicYearSelected
                );

                if ($isRegistered == 1) {
                    $this->Flash->success(
                        'All students(who are not dismissed and fullfield prerequiste) in the selected section are registered successfully for non elective courses for selected academic year and semester. You can view all course registrations using "Course Registration View" option or manintain elective courses separately if any, on manage missing registration on each student academic profile.'
                    );
                } else {
                    if ($isRegistered == 3) {
                        $this->Flash->info(
                            'Some of the students in the selected section are not elegible for registration.'
                        );
                    }
                }
            }
        }

        if ($student_id) {
            $this->request->data['Student']['studentnumber'] = $this->CourseRegistration->Student->field(
                'Student.studentnumber',
                array('Student.id' => $student_id)
            );

            if (isset($latest_academic_year)) {
                $this->request->data['Student']['academicyear'] = $latest_academic_year;
            }

            if (!empty($this->department_ids)) {
                $elegible_registrar_responsibility = $this->CourseRegistration->Student->find('count', array(
                    'conditions' => array(
                        'Student.id' => $student_id,
                        'Student.department_id' => $this->department_ids,
                        'Student.program_type_id' => $this->program_type_ids,
                        'Student.program_id' => $this->program_ids,
                    )
                ));
            } else {
                if (!empty($this->college_ids)) {
                    $elegible_registrar_responsibility = $this->CourseRegistration->Student->find('count', array(
                        'conditions' => array(
                            'Student.id' => $student_id,
                            'Student.college_id' => $this->college_ids,
                            'Student.program_type_id' => $this->program_type_ids,
                            'Student.program_id' => $this->program_ids,
                            'Student.department_id is null'
                        ),
                        'contain' => array()
                    ));
                } else {
                    if (!empty($this->department_id)) {
                        $elegible_registrar_responsibility = $this->CourseRegistration->Student->find('count', array(
                            'conditions' => array(
                                'Student.id' => $student_id,
                                'Student.department_id' => $this->department_id
                            )
                        ));
                    } else {
                        if (!empty($this->college_id)) {
                            $elegible_registrar_responsibility = $this->CourseRegistration->Student->find(
                                'count',
                                array(
                                    'conditions' => array(
                                        'Student.id' => $student_id,
                                        'Student.college_id' => $this->college_id
                                    )
                                )
                            );
                        }
                    }
                }
            }

            if ($elegible_registrar_responsibility == 0) {
                $this->Flash->error(
                    'You do not have the privilage to register the selected student. Your action is logged and reported to the system administrators.'
                );
                $details = null;
                $breaker_detail = ClassRegistry::init('User')->find(
                    'first',
                    array(
                        'conditions' => array('User.id' => $this->Auth->user('id')),
                        'contain' => array('Staff', 'Student')
                    )
                );

                if (isset($breaker_detail['Staff']) && !empty($breaker_detail['Staff'])) {
                    $details .= $breaker_detail['Staff'][0]['first_name'] . ' ' . $breaker_detail['Staff'][0]['middle_name'] . ' ' . $breaker_detail['Staff'][0]['last_name'] . ' (' . $breaker_detail['User']['username'] . ')';
                } else {
                    if (isset($breaker_detail['Student']) && !empty($breaker_detail['Student'])) {
                        $details .= $breaker_detail['Student'][0]['first_name'] . ' ' . $breaker_detail['Student'][0]['middle_name'] . ' ' . $breaker_detail['Student'][0]['last_name'] . ' (' . $breaker_detail['User']['username'] . ')';
                    }
                }
                ClassRegistry::init('AutoMessage')->sendPermissionManagementBreakAttempt(
                    Configure::read('User.user'),
                    '<u>' . $details . '</u> is trying to register students without assigned privilage. Please give appropriate warning.'
                );
                $this->request->data['Student']['studentnumber'] = null;
            } else {
                $this->request->data['continue'] = true;
            }
        }

        $buttonClicked = false;
        $buttonIndex = '';

        if (isset($this->request->data['CourseRegistration']['register_count'])) {
            for ($i = 0; $i <= $this->request->data['CourseRegistration']['register_count']; $i++) {
                if (isset($this->request->data['registerSelected_' . $i . ''])) {
                    $buttonClicked = true;
                    $buttonIndex = $i;
                    break;
                }
            }
        }

        debug($buttonIndex);

        //Register Selected students
        if (isset($this->request->data) && isset($this->request->data['CourseRegistration']) && $buttonClicked) {
            if (isset($this->request->data['registerSelected_' . $buttonIndex])) {
                unset($this->request->data['CourseRegistration']['register_count']);
                $studentLists = array();
                $regCount = 0;
                $studCount = 0;

                if (!empty($this->request->data['CourseRegistration'])) {
                    debug($this->request->data['CourseRegistration']);
                    //debug($this->request->data);
                    unset($this->request->data['CourseRegistration']['select_all']);
                    foreach ($this->request->data['CourseRegistration'] as $key => $data) {
                        if (is_numeric($key)) {
                            if (isset($data['ggp']) && ($data['ggp'] == '1' || $data['ggp'] == 1)) {
                                $notRegistered = $this->CourseRegistration->alreadyRegistred(
                                    $this->request->data['Student']['semester'],
                                    $this->request->data['Student']['academicyear'],
                                    $data['student_id']
                                );

                                $studCount++;
                                //debug($notRegistered);

                                if (!$notRegistered) {
                                    $publishedCourseLists = $this->CourseRegistration->registerSingleStudent(
                                        $data['student_id'],
                                        $this->request->data['Student']['academicyear'],
                                        $this->request->data['Student']['semester'],
                                        $exclude_elective = 1
                                    );

                                    if ($publishedCourseLists['passed'] == false || $publishedCourseLists['passed'] == 4) {
                                        continue;
                                    }

                                    //debug($publishedCourseLists);

                                    $psL = $this->CourseRegistration->getRegistrationType(
                                        $publishedCourseLists['register'],
                                        $data['student_id']
                                    );

                                    if (!empty($psL)) {
                                        $total_selected_credit = 0;
                                        foreach ($psL as $pl) {
                                            if (!isset($pl['prequisite_taken_passsed']) && !isset($pl['exemption']) || (isset($pl['prequisite_taken_passsed']) && $pl['prequisite_taken_passsed'] == 1)) {
                                                if (isset($pl['PublishedCourse']['id']) && !empty($pl['PublishedCourse']['id'])) {
                                                    //debug($pl['PublishedCourse']);
                                                    debug($pl['PublishedCourse']['course_id']);

                                                    $course_credit = $this->CourseRegistration->PublishedCourse->Course->field(
                                                        'Course.credit',
                                                        array('Course.id' => $pl['PublishedCourse']['course_id'])
                                                    );
                                                    $total_selected_credit += $course_credit;

                                                    debug($course_credit);

                                                    $maxLoad = $this->CourseRegistration->Student->calculateStudentLoad(
                                                        $data['student_id'],
                                                        $pl['PublishedCourse']['semester'],
                                                        $pl['PublishedCourse']['academic_year']
                                                    );
                                                    $allowedMaximum = ClassRegistry::init(
                                                        'AcademicCalendar'
                                                    )->maximumCreditPerSemester($data['student_id']);

                                                    debug($maxLoad);
                                                    debug($allowedMaximum);
                                                    debug($total_selected_credit);

                                                    if (is_numeric($maxLoad) && $maxLoad >= 0) {
                                                        // check max load + current registration < allowed credit for semester
                                                        if ((($maxLoad + $course_credit) <= $allowedMaximum) && (($maxLoad + $total_selected_credit) <= $allowedMaximum)) {
                                                            $studentLists['CourseRegistration'][$regCount]['student_id'] = $data['student_id'];
                                                            $studentLists['CourseRegistration'][$regCount]['semester'] = $pl['PublishedCourse']['semester'];
                                                            $studentLists['CourseRegistration'][$regCount]['academic_year'] = $pl['PublishedCourse']['academic_year'];
                                                            $studentLists['CourseRegistration'][$regCount]['year_level_id'] = $pl['PublishedCourse']['year_level_id'];
                                                            $studentLists['CourseRegistration'][$regCount]['section_id'] = $pl['PublishedCourse']['section_id'];
                                                            $studentLists['CourseRegistration'][$regCount]['published_course_id'] = $pl['PublishedCourse']['id'];
                                                            $regCount++;
                                                        }
                                                    }
                                                }
                                            }
                                        }
                                    }
                                }
                                unset($this->request->data['CourseRegistration'][$key]['ggp']); //will delete student to be processed in case of form resubmission and unckecks the next student to be selected on the page reload.
                            }
                        }
                    }
                }
            }

            if (isset($studentLists['CourseRegistration']) && !empty($studentLists['CourseRegistration'])) {
                if ($this->CourseRegistration->saveAll($studentLists['CourseRegistration'], array('validate' => false)
                )) {
                    $this->Flash->success(
                        'You have successfully registered the selected ' . ($studCount == 1 ? '1 student' : $studCount . ' students') . ' for non elective courses for ' . ($this->request->data['Student']['semester'] == 'I' ? '1st' : ($this->request->data['Student']['semester'] == 'II' ? '2nd' : '3rd')) . ' semester of ' . $this->request->data['Student']['academicyear'] . ' academic year. If there are any elective courses published for the section, mantain them separately on manage missing registration on student academic profile.'
                    );
                    $this->request->data['continue'] = false;
                    unset($this->request->data['CourseRegistration']);
                }
            }
        }

        // The system asks the user to enter student identification number OR to make selection for step number
        if (!empty($this->request->data) && isset($this->request->data['continue'])) {
            // $this->__init_search();
            if (empty($student_id)) {
                $students = $this->CourseRegistration->student_list_not_registred($this->request->data);
            }

            debug($this->request->data);

            if (empty($students) && $student_id == "") {
                if (!empty($this->request->data)) {
                    if (!$this->Session->check('Message.flash')) {
                        if (!empty($this->request->data['Student']['section_id'])) {
                            $sectionName = $this->CourseRegistration->Section->field(
                                'Section.name',
                                array('Section.id' => $this->request->data['Student']['section_id'])
                            );
                            $this->Flash->info(
                                'No result found for ' . $sectionName . ' section that needs course registration maintaince for ' . ($this->request->data['Student']['semester'] == 'I' ? '1st' : ($this->request->data['Student']['semester'] == 'II' ? '2nd' : '3rd')) . ' semester of ' . $this->request->data['Student']['academicyear'] . ' academic year.'
                            );
                        } else {
                            if (empty($this->request->data['Student']['studentnumber'])) {
                                $this->Flash->info(
                                    'No result found in the given criteria that needs course registration maintaince for ' . ($this->request->data['Student']['semester'] == 'I' ? '1st' : ($this->request->data['Student']['semester'] == 'II' ? '2nd' : '3rd')) . ' semester of ' . $this->request->data['Student']['academicyear'] . ' academic year.'
                                );
                            }
                        }
                    }
                } else {
                    $this->Flash->info(
                        'No result found in the given criteria that needs course registration maintaince for ' . ($this->request->data['Student']['semester'] == 'I' ? '1st' : ($this->request->data['Student']['semester'] == 'II' ? '2nd' : '3rd')) . ' semester of ' . $this->request->data['Student']['academicyear'] . ' academic year.'
                    );
                }
            }


            if (!empty($this->request->data['Student']['studentnumber'])) {
                $stud_id = $this->CourseRegistration->Student->field(
                    'Student.id',
                    array('Student.studentnumber like ' => trim($this->request->data['Student']['studentnumber']))
                );

                if (empty($stud_id)) {
                    $this->Flash->error('Student ID not found, Check if you made typo error, correct and try again!');
                    $this->redirect(array('action' => 'maintain_registration'));
                }

                $student_name = $this->CourseRegistration->Student->field('full_name', array('Student.id' => $stud_id));

                if ($this->role_id == ROLE_REGISTRAR && $this->Session->read('Auth.User')['is_admin'] != 1) {
                    if (!empty($this->department_ids)) {
                        $elegible_registrar_responsibility = $this->CourseRegistration->Student->find('count', array(
                            'conditions' => array(
                                'Student.id' => $stud_id,
                                'Student.department_id' => $this->department_ids,
                                'Student.program_type_id' => $this->program_type_ids,
                                'Student.program_id' => $this->program_ids,
                            )
                        ));
                    } else {
                        if (!empty($this->college_ids)) {
                            $elegible_registrar_responsibility = $this->CourseRegistration->Student->find(
                                'count',
                                array(
                                    'conditions' => array(
                                        'Student.id' => $stud_id,
                                        'Student.college_id' => $this->college_ids,
                                        'Student.program_type_id' => $this->program_type_ids,
                                        'Student.program_id' => $this->program_ids,
                                        'Student.department_id is null'
                                    ),
                                    'contain' => array()
                                )
                            );
                        }
                    }

                    if ($elegible_registrar_responsibility == 0) {
                        $this->Flash->error(
                            'You do not have the privilage to register ' . $student_name . ' (' . (trim(
                                $this->request->data['Student']['studentnumber']
                            )) . ')'
                        );
                        unset($this->request->data['CourseRegistration']);
                        $this->redirect(array('action' => 'maintain_registration'));
                    }
                }

                $latestAcSemester = $this->CourseRegistration->getLastestStudentSemesterAndAcademicYear(
                    $stud_id,
                    $latest_academic_year
                );
                $latestSemester = $latestAcSemester['semester'];
                $student_section = $this->CourseRegistration->Student->student_academic_detail(
                    $stud_id,
                    $latest_academic_year
                );

                debug($latestAcSemester);

                // check for maximum allowed credit/ECTS from General Settings
                if (!empty($latestAcSemester)) {
                    $overMaximumCreditAllowed = $this->CourseRegistration->Student->checkAllowedMaxCreditLoadPerSemester(
                        $stud_id,
                        $latestAcSemester['semester'],
                        $latestAcSemester['academic_year']
                    );
                    debug($overMaximumCreditAllowed);
                } else {
                    if (!empty($this->request->data['Student']['semester']) && !empty($this->request->data['Student']['academicyear'])) {
                        $overMaximumCreditAllowed = $this->CourseRegistration->Student->checkAllowedMaxCreditLoadPerSemester(
                            $stud_id,
                            $this->request->data['Student']['semester'],
                            $this->request->data['Student']['academicyear']
                        );
                        debug($overMaximumCreditAllowed);
                    } else {
                        $overMaximumCreditAllowed = $this->CourseRegistration->Student->checkAllowedMaxCreditLoadPerSemester(
                            $stud_id,
                            $this->AcademicYear->current_acy_and_semester()['semester'],
                            $this->AcademicYear->current_acy_and_semester()['academic_year']
                        );
                        debug($overMaximumCreditAllowed);
                    }
                }


                if (isset($latest_academic_year) && !empty($latest_academic_year)) {
                    $published_courses = $this->CourseRegistration->registerSingleStudent(
                        $stud_id,
                        $latest_academic_year
                    );
                } else {
                    if (!empty($this->request->data['Student']['semester'])) {
                        $published_courses = $this->CourseRegistration->registerSingleStudent(
                            $stud_id,
                            $latest_academic_year,
                            $this->request->data['Student']['semester']
                        );
                    }
                } /* else {
					$published_courses = $this->CourseRegistration->registerSingleStudent($stud_id, $latest_academic_year);
				} */

                if ($published_courses['passed'] === false || $published_courses['passed'] == 4) {
                    $this->Flash->info(
                        $student_name . ' (' . (trim(
                            $this->request->data['Student']['studentnumber']
                        )) . ') is dismissed. You can not register the student for semester ' . $latestSemester . '/' . $latest_academic_year . '.'
                    );
                    $dismissed = true;
                    $this->set(compact('dismissed'));
                }

                $previous_status_semester = $this->CourseRegistration->Student->StudentExamStatus->getPreviousSemester(
                    $latest_academic_year,
                    $latestSemester
                );
                $latest_status_year_semester = $this->CourseRegistration->Student->StudentExamStatus->studentYearAndSemesterLevelOfStatusDisplay(
                    $stud_id,
                    $latest_academic_year,
                    $previous_status_semester['semester']
                );
                $student_section_exam_status = $this->CourseRegistration->Student->get_student_section(
                    $stud_id,
                    $latest_academic_year,
                    $latest_status_year_semester['semester']
                );
                $published_courses = $published_courses['register'];

                if (empty($published_courses)) {
                    $this->Flash->warning(
                        'No published course is found for ' . $student_name . ' (' . (trim(
                            $this->request->data['Student']['studentnumber']
                        )) . ') for ' . ($latestSemester == 'I' ? '1st' : ($latestSemester == 'II' ? '2nd' : '3rd')) . ' semester of ' . $latest_academic_year . ' academic year. Please check course registrations for the latest semester on the student academic profile or contact the student department for course publication.'
                    );
                }

                $this->set('hide_search', true);
                debug($published_courses);

                $this->set(
                    compact('published_courses', 'student_section', 'year_level_name', 'student_section_exam_status')
                );
            }
            $this->set(compact('students'));
        }

        if (!empty($this->request->data) && isset($this->request->data['register'])) {
            debug($this->request->data);
            //check students has already registered
            // debug($this->Session->read('search_data_registration'));

            if (!empty($this->request->data['Student']['studentnumber'])) {
                //debug($this->request->data['CourseRegistration'][0]['student_id']);

                if (!empty($this->request->data['CourseRegistration'])) {
                    foreach ($this->request->data['CourseRegistration'] as $key => $value) {
                        if (isset($value['student_id'])) {
                            $studentID = $value['student_id'];
                        }
                    }
                }

                $not_registered = $this->CourseRegistration->alreadyRegistred(
                    $this->request->data['Student']['semester'],
                    $this->request->data['Student']['academicyear'],
                    $studentID
                );
                $semester = $this->request->data['Student']['semester'];
            } else {
                $semester = $this->request->data['CourseRegistration'][1]['semester'];
                $not_registered = $this->CourseRegistration->alreadyRegistred(
                    $this->request->data['CourseRegistration'][1]['semester'],
                    $this->request->data['CourseRegistration'][1]['academic_year'],
                    $this->request->data['CourseRegistration'][1]['student_id']
                );
            }

            debug($not_registered);

            if ($not_registered == 0) {
                //Save course registration.
                if (!empty($this->request->data['CourseRegistration'])) {
                    if (empty($this->request->data['Student']['studentnumber'])) {
                        foreach ($this->request->data['CourseRegistration'] as $eek => &$eev) {
                            /* if (!isset($eev['gp'])) {
								//unset($this->request->data['CourseRegistration'][$eek]);
							} else if ($eev['gp'] == 0) {
								unset($this->request->data['CourseRegistration'][$eek]);
							} */

                            if (isset($eev['elective_course']) && !empty($eev['elective_course']) && $eev['elective_course'] == 1 && isset($eev['gp']) && $eev['gp'] == 0) {
                                // remove not selected elective courses;
                                //debug($this->request->data['CourseRegistration'][$eek]);
                                unset($this->request->data['CourseRegistration'][$eek]);
                                continue;
                            } else {
                                if (isset($eev['gp']) && empty($eev['gp'])) {
                                    unset($this->request->data['CourseRegistration'][$eek]);
                                    continue;
                                } else {
                                    if (empty($eev['published_course_id']) || empty($eev['student_id'])) {
                                        // remove entries without published_id or student_id
                                        unset($this->request->data['CourseRegistration'][$eek]);
                                        continue;
                                    }
                                }
                            }

                            if (empty($eev['year_level_id'])) {
                                $eev['year_level_id'] = null;
                            }

                            $this->request->data['CourseRegistration'][$eek]['cafeteria_consumer'] = $this->request->data['CourseRegistration'][0]['cafeteria_consumer'];
                        }
                        //debug($this->request->data['CourseRegistration']);

                    } else {
                        $cafeteria_consumer = array_pop($this->request->data['CourseRegistration']);
                        debug($cafeteria_consumer['cafeteria_consumer']);

                        $this->request->data['CourseRegistration'] = array_values(
                            $this->request->data['CourseRegistration']
                        );
                        //$this->request->data['CourseRegistration'] = array_combine(range(1, count($this->request->data['CourseRegistration'])), array_values($this->request->data['CourseRegistration']));

                        foreach ($this->request->data['CourseRegistration'] as $key => &$value) {
                            if (isset($value['elective_course']) && !empty($value['elective_course']) && $value['elective_course'] == 1 && isset($value['gp']) && $value['gp'] == 0) {
                                // remove not selected elective courses;
                                //debug($this->request->data['CourseRegistration'][$key]);
                                unset($this->request->data['CourseRegistration'][$key]);
                                continue;
                            } else {
                                if (isset($value['gp']) && empty($value['gp'])) {
                                    unset($this->request->data['CourseRegistration'][$key]);
                                    continue;
                                } else {
                                    if (empty($value['published_course_id']) || empty($value['student_id'])) {
                                        // remove entries without published_id or student_id
                                        unset($this->request->data['CourseRegistration'][$key]);
                                        continue;
                                    }
                                }
                            }

                            if (empty($value['year_level_id'])) {
                                $value['year_level_id'] = null;
                            }

                            $this->request->data['CourseRegistration'][$key]['cafeteria_consumer'] = $cafeteria_consumer['cafeteria_consumer'];
                        }
                        //debug($this->request->data['CourseRegistration']);
                    }

                    if (!empty($this->request->data['CourseRegistration'])) {
                        if ($this->CourseRegistration->saveAll(
                            $this->request->data['CourseRegistration'],
                            array('validate' => false)
                        )) {
                            foreach ($this->request->data['CourseRegistration'] as $nn => $namevalue) {
                                $student_id = $namevalue['student_id'];
                                $sem = $namevalue['semester'];
                                $ac_year = $namevalue['academic_year'];
                                break;
                            }
                            $student_name = $this->CourseRegistration->Student->field(
                                'full_name',
                                array('Student.id' => $student_id)
                            );
                            $student_number = $this->CourseRegistration->Student->field(
                                'studentnumber',
                                array('Student.id' => $student_id)
                            );
                            $this->Flash->success(
                                'You have successfully registered ' . $student_name . ' (' . $student_number . ') for ' . ($sem == 'I' ? '1st' : ($sem == 'II' ? '2nd' : '3rd')) . ' semester of ' . $ac_year . ' academic year.'
                            );
                            unset($this->request->data['Student']['studentnumber']);
                            //$this->__init_search();
                            $this->redirect(array('action' => 'maintain_registration'));
                        }
                    } else {
                        $this->Flash->error(
                            'Please select the courses you want to register for ' . ($latestSemester == 'I' ? '1st' : ($latestSemester == 'II' ? '2nd' : '3rd')) . ' semester of ' . $latest_academic_year . ' academic year.'
                        );
                    }
                }
            } else {
                $this->Flash->error(
                    'The student has already registered for ' . ($semester == 'I' ? '1st' : ($semester == 'II' ? '2nd' : '3rd')) . ' semester of ' . ($this->AcademicYear->current_academicyear(
                    )) . ' academic year.'
                );
            }
        }

        $latest_semester_academic_year = $this->CourseRegistration->latest_academic_year_semester(
            $this->AcademicYear->current_academicyear()
        );
        $yearLevels = $this->year_levels;
        $programs = $this->CourseRegistration->Student->Program->find('list');
        $programTypes = $this->CourseRegistration->Student->ProgramType->find('list');

        debug(key($yearLevels));

        if ($this->role_id == ROLE_REGISTRAR || ROLE_REGISTRAR == $this->Session->read(
                'Auth.User'
            )['Role']['parent_id']) {
            if ($this->Session->read('Auth.User')['is_admin'] == 1) {
                $programs = $this->CourseRegistration->Student->Program->find('list');
                $programTypes = $this->CourseRegistration->Student->ProgramType->find('list');
            } else {
                $programs = $this->CourseRegistration->Student->Program->find('list', array(
                    'conditions' => array(
                        'Program.id' => $this->program_ids
                    )
                ));
                $programTypes = $this->CourseRegistration->Student->ProgramType->find('list', array(
                    'conditions' => array(
                        'ProgramType.id' => $this->program_type_ids
                    )
                ));
            }

            if (!empty($this->department_ids)) {
                $departments = $this->CourseRegistration->Student->Department->find(
                    'list',
                    array('conditions' => array('Department.id' => $this->department_ids, 'Department.active' => 1))
                );

                if (isset($this->request->data['Student']) && !empty($this->request->data['Student'])) {
                    $sections = $this->CourseRegistration->PublishedCourse->Section->find('all', array(
                        'conditions' => array(
                            'Section.department_id' => $this->request->data['Student']['department_id'],
                            'Section.program_type_id' => $this->request->data['Student']['program_type_id'],
                            'Section.program_id' => $this->request->data['Student']['program_id'],
                            'Section.academicyear LIKE ' => $this->request->data['Student']['academicyear'],
                            'YearLevel.name LIKE ' => $this->request->data['Student']['year_level_id'],
                            'OR' => array(
                                'Section.year_level_id IS NOT NULL',
                                'Section.year_level_id <> 0',
                                'Section.year_level_id != ""',
                            ),
                            'Section.archive' => 0,
                        ),
                        'contain' => array(
                            'YearLevel' => array('id', 'name'),
                            'Program' => array('id', 'name'),
                            'ProgramType' => array('id', 'name'),
                            'Department' => array('id', 'name', 'type'),
                            'College' => array('id', 'name', 'type'),
                            'Curriculum' => array('id', 'name', 'type_credit', 'year_introduced', 'active')
                        ),
                        'order' => array(
                            'Section.year_level_id' => 'ASC',
                            'Section.college_id' => 'ASC',
                            'Section.department_id' => 'ASC',
                            'Section.id' => 'ASC',
                            'Section.name' => 'ASC'
                        ),
                        'recursive' => -1
                    ));
                } else {
                    $sections = $this->CourseRegistration->PublishedCourse->Section->find('all', array(
                        'conditions' => array(
                            'Section.department_id' => key($departments),
                            'Section.program_type_id' => key($programTypes),
                            'Section.program_id' => key($programs),
                            'Section.academicyear LIKE ' => $this->AcademicYear->current_academicyear(),
                            'YearLevel.name LIKE ' => key($yearLevels),
                            'OR' => array(
                                'Section.year_level_id IS NOT NULL',
                                'Section.year_level_id <> 0',
                                'Section.year_level_id != ""',
                            ),
                            'Section.archive' => 0,
                        ),
                        'contain' => array(
                            'YearLevel' => array('id', 'name'),
                            'Program' => array('id', 'name'),
                            'ProgramType' => array('id', 'name'),
                            'Department' => array('id', 'name', 'type'),
                            'College' => array('id', 'name', 'type'),
                            'Curriculum' => array('id', 'name', 'type_credit', 'year_introduced', 'active')
                        ),
                        'order' => array(
                            'Section.year_level_id' => 'ASC',
                            'Section.college_id' => 'ASC',
                            'Section.department_id' => 'ASC',
                            'Section.id' => 'ASC',
                            'Section.name' => 'ASC'
                        ),
                        'recursive' => -1
                    ));
                }
            } else {
                if (!empty($this->college_ids)) {
                    $colleges = $this->CourseRegistration->Student->College->find(
                        'list',
                        array('conditions' => array('College.id' => $this->college_ids, 'College.active' => 1))
                    );

                    if (!empty($this->request->data['Student'])) {
                        $sections = $this->CourseRegistration->PublishedCourse->Section->find('all', array(
                            'conditions' => array(
                                'Section.college_id' => $this->request->data['Student']['college_id'],
                                'Section.program_type_id' => $this->request->data['Student']['program_type_id'],
                                'Section.program_id' => $this->request->data['Student']['program_id'],
                                'Section.academicyear LIKE ' => $this->request->data['Student']['academicyear'],
                                'Section.department_id IS NULL',
                                'Section.archive' => 0,
                            ),
                            'contain' => array(
                                'YearLevel' => array('id', 'name'),
                                'Program' => array('id', 'name'),
                                'ProgramType' => array('id', 'name'),
                                'Department' => array('id', 'name', 'type'),
                                'College' => array('id', 'name', 'type'),
                                'Curriculum' => array('id', 'name', 'type_credit', 'year_introduced', 'active')
                            ),
                            'order' => array(
                                'Section.year_level_id' => 'ASC',
                                'Section.college_id' => 'ASC',
                                'Section.department_id' => 'ASC',
                                'Section.id' => 'ASC',
                                'Section.name' => 'ASC'
                            ),
                            'recursive' => -1
                        ));
                    } else {
                        $sections = $this->CourseRegistration->PublishedCourse->Section->find('all', array(
                            'conditions' => array(
                                'Section.college_id' => key($colleges),
                                'Section.program_type_id' => key($programTypes),
                                'Section.program_id' => key($programs),
                                'Section.academicyear LIKE ' => $this->AcademicYear->current_academicyear(),
                                //'YearLevel.name LIKE ' => key($yearLevels),
                                'Section.department_id IS NULL',
                                'Section.archive' => 0,
                            ),
                            'contain' => array(
                                'YearLevel' => array('id', 'name'),
                                'Program' => array('id', 'name'),
                                'ProgramType' => array('id', 'name'),
                                'Department' => array('id', 'name', 'type'),
                                'College' => array('id', 'name', 'type'),
                                'Curriculum' => array('id', 'name', 'type_credit', 'year_introduced', 'active')
                            ),
                            'order' => array(
                                'Section.year_level_id' => 'ASC',
                                'Section.college_id' => 'ASC',
                                'Section.department_id' => 'ASC',
                                'Section.id' => 'ASC',
                                'Section.name' => 'ASC'
                            ),
                            'recursive' => -1
                        ));
                    }
                }
            }
        }

        if ($this->role_id == ROLE_DEPARTMENT && !empty($this->department_id)) {
            $departments = $this->CourseRegistration->Student->Department->find(
                'list',
                array('conditions' => array('Department.id' => $this->department_id))
            );

            if (!empty($this->request->data['Student'])) {
                $sections = $this->CourseRegistration->PublishedCourse->Section->find('all', array(
                    'conditions' => array(
                        'Section.department_id' => $this->request->data['Student']['department_id'],
                        'Section.program_type_id' => $this->request->data['Student']['program_type_id'],
                        'Section.program_id' => $this->request->data['Student']['program_id'],
                        'Section.academicyear LIKE ' => $this->request->data['Student']['academicyear'],
                        'YearLevel.name LIKE ' => $this->request->data['Student']['year_level_id'],
                        'OR' => array(
                            'Section.year_level_id IS NOT NULL',
                            'Section.year_level_id <> 0',
                            'Section.year_level_id != ""',
                        ),
                        'Section.archive' => 0,
                    ),
                    'contain' => array(
                        'YearLevel' => array('id', 'name'),
                        'Program' => array('id', 'name'),
                        'ProgramType' => array('id', 'name'),
                        'Department' => array('id', 'name', 'type'),
                        'College' => array('id', 'name', 'type'),
                        'Curriculum' => array('id', 'name', 'type_credit', 'year_introduced', 'active')
                    ),
                    'order' => array(
                        'Section.year_level_id' => 'ASC',
                        'Section.college_id' => 'ASC',
                        'Section.department_id' => 'ASC',
                        'Section.id' => 'ASC',
                        'Section.name' => 'ASC'
                    ),
                    'recursive' => -1
                ));
            } else {
                $sections = $this->CourseRegistration->PublishedCourse->Section->find('all', array(
                    'conditions' => array(
                        'Section.department_id' => $this->department_id,
                        'Section.program_type_id' => key($programTypes),
                        'Section.program_id' => key($programs),
                        'Section.academicyear LIKE ' => $this->AcademicYear->current_academicyear(),
                        'YearLevel.name LIKE ' => key($yearLevels),
                        'OR' => array(
                            'Section.year_level_id IS NOT NULL',
                            'Section.year_level_id <> 0',
                            'Section.year_level_id != ""',
                        ),
                        'Section.archive' => 0,
                    ),
                    'contain' => array(
                        'YearLevel' => array('id', 'name'),
                        'Program' => array('id', 'name'),
                        'ProgramType' => array('id', 'name'),
                        'Department' => array('id', 'name', 'type'),
                        'College' => array('id', 'name', 'type'),
                        'Curriculum' => array('id', 'name', 'type_credit', 'year_introduced', 'active')
                    ),
                    'order' => array(
                        'Section.year_level_id' => 'ASC',
                        'Section.college_id' => 'ASC',
                        'Section.department_id' => 'ASC',
                        'Section.id' => 'ASC',
                        'Section.name' => 'ASC'
                    ),
                    'recursive' => -1
                ));
            }
        } else {
            if ($this->role_id == ROLE_COLLEGE && !empty($this->college_id)) {
                $colleges = $this->CourseRegistration->Student->College->find(
                    'list',
                    array('conditions' => array('College.id' => $this->college_id))
                );

                if (!empty($this->request->data['Student'])) {
                    $sections = $this->CourseRegistration->PublishedCourse->Section->find('all', array(
                        'conditions' => array(
                            'Section.college_id' => $this->request->data['Student']['college_id'],
                            'Section.program_type_id' => $this->request->data['Student']['program_type_id'],
                            'Section.program_id' => $this->request->data['Student']['program_id'],
                            'Section.academicyear LIKE ' => $this->request->data['Student']['academicyear'],
                            'Section.department_id IS NULL',
                            'Section.archive' => 0,
                        ),
                        'contain' => array(
                            'YearLevel' => array('id', 'name'),
                            'Program' => array('id', 'name'),
                            'ProgramType' => array('id', 'name'),
                            'Department' => array('id', 'name', 'type'),
                            'College' => array('id', 'name', 'type'),
                            'Curriculum' => array('id', 'name', 'type_credit', 'year_introduced', 'active')
                        ),
                        'order' => array(
                            'Section.year_level_id' => 'ASC',
                            'Section.college_id' => 'ASC',
                            'Section.department_id' => 'ASC',
                            'Section.id' => 'ASC',
                            'Section.name' => 'ASC'
                        ),
                        'recursive' => -1
                    ));
                } else {
                    $sections = $this->CourseRegistration->PublishedCourse->Section->find('all', array(
                        'conditions' => array(
                            'Section.college_id' => $this->college_id,
                            'Section.program_type_id' => key($programTypes),
                            'Section.program_id' => key($programs),
                            'Section.academicyear LIKE ' => $this->AcademicYear->current_academicyear(),
                            'Section.department_id IS NULL',
                            'Section.archive' => 0,
                        ),
                        'contain' => array(
                            'YearLevel' => array('id', 'name'),
                            'Program' => array('id', 'name'),
                            'ProgramType' => array('id', 'name'),
                            'Department' => array('id', 'name', 'type'),
                            'College' => array('id', 'name', 'type'),
                            'Curriculum' => array('id', 'name', 'type_credit', 'year_introduced', 'active')
                        ),
                        'order' => array(
                            'Section.year_level_id' => 'ASC',
                            'Section.college_id' => 'ASC',
                            'Section.department_id' => 'ASC',
                            'Section.id' => 'ASC',
                            'Section.name' => 'ASC'
                        ),
                        'recursive' => -1
                    ));
                }
            }
        }

        if (!empty($sections)) {
            $sectionOrganizedByYearLevel = array();
            $sectionOrganizedByYearLevel[''] = '[ Select Section ]';
            foreach ($sections as $k => $v) {
                if (!empty($v['YearLevel']['name'])) {
                    $sectionOrganizedByYearLevel[$v['Section']['id']] = $v['Section']['name'] . " (" . $v['Section']['academicyear'] . ", " . $v['YearLevel']['name'] . ")";
                } else {
                    $sectionOrganizedByYearLevel[$v['Section']['id']] = $v['Section']['name'] . " (" . $v['Section']['academicyear'] . ", " . ($v['Section']['program_id'] == PROGRAM_REMEDIAL ? ' Remedial' : ' Pre/1st') . ")";
                }
            }
            $sections = $sectionOrganizedByYearLevel;
        } else {
            $sections[''] = '[ No Active Section, Try Changing Filters ] ';
        }

        //debug($sections);

        if (!empty($this->request->data['Student'])) {
            $program_name = $this->CourseRegistration->PublishedCourse->Program->field(
                'Program.name',
                array('Program.id' => $this->request->data['Student']['program_id'])
            );
            $program_type_name = $this->CourseRegistration->PublishedCourse->ProgramType->field(
                'ProgramType.name',
                array('ProgramType.id' => $this->request->data['Student']['program_type_id'])
            );
            $academic_year = $this->request->data['Student']['academicyear'];
            $semester = $this->request->data['Student']['semester'];

            if (isset($this->request->data['Student']['department_id']) && !empty($this->request->data['Student']['department_id'])) {
                $department_name = $this->CourseRegistration->PublishedCourse->Department->find(
                    'first',
                    array(
                        'conditions' => array('Department.id' => $this->request->data['Student']['department_id']),
                        'fields' => array('Department.id', 'Department.name', 'Department.type'),
                        'recursive' => -1
                    )
                );
            } else {
                if (isset($this->request->data['Student']['college_id']) && !empty($this->request->data['Student']['college_id'])) {
                    $college_name = $this->CourseRegistration->PublishedCourse->College->find(
                        'first',
                        array(
                            'conditions' => array('College.id' => $this->request->data['Student']['college_id']),
                            'fields' => array('College.id', 'College.name', 'College.type'),
                            'recursive' => -1
                        )
                    );
                }
            }

            $this->set(
                compact(
                    'program_name',
                    'program_type_name',
                    'academic_year',
                    'semester',
                    'department_name',
                    'college_name'
                )
            );
        }

        $this->set(
            compact(
                'departments',
                'colleges',
                'programs',
                'programTypes',
                'latest_semester_academic_year',
                'sections',
                'yearLevels'
            )
        );
        $this->render('maintain_registration');
    }

    public function __init_search()
    {

        // We create a search_data session variable when we fill any criteria in the search form.
        if (!empty($this->request->data['Student'])) {
            $search_session = $this->request->data['Student'];
            $this->Session->write('search_data_registration', $search_session);
        } else {
            $search_session = $this->Session->read('search_data_registration');
            $this->request->data['Student'] = $search_session;
        }
    }

    public function __init_maintain_academic_year()
    {

        // We create a search_data session variable when we fill any criteria in the search form.
        if (!empty($this->request->data['Student']) && !empty($this->request->data['Student']['academicyear'])) {
            $search_session = $this->request->data['Student'];
            $this->Session->write('search_data_registration', $search_session);
        } else {
            $search_session = $this->Session->read('search_data_registration');
            $this->request->data['Student'] = $search_session;
        }
    }

    public function register_individual_course()
    {

        if ($this->role_id != ROLE_REGISTRAR) {
            $this->redirect(array('action' => 'index'));
        }

        if ($this->Session->read('search_data_registration') && !isset($this->request->data['getsection'])) {
            $this->request->data['getsection'] = true;
            $this->request->data['Student'] = $this->Session->read('search_data_registration');
            $this->set('hide_search', true);
        }

        if (!empty($this->request->data) && isset($this->request->data['registerIndivdualCourse'])) {
            $one_is_selected = 0;
            $selected_published_courses = array();
            $formattedSaveAllRegistration = array();
            $count = 0;
            $total_selected_credit = 0;

            if (!empty($this->request->data['PublishedCourse'])) {
                foreach ($this->request->data['PublishedCourse'] as $section_id => $publishedcourse) {
                    $student_list = $this->CourseRegistration->Section->getSectionActiveStudentsId(
                        $section_id,
                        $this->request->data['Student']['academic_year']
                    );
                    foreach ($publishedcourse as $p_id => $selected) {
                        if ($selected == 1) {
                            $publishedCourseDetailedS = $this->CourseRegistration->PublishedCourse->find(
                                'first',
                                array(
                                    'conditions' => array('PublishedCourse.id' => $p_id),
                                    'contain' => array('Course' => array('id', 'credit')),
                                    'recursive' => -1
                                )
                            );
                            $one_is_selected++;
                            $total_selected_credit += $publishedCourseDetailedS['Course']['credit'];

                            if (!empty($student_list)) {
                                foreach ($student_list as $stk => $stv) {
                                    // credit checking per semester goes here.. using $stv
                                    $maxLoad = $this->CourseRegistration->Student->calculateStudentLoad(
                                        $stv,
                                        $publishedCourseDetailedS['PublishedCourse']['semester'],
                                        $publishedCourseDetailedS['PublishedCourse']['academic_year']
                                    );
                                    $allowedMaximum = ClassRegistry::init('AcademicCalendar')->maximumCreditPerSemester(
                                        $stv
                                    );

                                    debug($maxLoad);
                                    debug($allowedMaximum);
                                    debug($total_selected_credit);

                                    if (is_numeric(
                                            $maxLoad
                                        ) && $maxLoad > 0) { // > 0, only registers students who are registered for at least one course, >= 0 all students in the section, with or without registration
                                        // check max load + current registration < allowed credit for semester
                                        if ((($maxLoad + $publishedCourseDetailedS['Course']['credit']) <= $allowedMaximum) && (($maxLoad + $total_selected_credit) <= $allowedMaximum)) {
                                            //courseRegistered
                                            //debug($this->CourseRegistration->CourseDrop->course_taken($stv, $publishedCourseDetailedS['PublishedCourse']['course_id']));
                                            //debug($this->CourseRegistration->courseRegistered($p_id, $publishedCourseDetailedS['PublishedCourse']['semester'], $publishedCourseDetailedS['PublishedCourse']['academic_year'], $stv));

                                            if (!($this->CourseRegistration->courseRegistered(
                                                    $p_id,
                                                    $publishedCourseDetailedS['PublishedCourse']['semester'],
                                                    $publishedCourseDetailedS['PublishedCourse']['academic_year'],
                                                    $stv
                                                )) && $this->CourseRegistration->CourseDrop->course_taken(
                                                    $stv,
                                                    $publishedCourseDetailedS['PublishedCourse']['course_id']
                                                ) == 3) {
                                                $formattedSaveAllRegistration['CourseRegistration'][$count]['published_course_id'] = $publishedCourseDetailedS['PublishedCourse']['id'];
                                                $formattedSaveAllRegistration['CourseRegistration'][$count]['course_id'] = $publishedCourseDetailedS['PublishedCourse']['course_id'];
                                                $formattedSaveAllRegistration['CourseRegistration'][$count]['semester'] = $publishedCourseDetailedS['PublishedCourse']['semester'];
                                                $formattedSaveAllRegistration['CourseRegistration'][$count]['academic_year'] = $publishedCourseDetailedS['PublishedCourse']['academic_year'];
                                                $formattedSaveAllRegistration['CourseRegistration'][$count]['student_id'] = $stv;
                                                $formattedSaveAllRegistration['CourseRegistration'][$count]['section_id'] = $publishedCourseDetailedS['PublishedCourse']['section_id'];
                                                $formattedSaveAllRegistration['CourseRegistration'][$count]['year_level_id'] = (is_numeric(
                                                    $publishedCourseDetailedS['PublishedCourse']['year_level_id']
                                                ) && $publishedCourseDetailedS['PublishedCourse']['year_level_id'] > 0 ? $publishedCourseDetailedS['PublishedCourse']['year_level_id'] : null);
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

            //debug($formattedSaveAllRegistration);

            if (isset($formattedSaveAllRegistration) && !empty($formattedSaveAllRegistration)) {
                $total_registered = (int)((count(
                        $formattedSaveAllRegistration['CourseRegistration']
                    )) / $one_is_selected);
                if ($this->CourseRegistration->saveAll(
                    $formattedSaveAllRegistration['CourseRegistration'],
                    array('validate' => false)
                )) {
                    $this->Flash->success(
                        'Course registertion is maintained for ' . ($total_registered) . ' ' . ($total_registered > 1 ? 'elegible students' : 'elegible student') . ' who took prequisites if any, and not registered more than the allowed maximum credit set per semester(' . $allowedMaximum . ') including to selected course(s) and registered for atleast one course.'
                    );
                } else {
                    $this->Flash->error('The selected course couldn\'t be registered for the students.');
                }
            } else {
                $this->Flash->info(
                    'No student in the selected section(s) needs the selected course(s) registration(s).'
                );
            }

            if ($one_is_selected == 0) {
                $this->Flash->error('Please select atleast one course you want to register for eligible students.');
            }
        }

        if (!empty($this->request->data) && isset($this->request->data['getsection'])) {
            $this->Session->delete('search_data_registration');
            $everythingfine = false;

            switch ($this->request->data) {
                case empty($this->request->data['Student']['academic_year']):
                    $this->Flash->error('Please select the academic year you want to cancel course registration.');
                    break;
                case empty($this->request->data['Student']['semester']):
                    $this->Flash->error('Please select the semester you want to cancel  course registration.');
                    break;
                /* case empty($this->request->data['Student']['department_id']):
					$this->Flash->error('Please select the department you want to cancel  course registration.');
					break;
				case empty($this->request->data['Student']['year_level_id']):
					$this->Flash->error('Please select the year level you want cancel course registration.');
					break; */
                case empty($this->request->data['Student']['program_id']):
                    $this->Flash->error('Please select the program you want to cancel courses registration.');
                    break;
                case empty($this->request->data['Student']['program_type_id']):
                    $this->Flash->error('Please select the program type you want to cancel course registration.');
                    break;
                default:
                    $everythingfine = true;
            }

            if ($everythingfine) {
                $this->__init_search();

                if (isset($this->request->data['Student']['department_id']) && !empty($this->request->data['Student']['department_id'])) {
                    $yearLevelId = $this->CourseRegistration->PublishedCourse->YearLevel->field('id', array(
                        'YearLevel.department_id' => $this->request->data['Student']['department_id'],
                        'YearLevel.name' => $this->request->data['Student']['year_level_id']
                    ));

                    $sections = $this->CourseRegistration->Section->find('list', array(
                        'conditions' => array(
                            'Section.department_id' => $this->request->data['Student']['department_id'],
                            'Section.year_level_id' => $yearLevelId,
                            'Section.program_id' => $this->request->data['Student']['program_id'],
                            'Section.program_type_id' => $this->request->data['Student']['program_type_id'],
                            'Section.academicyear' => $this->request->data['Student']['academic_year']
                        )
                    ));

                    $listOfPublishedCourses = $this->CourseRegistration->PublishedCourse->find('all', array(
                        'conditions' => array(
                            'PublishedCourse.department_id' => $this->request->data['Student']['department_id'],
                            'PublishedCourse.year_level_id' => $yearLevelId,
                            'PublishedCourse.drop' => 0,
                            'PublishedCourse.add' => 0,
                            // It is preffered to filter out Mass Added Courses if not it will cause double registrations if mass added courses are approved first or approved later
                            'PublishedCourse.program_id' => $this->request->data['Student']['program_id'],
                            'PublishedCourse.program_type_id' => $this->request->data['Student']['program_type_id'],
                            'PublishedCourse.semester' => $this->request->data['Student']['semester'],
                            'PublishedCourse.academic_year' => $this->request->data['Student']['academic_year'],
                        ),
                        'fields' => array('id', 'section_id'),
                        //'group' => array('PublishedCourse.section_id', 'PublishedCourse.course_id'), // better to leave that off to show duplicated course publications so that they can be noticed and deleted
                        //'order' => array('PublishedCourse.drop' => 'ASC', 'PublishedCourse.add'  => 'ASC'),
                        'contain' => array(
                            'Course' => array(
                                'Curriculum' => array('id', 'name', 'type_credit', 'year_introduced', 'active'),
                                'fields' => array(
                                    'id',
                                    'course_title',
                                    'course_code',
                                    'credit',
                                    'lecture_hours',
                                    'tutorial_hours',
                                    'laboratory_hours'
                                )
                            ),
                            'Program' => array('id', 'name'),
                            'ProgramType' => array('id', 'name'),
                            'Department' => array('id', 'name', 'type'),
                            'College' => array('id', 'name', 'type'),
                            'Section' => array(
                                'fields' => array('id', 'name', 'academicyear', 'archive'),
                                'YearLevel' => array('id', 'name'),
                                'Program' => array('id', 'name'),
                                'ProgramType' => array('id', 'name'),
                                'Department' => array('id', 'name', 'type'),
                                'College' => array('id', 'name', 'type'),
                                'Curriculum' => array('id', 'name', 'type_credit', 'year_introduced', 'active'),
                            ),
                            'YearLevel' => array('id', 'name'),
                        )
                    ));
                } else {
                    if (isset($this->request->data['Student']['college_id']) && !empty($this->request->data['Student']['college_id'])) {
                        $sections = $this->CourseRegistration->Section->find('list', array(
                            'conditions' => array(
                                'Section.college_id' => $this->request->data['Student']['college_id'],
                                'Section.department_id is null',
                                'Section.program_id' => $this->request->data['Student']['program_id'],
                                'Section.program_type_id' => $this->request->data['Student']['program_type_id'],
                                'Section.academicyear' => $this->request->data['Student']['academic_year']
                            )
                        ));

                        $listOfPublishedCourses = $this->CourseRegistration->PublishedCourse->find('all', array(
                            'conditions' => array(
                                'PublishedCourse.college_id' => $this->request->data['Student']['college_id'],
                                'PublishedCourse.department_id is null',
                                'PublishedCourse.drop' => 0,
                                'PublishedCourse.add' => 0,
                                // It is preffered to filter out Mass Added Courses if not it will cause double registrations if mass added courses are approved first or approved later
                                'PublishedCourse.program_id' => $this->request->data['Student']['program_id'],
                                'PublishedCourse.program_type_id' => $this->request->data['Student']['program_type_id'],
                                'PublishedCourse.semester' => $this->request->data['Student']['semester'],
                                'PublishedCourse.academic_year' => $this->request->data['Student']['academic_year'],

                            ),
                            'fields' => array('id', 'section_id'),
                            'contain' => array(
                                'Course' => array(
                                    'Curriculum' => array('id', 'name', 'type_credit', 'year_introduced', 'active'),
                                    'fields' => array(
                                        'id',
                                        'course_title',
                                        'course_code',
                                        'credit',
                                        'lecture_hours',
                                        'tutorial_hours',
                                        'laboratory_hours'
                                    )
                                ),
                                'Program' => array('id', 'name'),
                                'ProgramType' => array('id', 'name'),
                                'Department' => array('id', 'name', 'type'),
                                'College' => array('id', 'name', 'type'),
                                'Section' => array(
                                    'fields' => array('id', 'name', 'academicyear', 'archive'),
                                    'YearLevel' => array('id', 'name'),
                                    'Program' => array('id', 'name'),
                                    'ProgramType' => array('id', 'name'),
                                    'Department' => array('id', 'name', 'type'),
                                    'College' => array('id', 'name', 'type'),
                                    'Curriculum' => array('id', 'name', 'type_credit', 'year_introduced', 'active'),
                                ),
                                'YearLevel' => array('id', 'name'),
                            )
                        ));
                    }
                }

                $organized_published_course_by_section = array();
                $published_counter = 0;

                if (!empty($listOfPublishedCourses)) {
                    foreach ($listOfPublishedCourses as $lp => $lv) {
                        if (isset($lv['PublishedCourse']['section_id']) && !empty($lv['PublishedCourse']['section_id'])) {
                            $organized_published_course_by_section[$lv['PublishedCourse']['section_id']][$published_counter] = $lv;
                            $publish_courses_list_ids[$published_counter] = $lv['PublishedCourse']['id'];
                        }
                        $published_counter++;
                    }
                }

                if (empty($listOfPublishedCourses) && !isset($this->request->data['registerIndivdualCourse'])) {
                    $this->Flash->info(
                        'No result is found in a given criteria. Either all students are registered or no active section is found with published course that needs mass course registration.'
                    );
                } else {
                    $this->set('hide_search', true);
                    $listofPublishedCourses = $organized_published_course_by_section;
                    $this->set(compact('sections', 'listOfPublishedCourses'));
                    $this->set(
                        compact('organized_published_course_by_section', 'published_counter', 'grade_submitted_counter')
                    );
                }

                if (!empty($this->request->data['Student']['year_level_id'])) {
                    $year_level_id = $this->request->data['Student']['year_level_id'];
                } else {
                    $year_level_id = 'Pre/1st';
                }

                $program_name = $this->CourseRegistration->PublishedCourse->Program->field(
                    'Program.name',
                    array('Program.id' => $this->request->data['Student']['program_id'])
                );
                $program_type_name = $this->CourseRegistration->PublishedCourse->ProgramType->field(
                    'ProgramType.name',
                    array('ProgramType.id' => $this->request->data['Student']['program_type_id'])
                );
                $academic_year = $this->request->data['Student']['academic_year'];
                $semester = $this->request->data['Student']['semester'];

                if (isset($this->request->data['Student']['department_id']) && !empty($this->request->data['Student']['department_id'])) {
                    $department_name = $this->CourseRegistration->PublishedCourse->Department->find(
                        'first',
                        array(
                            'conditions' => array('Department.id' => $this->request->data['Student']['department_id']),
                            'fields' => array('Department.id', 'Department.name', 'Department.type'),
                            'recursive' => -1
                        )
                    );
                } else {
                    if (isset($this->request->data['Student']['college_id']) && !empty($this->request->data['Student']['college_id'])) {
                        $college_name = $this->CourseRegistration->PublishedCourse->College->find(
                            'first',
                            array(
                                'conditions' => array('College.id' => $this->request->data['Student']['college_id']),
                                'fields' => array('College.id', 'College.name', 'College.type'),
                                'recursive' => -1
                            )
                        );
                    }
                }

                $this->set(
                    compact(
                        'sections',
                        'year_level_id',
                        'program_name',
                        'program_type_name',
                        'academic_year',
                        'semester',
                        'department_name',
                        'college_name'
                    )
                );
            }
        }

        if ($this->role_id == ROLE_REGISTRAR || ROLE_REGISTRAR == $this->Session->read(
                'Auth.User'
            )['Role']['parent_id']) {
            $yearLevels = $this->CourseRegistration->YearLevel->distinct_year_level();
            $programs = $this->CourseRegistration->Student->Program->find(
                'list',
                array('conditions' => array('Program.id' => $this->program_id))
            );
            if (!empty($this->department_ids)) {
                $departments = $this->CourseRegistration->Student->Department->find(
                    'list',
                    array('conditions' => array('Department.id' => $this->department_ids, 'Department.active' => 1))
                );
                $this->set(compact('departments'));
            } else {
                if (!empty($this->college_ids)) {
                    $colleges = $this->CourseRegistration->Student->College->find(
                        'list',
                        array('conditions' => array('College.id' => $this->college_ids, 'College.active' => 1))
                    );
                    $this->set(compact('colleges'));
                }
            }
            $this->set(compact('yearLevels', 'programs'));
        } else {
            if ($this->role_id == ROLE_COLLEGE) {
                $yearLevels = $this->CourseRegistration->YearLevel->distinct_year_level();
                $programs = $this->CourseRegistration->Student->Program->find('list');
                $departments = $this->CourseRegistration->Student->Department->find(
                    'list',
                    array('conditions' => array('Department.college_id' => $this->college_id, 'Department.active' => 1))
                );
                $this->set(compact('departments', 'yearLevels', 'programs'));
            } else {
                if ($this->role_id == ROLE_DEPARTMENT) {
                    $departments = $this->CourseRegistration->Student->Department->find(
                        'list',
                        array('conditions' => array('Department.id' => $this->department_id))
                    );
                    $yearLevels = $this->CourseRegistration->YearLevel->find(
                        'list',
                        array('conditions' => array('YearLevel.department_id' => $this->department_id))
                    );
                    $programs = $this->CourseRegistration->Student->Program->find('list');
                    $this->request->data['Student']['department_id'] = $this->department_id;
                    $this->set(compact('departments', 'yearLevels', 'programs'));
                }
            }
        }

        $programTypes = $this->CourseRegistration->Student->ProgramType->find('list');
        $this->set(compact('programTypes'));
    }

    // Function to cancel registration of selected department given academic year,semester.
    public function cancel_registration()
    {

        // Function to load/save search criteria.
        if ($this->Session->read('search_data_registration') && !isset($this->request->data['getsection'])) {
            $this->request->data['getsection'] = true;
            $this->request->data['Student'] = $this->Session->read('search_data_registration');
            $this->set('hide_search', true);
        }

        if (!empty($this->request->data) && isset($this->request->data['canceregistration'])) {
            $one_is_selected = 0;
            $selected_published_courses = array();

            if (!empty($this->request->data['PublishedCourse'])) {
                foreach ($this->request->data['PublishedCourse'] as $section_id => $publishedcourse) {
                    foreach ($publishedcourse as $p_id => $selected) {
                        if ($selected == 1) {
                            $one_is_selected++;
                            // break 2;
                            $selected_published_courses[] = $p_id;
                        }
                    }
                }
            }

            //check if checked.
            if ($one_is_selected) {
                if (!empty($selected_published_courses)) {
                    //foreach publish course
                    $register_for_delete['register'] = array();
                    $tmp = array();
                    $add_for_delete['add'] = array();
                    $grade_submitted_pub_count = 0;

                    foreach ($selected_published_courses as $key => $pid) {
                        $is_grade_submitted = $this->CourseRegistration->ExamGrade->is_grade_submitted($pid);
                        //check again if grade si not submitted then allow cancellation.
                        if (!$is_grade_submitted) {
                            $tmp = $this->CourseRegistration->PublishedCourse->getStudentsTakingPublishedCourse($pid);

                            if (!empty($tmp['register']) && count($tmp['register']) > 0) {
                                foreach ($tmp['register'] as $index => $value) {
                                    if (isset($value['CourseRegistration']['id']) && !empty($value['CourseRegistration']['id'])) {
                                        $register_for_delete['register'][] = $value['CourseRegistration']['id'];
                                    }

                                    if (isset($value['CourseAdd']['id']) && !empty($value['CourseAdd']['id'])) {
                                        $add_for_delete['add'][] = $value['CourseAdd']['id'];
                                    }
                                }
                            }
                            if (!empty($tmp['add']) && count($tmp['add']) > 0) {
                                foreach ($tmp['add'] as $index => $value) {
                                    if (!empty($value['CourseAdd']['id'])) {
                                        $add_for_delete['add'][] = $value['CourseAdd']['id'];
                                    }
                                }
                            }
                            $tmp = array();
                        } else {
                            $grade_submitted_pub_count++;
                        }
                    }

                    if (count($selected_published_courses) != $grade_submitted_pub_count) {
                        if (!empty($register_for_delete['register'])) {
                            if ($this->CourseRegistration->deleteAll(
                                array('CourseRegistration.id' => $register_for_delete['register']),
                                false
                            )) {
                            }
                        }

                        if (!empty($add_for_delete['add'])) {
                            if ($this->CourseRegistration->PublishedCourse->CourseAdd->deleteAll(
                                array('CourseAdd.id' => $add_for_delete['add']),
                                false
                            )) {
                            }
                        }
                        if (!empty($register_for_delete['register']) || !empty($add_for_delete['add'])) {
                            $this->Flash->success(
                                'Course registration is cancelled for all section students who registered/added for selected published course(s).'
                            );
                        }
                    } else {
                        $this->Flash->error(
                            'You can not cancel the selected course(s) registration as grade is already submitted for some or all students registered/added for the selected course(s).'
                        );
                    }
                    // $this->redirect(array('action'=>'index'));
                }
            } else {
                $this->Flash->error(
                    'Please select course(s) you want to cancel registration for those who were registered in the given section.'
                );
            }
        }

        if (!empty($this->request->data) && isset($this->request->data['getsection'])) {
            $this->Session->delete('search_data_registration');
            $everythingfine = false;
            switch ($this->request->data) {
                case empty($this->request->data['Student']['academic_year']):
                    $this->Flash->error('Please select the academic year you want to cancel course registration.');
                    break;
                case empty($this->request->data['Student']['semester']):
                    $this->Flash->error('Please select the semester you want to cancel  course registration.');
                    break;
                /* case empty($this->request->data['Student']['year_level_id']):
					$this->Flash->error('Please select the year level you want cancel course registration.');
					break; */
                case empty($this->request->data['Student']['program_id']):
                    $this->Flash->error('Please select the program you want to cancel courses registration.');
                    break;
                case empty($this->request->data['Student']['program_type_id']):
                    $this->Flash->error('Please select the program type you want to cancel course registration.');
                    break;
                default:
                    $everythingfine = true;
            }

            if ($everythingfine) {
                $this->__init_search();

                if (isset($this->request->data['Student']['department_id']) && !empty($this->request->data['Student']['department_id'])) {
                    $yearLevelId = $this->CourseRegistration->PublishedCourse->YearLevel->field('id', array(
                        'YearLevel.department_id' => $this->request->data['Student']['department_id'],
                        'YearLevel.name' => $this->request->data['Student']['year_level_id']
                    ));

                    $sections = $this->CourseRegistration->Section->find('list', array(
                        'conditions' => array(
                            'Section.department_id' => $this->request->data['Student']['department_id'],
                            'Section.year_level_id' => $yearLevelId,
                            'Section.program_id' => $this->request->data['Student']['program_id'],
                            'Section.program_type_id' => $this->request->data['Student']['program_type_id'],
                            'Section.academicyear' => $this->request->data['Student']['academic_year']
                        )
                    ));

                    $listOfPublishedCourses = $this->CourseRegistration->PublishedCourse->find('all', array(
                        'conditions' => array(
                            'PublishedCourse.department_id' => $this->request->data['Student']['department_id'],
                            'PublishedCourse.year_level_id' => $yearLevelId,
                            'PublishedCourse.drop' => 0,
                            'PublishedCourse.program_id' => $this->request->data['Student']['program_id'],
                            'PublishedCourse.program_type_id' => $this->request->data['Student']['program_type_id'],
                            'PublishedCourse.semester' => $this->request->data['Student']['semester'],
                            'PublishedCourse.academic_year' => $this->request->data['Student']['academic_year'],
                        ),
                        'fields' => array('id', 'section_id'),
                        'contain' => array(
                            'Course' => array(
                                'Curriculum' => array('id', 'name', 'type_credit', 'year_introduced', 'active'),
                                'fields' => array(
                                    'id',
                                    'course_title',
                                    'course_code',
                                    'credit',
                                    'lecture_hours',
                                    'tutorial_hours',
                                    'laboratory_hours'
                                )
                            ),
                            'Program' => array('id', 'name'),
                            'ProgramType' => array('id', 'name'),
                            'Department' => array('id', 'name', 'type'),
                            'College' => array('id', 'name', 'type'),
                            'Section' => array(
                                'fields' => array('id', 'name', 'academicyear', 'archive'),
                                'YearLevel' => array('id', 'name'),
                                'Program' => array('id', 'name'),
                                'ProgramType' => array('id', 'name'),
                                'Department' => array('id', 'name', 'type'),
                                'College' => array('id', 'name', 'type'),
                                'Curriculum' => array('id', 'name', 'type_credit', 'year_introduced', 'active'),
                            ),
                            'YearLevel' => array('id', 'name'),
                        )
                    ));
                } else {
                    if (isset($this->request->data['Student']['college_id']) && !empty($this->request->data['Student']['college_id'])) {
                        $sections = $this->CourseRegistration->Section->find('list', array(
                            'conditions' => array(
                                'Section.college_id' => $this->request->data['Student']['college_id'],
                                'Section.department_id is null',
                                //'Section.year_level_id is null',
                                'Section.program_id' => $this->request->data['Student']['program_id'],
                                'Section.program_type_id' => $this->request->data['Student']['program_type_id'],
                                'Section.academicyear' => $this->request->data['Student']['academic_year']
                            )
                        ));

                        $listOfPublishedCourses = $this->CourseRegistration->PublishedCourse->find('all', array(
                            'conditions' => array(
                                'PublishedCourse.college_id' => $this->request->data['Student']['college_id'],
                                'PublishedCourse.department_id is null',
                                'PublishedCourse.drop' => 0,
                                'PublishedCourse.program_id' => $this->request->data['Student']['program_id'],
                                'PublishedCourse.program_type_id' => $this->request->data['Student']['program_type_id'],
                                'PublishedCourse.semester' => $this->request->data['Student']['semester'],
                                'PublishedCourse.academic_year' => $this->request->data['Student']['academic_year'],

                            ),
                            'fields' => array('id', 'section_id'),
                            'contain' => array(
                                'Course' => array(
                                    'Curriculum' => array('id', 'name', 'type_credit', 'year_introduced', 'active'),
                                    'fields' => array(
                                        'id',
                                        'course_title',
                                        'course_code',
                                        'credit',
                                        'lecture_hours',
                                        'tutorial_hours',
                                        'laboratory_hours'
                                    )
                                ),
                                'Program' => array('id', 'name'),
                                'ProgramType' => array('id', 'name'),
                                'Department' => array('id', 'name', 'type'),
                                'College' => array('id', 'name', 'type'),
                                'Section' => array(
                                    'fields' => array('id', 'name', 'academicyear', 'archive'),
                                    'YearLevel' => array('id', 'name'),
                                    'Program' => array('id', 'name'),
                                    'ProgramType' => array('id', 'name'),
                                    'Department' => array('id', 'name', 'type'),
                                    'College' => array('id', 'name', 'type'),
                                    'Curriculum' => array('id', 'name', 'type_credit', 'year_introduced', 'active'),
                                ),
                                'YearLevel' => array('id', 'name'),
                            )
                        ));
                    }
                }

                $organized_published_course_by_section = array();
                $publish_courses_list_ids = array();
                $published_counter = 0;
                $grade_submitted_counter = 0;

                if (!empty($listOfPublishedCourses)) {
                    foreach ($listOfPublishedCourses as $lp => $lv) {
                        if (isset($lv['PublishedCourse']['section_id']) && !empty($lv['PublishedCourse']['section_id'])) {
                            $is_grade_submitted = $this->CourseRegistration->ExamGrade->is_grade_submitted(
                                $lv['PublishedCourse']['id']
                            );
                            $anyRegistration = $this->CourseRegistration->find(
                                'count',
                                array('conditions' => array('CourseRegistration.published_course_id' => $lv['PublishedCourse']['id']))
                            );

                            if ($anyRegistration) {
                                $organized_published_course_by_section[$lv['PublishedCourse']['section_id']][$published_counter] = $lv;

                                if ($is_grade_submitted) {
                                    $organized_published_course_by_section[$lv['PublishedCourse']['section_id']][$published_counter]['grade_submitted'] = 1;
                                    $grade_submitted_counter++;
                                } else {
                                    $organized_published_course_by_section[$lv['PublishedCourse']['section_id']][$published_counter]['grade_submitted'] = 0;
                                }
                                $publish_courses_list_ids[$published_counter] = $lv['PublishedCourse']['id'];
                            }
                        }

                        $published_counter++;
                    }
                }

                debug($publish_courses_list_ids);

                $publishedCourseRegister = $this->CourseRegistration->find('all', array(
                    'conditions' => array(
                        'CourseRegistration.published_course_id' => $publish_courses_list_ids,
                        'CourseRegistration.published_course_id IN (select published_course_id from course_registrations)',
                        'CourseRegistration.id NOT IN (select course_registration_id from exam_grades where  course_registration_id is not null)'
                    ),
                    'order' => array('CourseRegistration.id' => 'DESC'),
                    'contain' => array(
                        'ExamGrade',
                        'PublishedCourse' => array('Course')
                    )
                ));

                $publishedCourseAdd = ClassRegistry::init('CourseAdd')->find('all', array(
                    'conditions' => array(
                        'CourseAdd.published_course_id' => $publish_courses_list_ids,
                        'CourseAdd.published_course_id IN (select published_course_id  from course_adds)',
                        'CourseAdd.id NOT IN (select course_add_id from exam_grades where course_add_id is not null)'
                    ),
                    'contain' => array(
                        'ExamGrade',
                        'PublishedCourse' => array(
                            'Course'
                        )
                    )
                ));

                if (empty($publishedCourseRegister) && empty($publishedCourseAdd) && !isset($this->request->data['canceregistration'])) {
                    $this->Flash->info(
                        'No result is found. Either grade is submitted or there is no course registration in the selected criteria.'
                    );
                } else {
                    $this->set('hide_search', true);
                    $listofPublishedCourses = $organized_published_course_by_section;
                    $this->set(compact('sections', 'listOfPublishedCourses'));
                    $this->set(
                        compact('organized_published_course_by_section', 'published_counter', 'grade_submitted_counter')
                    );
                }

                if (!empty($this->request->data['Student']['year_level_id'])) {
                    $year_level_id = $this->request->data['Student']['year_level_id'];
                } else {
                    $year_level_id = 'Pre/1st';
                }

                $program_name = $this->CourseRegistration->PublishedCourse->Program->field(
                    'Program.name',
                    array('Program.id' => $this->request->data['Student']['program_id'])
                );
                $program_type_name = $this->CourseRegistration->PublishedCourse->ProgramType->field(
                    'ProgramType.name',
                    array('ProgramType.id' => $this->request->data['Student']['program_type_id'])
                );
                $academic_year = $this->request->data['Student']['academic_year'];
                $semester = $this->request->data['Student']['semester'];

                if (isset($this->request->data['Student']['department_id']) && !empty($this->request->data['Student']['department_id'])) {
                    $department_name = $this->CourseRegistration->PublishedCourse->Department->find(
                        'first',
                        array(
                            'conditions' => array('Department.id' => $this->request->data['Student']['department_id']),
                            'fields' => array('Department.id', 'Department.name', 'Department.type'),
                            'recursive' => -1
                        )
                    );
                } else {
                    if (isset($this->request->data['Student']['college_id']) && !empty($this->request->data['Student']['college_id'])) {
                        $college_name = $this->CourseRegistration->PublishedCourse->College->find(
                            'first',
                            array(
                                'conditions' => array('College.id' => $this->request->data['Student']['college_id']),
                                'fields' => array('College.id', 'College.name', 'College.type'),
                                'recursive' => -1
                            )
                        );
                    }
                }

                $this->set(
                    compact(
                        'sections',
                        'year_level_id',
                        'program_name',
                        'program_type_name',
                        'academic_year',
                        'semester',
                        'department_name',
                        'college_name'
                    )
                );
            }
        }

        if ($this->role_id == ROLE_REGISTRAR || ROLE_REGISTRAR == $this->Session->read(
                'Auth.User'
            )['Role']['parent_id']) {
            $yearLevels = $this->CourseRegistration->YearLevel->distinct_year_level();
            $programs = $this->CourseRegistration->Student->Program->find(
                'list',
                array('conditions' => array('Program.id' => $this->program_id))
            );

            if (isset($this->department_ids) && !empty($this->department_ids)) {
                $departments = $this->CourseRegistration->Student->Department->find(
                    'list',
                    array('conditions' => array('Department.id' => $this->department_ids))
                );
                $this->set(compact('departments'));
            } else {
                $colleges = $this->CourseRegistration->Student->College->find(
                    'list',
                    array('conditions' => array('College.id' => $this->college_ids, 'College.active' => 1))
                );
                $this->set(compact('colleges'));
            }
            $this->set(compact('yearLevels', 'programs'));
        } else {
            if ($this->role_id == ROLE_COLLEGE) {
                $yearLevels = $this->CourseRegistration->YearLevel->distinct_year_level();
                $programs = $this->CourseRegistration->Student->Program->find('list');
                if (isset($this->department_ids) && !empty($this->department_ids)) {
                    $departments = $this->CourseRegistration->Student->Department->find(
                        'list',
                        array('conditions' => array('Department.id' => $this->department_ids))
                    );
                } else {
                    $colleges = $this->CourseRegistration->Student->College->find(
                        'list',
                        array('conditions' => array('College.id' => $this->college_ids, 'College.active' => 1))
                    );
                }
                $this->set(compact('departments', 'colleges', 'yearLevels', 'programs'));
            } else {
                if ($this->role_id == ROLE_DEPARTMENT) {
                    $departments = $this->CourseRegistration->Student->Department->find(
                        'list',
                        array('conditions' => array('Department.id' => $this->department_id))
                    );
                    $yearLevels = $this->CourseRegistration->YearLevel->find(
                        'list',
                        array('conditions' => array('YearLevel.department_id' => $this->department_id))
                    );
                    $programs = $this->CourseRegistration->Student->Program->find('list');
                    $this->request->data['Student']['department_id'] = $this->department_id;
                    $this->set(compact('departments', 'yearLevels', 'programs'));
                }
            }
        }

        $programTypes = $this->CourseRegistration->Student->ProgramType->find('list');
        $this->set(compact('programTypes'));
    }

    public function show_course_registred_students($published_course_id = null)
    {

        $this->layout = 'ajax';
        // give the user the list of courses which is already displayed from the session when validation error occur.
        $registred_students = $this->CourseRegistration->PublishedCourse->getStudentsTakingPublishedCourse(
            $published_course_id
        );
        $this->set(compact('registred_students'));
    }

    public function get_course_registered_grade_list($register_or_add = null)
    {

        $this->layout = 'ajax';
        $grade_scale = array();

        if ($register_or_add != "0" && $register_or_add != "") {
            $register_or_add = explode('~', $register_or_add);
            if (strcasecmp($register_or_add[1], 'add') == 0) {
                $published_course_id = $this->CourseRegistration->PublishedCourse->CourseAdd->field(
                    'published_course_id',
                    array('id' => $register_or_add[0])
                );
            } else {
                $published_course_id = $this->CourseRegistration->field(
                    'published_course_id',
                    array('id' => $register_or_add[0])
                );
            }
            $grade_scale = $this->CourseRegistration->PublishedCourse->CourseRegistration->getPublishedCourseGradeScaleList(
                $published_course_id
            );
            //$grade_scale = $grade_scale + array('NG' => 'NG');
            $grade_scale = array('0' => '[ Select Grade ]') + $grade_scale;
        }
        $this->set(compact('grade_scale'));
    }

    public function get_course_registered_grade_result($register_or_add = null)
    {

        $this->layout = 'ajax';
        $grade_history = array();
        if ($register_or_add != "0" && $register_or_add != "") {
            $register_or_add = explode('~', $register_or_add);
            if (count($register_or_add) == 2) {
                if ($register_or_add[1] == 'register') {
                    $grade_history = $this->CourseRegistration->getCourseRegistrationGradeHistory($register_or_add[0]);
                } else {
                    $grade_history = $this->CourseRegistration->PublishedCourse->CourseAdd->getCourseAddGradeHistory(
                        $register_or_add[0]
                    );
                }
            }
        }
        $this->set(compact('grade_history', 'register_or_add'));
    }

    public function _givenPublisheCourseReturnDept($publish_course_ids = array())
    {

        //write it as function and reuse
        //$department_colleges_ids = array ();
        $department_colleges_ids['dept'] = array();
        $department_colleges_ids['college'] = array();
        if (!empty($publish_course_ids)) {
            foreach ($publish_course_ids as $id => $idvalue) {
                /*
				if (!empty($idvalue['department_id'])) {
                    $department_ids[] = $this->CourseRegistration->PublishedCourse->field('department_id',array('PublishedCourse.id'=>$idvalue));
                }
                */
                $college_department = $this->CourseRegistration->PublishedCourse->find('first', array(
                    'conditions' => array('PublishedCourse.id' => $idvalue),
                    'fields' => array('department_id', 'college_id'),
                    'recursive' => -1
                ));

                if (!empty($college_department['PublishedCourse']['department_id'])) {
                    $department_colleges_ids['dept'][] = $college_department['PublishedCourse']['department_id'];
                } else {
                    $department_colleges_ids['college'][] = $college_department['PublishedCourse']['college_id'];
                }
            }
        }
        return $department_colleges_ids;
    }

    // Cancel individual student registration
    public function cancel_individual_registration($student_id = null)
    {

        if (!empty($this->request->data) && isset($this->request->data['canceregistration'])) {
            $registrationListForDelete = array_keys($this->request->data['CourseRegistration']);

            if ($this->CourseRegistration->deleteAll(array('CourseRegistration.id' => $registrationListForDelete),
                false)) {
                $this->Flash->success('The selected student course registration cancellation is successful.');
            }

            $this->Session->delete('search_data_registration');
            unset($this->request->data['getstudentregistration']);
        }

        // Function to load/save search criteria.
        if ($this->Session->read(
                'search_data_registration'
            ) && !isset($this->request->data['getstudentregistration'])) {
            $this->request->data['getstudentregistration'] = true;
            $this->request->data['Student'] = $this->Session->read('search_data_registration');
            $this->set('hide_search', true);
        }

        if (!empty($this->request->data) && isset($this->request->data['getstudentregistration'])) {
            $this->Session->delete('search_data_registration');
            $everythingfine = false;
            switch ($this->request->data) {
                case empty($this->request->data['Student']['academic_year']):
                    $this->Flash->error('Please select the academic year you want to cancel course registration.');
                    break;
                case empty($this->request->data['Student']['semester']):
                    $this->Flash->error('Please select the semester you want to cancel  course registration.');
                    break;
                case empty($this->request->data['Student']['studentnumber']):
                    $this->Flash->error(
                        'Please provide the student number (ID) you want to cancel course registration.'
                    );
                    break;
                default:
                    $everythingfine = true;
            }

            if ($everythingfine) {
                $check_id_is_valid = $this->CourseRegistration->Student->find(
                    'count',
                    array(
                        'conditions' => array(
                            'Student.studentnumber' => trim(
                                $this->request->data['Student']['studentnumber']
                            )
                        )
                    )
                );

                if ($check_id_is_valid > 0) {
                    // do something if needed
                } else {
                    $everythingfine = false;
                    $this->Flash->error('The provided student number is not valid.');
                }
            }

            if ($everythingfine) {
                $this->__init_search();

                $studentDbId = $this->CourseRegistration->Student->field(
                    'Student.id',
                    array('Student.studentnumber' => $this->request->data['Student']['studentnumber'])
                );
                $student_section = $this->CourseRegistration->Student->student_academic_detail(
                    $studentDbId,
                    $this->request->data['Student']['academic_year']
                );
                $student_section_exam_status = $this->CourseRegistration->Student->get_student_section(
                    $studentDbId,
                    $this->request->data['Student']['academic_year'],
                    $this->request->data['Student']['semester']
                );

                $course_registration_id_publish_ids = $this->CourseRegistration->find('list', array(
                    'conditions' => array(
                        'CourseRegistration.student_id' => $studentDbId,
                        'CourseRegistration.semester' => $this->request->data['Student']['semester'],
                        'CourseRegistration.academic_year' => $this->request->data['Student']['academic_year']
                    ),
                    'fields' => array('CourseRegistration.id', 'CourseRegistration.published_course_id'),
                    'recursive' => -1
                ));

                $publiscourse_ids = array_values($course_registration_id_publish_ids);

                $listOfPublishedCourses = $this->CourseRegistration->PublishedCourse->find('all', array(
                    'conditions' => array(
                        'PublishedCourse.id' => $publiscourse_ids,
                        'PublishedCourse.drop' => 0,
                        'PublishedCourse.academic_year' => $this->request->data['Student']['academic_year'],
                        'PublishedCourse.semester' => $this->request->data['Student']['semester'],
                    ),
                    'fields' => array('id', 'section_id'),
                    'contain' => array(
                        'Course' => array(
                            'Curriculum' => array('id', 'name', 'type_credit', 'year_introduced', 'active'),
                            'fields' => array(
                                'id',
                                'course_title',
                                'course_code',
                                'credit',
                                'lecture_hours',
                                'tutorial_hours',
                                'laboratory_hours'
                            )
                        ),
                        'Program' => array('id', 'name'),
                        'ProgramType' => array('id', 'name'),
                        'Department' => array('id', 'name', 'type'),
                        'College' => array('id', 'name', 'type'),
                        'Section' => array(
                            'fields' => array('id', 'name', 'academicyear', 'archive'),
                            'YearLevel' => array('id', 'name'),
                            'Program' => array('id', 'name'),
                            'ProgramType' => array('id', 'name'),
                            'Department' => array('id', 'name', 'type'),
                            'College' => array('id', 'name', 'type'),
                            'Curriculum' => array('id', 'name', 'type_credit', 'year_introduced', 'active'),
                        ),
                        'YearLevel' => array('id', 'name'),
                    )
                ));

                $organized_published_course_by_section = array();
                $publish_courses_list_ids = array();
                $published_counter = 0;
                $grade_submitted_counter = 0;
                $isGradeSubmittedToAnyCourse = false;

                if (!empty($listOfPublishedCourses)) {
                    foreach ($listOfPublishedCourses as $lp => $lv) {
                        if (isset($lv['PublishedCourse']['section_id']) && !empty($lv['PublishedCourse']['section_id'])) {
                            $is_grade_submitted = $this->CourseRegistration->ExamGrade->is_grade_submitted(
                                $lv['PublishedCourse']['id']
                            );
                            $organized_published_course_by_section[$lv['PublishedCourse']['section_id']][$published_counter] = $lv;

                            if ($is_grade_submitted) {
                                $organized_published_course_by_section[$lv['PublishedCourse']['section_id']][$published_counter]['grade_submitted'] = 1;
                                $grade_submitted_counter++;
                                $isGradeSubmittedToAnyCourse = true;
                            } else {
                                $organized_published_course_by_section[$lv['PublishedCourse']['section_id']][$published_counter]['grade_submitted'] = 0;
                            }
                            $publish_courses_list_ids[$published_counter] = $lv['PublishedCourse']['id'];
                        }
                        $published_counter++;
                    }
                }

                if (empty($listOfPublishedCourses)) {
                    $this->Flash->info('No result is found. There is no course registration in the selected criteria.');
                } else {
                    $this->set('hide_search', true);
                    $listofPublishedCourses = $organized_published_course_by_section;
                    $this->set(compact('listOfPublishedCourses'));

                    $this->set(
                        compact(
                            'organized_published_course_by_section',
                            'published_counter',
                            'grade_submitted_counter'
                        )
                    );
                }

                $this->set(
                    compact(
                        'student_section_exam_status',
                        'isGradeSubmittedToAnyCourse',
                        'course_registration_id_publish_ids'
                    )
                );
            }
        }

        $current_acy = $this->AcademicYear->current_academicyear();

        if (is_numeric(ACY_BACK_COURSE_REGISTRATION) && ACY_BACK_COURSE_REGISTRATION) {
            $academicYearList = $this->AcademicYear->academicYearInArray(
                ((explode('/', $current_acy)[0]) - ACY_BACK_COURSE_REGISTRATION),
                (explode('/', $current_acy)[0])
            );
        } else {
            $academicYearList[$current_acy] = $current_acy;
        }

        if ($this->Session->read('Auth.User')['role_id'] == ROLE_REGISTRAR && $this->Session->read(
                'Auth.User'
            )['is_admin'] == 1) {
            $academicYearList = $this->AcademicYear->academicYearInArray(
                APPLICATION_START_YEAR,
                (explode('/', $current_acy)[0])
            );
        }

        debug($current_acy);

        $this->set(compact('academicYearList'));
    }

    public function grade_view_by_course()
    {

        $this->paginate = array(
            'contain' => array(
                'Student' => array(
                    'Department',
                    'Curriculum',
                    'ProgramType',
                    'Program'
                ),
                'ExamGrade' => array('order' => array('ExamGrade.created DESC'))
            )
        );

        if ((isset($this->request->data['CourseRegistration']) && isset($this->request->data['viewPDF']))) {
            $search_session = $this->Session->read('search_data_list_course');
            $this->request->data['CourseRegistration'] = $search_session;
        }

        if (isset($this->passedArgs)) {
            if (isset($this->passedArgs['page'])) {
                $this->__init_search_course_lists();
                $this->request->data['CourseRegistration']['page'] = $this->passedArgs['page'];
                $this->__init_search_course_lists();
            }
        }

        if ((isset($this->request->data['CourseRegistration']) && isset($this->request->data['listStudentWithGrade']))) {
            $this->__init_search_course_lists();
        }

        //limit
        if (isset($this->request->data['CourseRegistration']['limit']) && !empty($this->request->data['CourseRegistration']['limit'])) {
            $this->paginate['limit'] = $this->request->data['CourseRegistration']['limit'];
        } else {
            $this->paginate['limit'] = 50;
        }

        // filter by department
        if (isset($this->request->data['CourseRegistration']['department_id']) && !empty($this->request->data['CourseRegistration']['department_id'])) {
            $this->paginate['conditions'][]['Student.department_id'] = $this->request->data['CourseRegistration']['department_id'];
        }

        // filter by college
        if (isset($this->request->data['CourseRegistration']['college_id']) && !empty($this->request->data['CourseRegistration']['college_id'])) {
            if ($this->request->data['CourseRegistration']['college_id'] == 'pre') {
                $this->paginate['conditions'][]['Student.college_id'] = $this->request->data['CourseRegistration']['college_id'];
                $this->paginate['conditions'][] = 'Student.department_id is null';
            } else {
                $this->paginate['conditions'][]['Student.college_id'] = $this->request->data['CourseRegistration']['college_id'];
            }
        }

        // filter by program
        if (isset($this->request->data['CourseRegistration']['program_id']) && !empty($this->request->data['CourseRegistration']['program_id'])) {
            $this->paginate['conditions'][]['Student.program_id'] = $this->request->data['CourseRegistration']['program_id'];
        }

        // filter by program type
        if (isset($this->request->data['CourseRegistration']['program_type_id']) && !empty($this->request->data['CourseRegistration']['program_type_id'])) {
            $this->paginate['conditions'][]['Student.program_type_id'] = $this->request->data['CourseRegistration']['program_type_id'];
        }


        // filter by program type
        if (isset($this->request->data['CourseRegistration']['course_id']) && !empty($this->request->data['CourseRegistration']['course_id'])) {
            $listCourseRegistrationIdsSql = "SELECT GROUP_CONCAT( cr.id ) as ids FROM  course_registrations AS cr, published_courses AS ps WHERE cr.academic_year='" . $this->request->data['CourseRegistration']['acadamic_year'] . "' AND cr.semester= '" . $this->request->data['CourseRegistration']['semester'] . "' AND ps.semester='" . $this->request->data['CourseRegistration']['semester'] . "' AND ps.academic_year='" . $this->request->data['CourseRegistration']['acadamic_year'] . "' AND ps.id = cr.published_course_id AND ps.course_id =" . $this->request->data['CourseRegistration']['course_id'] . " AND ps.program_id=" . $this->request->data['CourseRegistration']['program_id'] . " AND ps.program_type_id=" . $this->request->data['CourseRegistration']['program_type_id'] . " and cr.published_course_id=ps.id ORDER BY GROUP_CONCAT(cr.id)";
            $listCourseRegistrationIdsQueryResult = ClassRegistry::init('CourseRegistration')->query(
                $listCourseRegistrationIdsSql
            );

            if (!empty($listCourseRegistrationIdsQueryResult[0][0]['ids'])) {
                $this->paginate['conditions'][]['CourseRegistration.id'] = explode(
                    ',',
                    $listCourseRegistrationIdsQueryResult[0][0]['ids']
                );
            }
        }

        //order by
        if (isset($this->request->data['CourseRegistration']['sortby']) && !empty($this->request->data['CourseRegistration']['sortby'])) {
            $this->paginate['order'] = array(
                'Student.' . $this->request->data['CourseRegistration']['sortby'] . ' ASC',
                'Student.first_name'
            );
        }

        if (isset($this->request->data['CourseRegistration']['page']) && !empty($this->request->data['CourseRegistration']['page'])) {
            $this->paginate['page'] = $this->request->data['CourseRegistration']['page'];
        }

        $this->Paginator->settings = $this->paginate;
        //debug($this->Paginator->settings);

        if (isset($this->Paginator->settings['conditions'])) {
            $studentExamGradeList = $this->Paginator->paginate('CourseRegistration');
        } else {
            $studentExamGradeList = array();
        }

        if (empty($studentExamGradeList) && isset($this->request->data) && !empty($this->request->data)) {
            $this->Flash->info('No student taking the course and score grade for the selected course.');
        }

        if ((!empty($this->request->data['CourseRegistration']) && !empty($this->request->data['viewPDF']))) {
            $this->autoLayout = false;
            $courseDetail = $this->CourseRegistration->PublishedCourse->Course->find(
                'first',
                array(
                    'conditions' => array('Course.id' => $this->request->data['CourseRegistration']['course_id']),
                    'contain' => array('Curriculum', 'YearLevel')
                )
            );
            $academicYear = $this->request->data['CourseRegistration']['acadamic_year'];
            $semester = $this->request->data['CourseRegistration']['semester'];

            $department = $this->CourseRegistration->Student->Department->find('first', array(
                'conditions' => array(
                    'Department.id' => $this->request->data['CourseRegistration']['department_id']
                ),
                'contain' => array('College' => array('Campus'))
            ));

            $program = $this->CourseRegistration->Student->Program->find('first', array(
                'conditions' => array(
                    'Program.id' => $this->request->data['CourseRegistration']['program_id']
                ),
                'recursive' => -1
            ));

            $programType = $this->CourseRegistration->Student->ProgramType->find('first', array(
                'conditions' => array(
                    'ProgramType.id' => $this->request->data['CourseRegistration']['program_type_id']
                ),
                'recursive' => -1
            ));

            $university = ClassRegistry::init('University')->getStudentUnivrsity(
                $studentExamGradeList[0]['CourseRegistration']['student_id']
            );
            $filename = "Roaster- " . $department['Department']['name'] . ' Academic_Year-' . $academicYear . ' Semester- ' . $semester;
            $this->set(
                compact(
                    'courseDetail',
                    'department',
                    'program',
                    'programType',
                    'university',
                    'studentExamGradeList',
                    'filename',
                    'academicYear',
                    'semester'
                )
            );

            $this->render('grade_view_xls');
            /*
			$this->set(compact('studentExamGradeList'));
			$this->response->type('application/pdf');
			$this->layout = '/pdf/default';
			$this->render('grade_view_list_pdf');
			*/
        }

        if (!empty($this->department_ids)) {
            $departments = $this->CourseRegistration->PublishedCourse->Department->find(
                'list',
                array('conditions' => array('Department.id' => $this->department_ids))
            );
        } else {
            if (!empty($this->college_ids)) {
                $colleges = $this->CourseRegistration->PublishedCourse->College->find(
                    'list',
                    array('conditions' => array('College.id' => $this->college_ids))
                );
            } else {
                if (!empty($this->department_id) && $this->role_id == ROLE_DEPARTMENT) {
                    $departments = $this->CourseRegistration->PublishedCourse->Department->find(
                        'list',
                        array('conditions' => array('Department.id' => $this->department_id))
                    );
                } else {
                    if (!empty($this->college_id) && $this->role_id == ROLE_COLLEGE) {
                        $colleges = $this->CourseRegistration->PublishedCourse->College->find(
                            'list',
                            array('conditions' => array('College.id' => $this->college_id))
                        );
                        $colleges['pre'] = 'Pre Engineering';
                    }
                }
            }
        }

        $selectedAcademicYear = $this->AcademicYear->current_academicyear();
        $defaultSemester = "I";

        if (!empty($this->request->data['CourseRegistration']['acadamic_year'])) {
            $selectedAcademicYear = $this->request->data['CourseRegistration']['acadamic_year'];
        }

        if (!empty($this->request->data['CourseRegistration']['semester'])) {
            $defaultSemester = $this->request->data['CourseRegistration']['semester'];
        }

        $programs = $this->CourseRegistration->PublishedCourse->Section->Program->find('list');
        $program_types = $this->CourseRegistration->PublishedCourse->Section->ProgramType->find('list');

        if (isset($this->request->data['CourseRegistration']) && !empty($this->request->data['CourseRegistration']['department_id'])) {
            $defaultDepartment = $this->request->data['CourseRegistration']['department_id'];
        } else {
            $defaultDepartment = current(array_keys($departments));
        }

        if (isset($this->request->data['CourseRegistration']) && !empty($this->request->data['CourseRegistration']['college_id'])) {
            $defaultCollege = $this->request->data['CourseRegistration']['college_id'];
        } else {
            if (!empty($colleges)) {
                $defaultCollege = current(array_keys($colleges));
            } else {
                $defaultCollege = null;
            }
        }

        if (isset($this->request->data['CourseRegistration']) && !empty($this->request->data['CourseRegistration']['program_id'])) {
            $defaultProgram = $this->request->data['CourseRegistration']['program_id'];
        } else {
            $defaultProgram = current(array_keys($programs));
        }

        if (isset($this->request->data['CourseRegistration']) && !empty($this->request->data['CourseRegistration']['program_type_id'])) {
            $defaultProgramType = $this->request->data['CourseRegistration']['program_type_id'];
        } else {
            $defaultProgramType = current(array_keys($program_types));
        }

        if (!empty($defaultDepartment)) {
            $courses = $this->_getCourseLists(
                $selectedAcademicYear,
                $defaultSemester,
                $defaultProgram,
                $defaultProgramType,
                $defaultCollege,
                $defaultDepartment,
                0
            );
        } else {
            $courses = $this->_getCourseLists(
                $selectedAcademicYear,
                $defaultSemester,
                $defaultProgram,
                $defaultProgramType,
                $defaultCollege,
                $defaultDepartment,
                1
            );
        }

        $sortOptions = array(
            'middle_name' => 'Middle Name',
            'last_name' => 'Last Name',
            'studentnumber' => 'Student ID'
        );
        $this->set(
            compact(
                'programs',
                'courses',
                'program_types',
                'departments',
                'sortOptions',
                'colleges',
                'studentExamGradeList'
            )
        );
    }

    public function get_course_category_combo($paramaters)
    {

        $this->layout = 'ajax';
        $courseLists = array();
        $criteriaLists = explode('~', $paramaters);
        if (!empty($criteriaLists[0])) {
            $courseLists = $this->_getCourseLists(
                str_replace('-', '/', $criteriaLists[2]),
                $criteriaLists[3],
                $criteriaLists[4],
                $criteriaLists[5],
                $criteriaLists[1],
                $criteriaLists[0],
                0
            );
            $this->set(compact('courseLists'));
        } else {
            if (!empty($criteriaLists[1])) {
                if ($criteriaLists[1] == 'pre') {
                    $courseLists = $this->_getCourseLists(
                        str_replace('-', '/', $criteriaLists[2]),
                        $criteriaLists[3],
                        $criteriaLists[4],
                        $criteriaLists[5],
                        $criteriaLists[1],
                        $criteriaLists[0],
                        1
                    );
                } else {
                    $courseLists = $this->_getCourseLists(
                        str_replace('-', '/', $criteriaLists[2]),
                        $criteriaLists[3],
                        $criteriaLists[4],
                        $criteriaLists[5],
                        $criteriaLists[1],
                        $criteriaLists[0],
                        0
                    );
                }
            }
        }
        $this->set(compact('courseLists'));
    }

    public function _getCourseLists(
        $academic_year,
        $semester,
        $program_id,
        $program_type_id,
        $college_id = null,
        $department_id = null,
        $pre = false
    ) {

        $courseLists = array();
        if ($pre) {
            $courses = $this->CourseRegistration->PublishedCourse->find('all', array(
                'conditions' => array(
                    'PublishedCourse.academic_year' => $academic_year,
                    'PublishedCourse.semester' => $semester,
                    'PublishedCourse.program_id' => $program_id,
                    'PublishedCourse.program_type_id' => $program_type_id,
                    'PublishedCourse.college_id' => $college_id,
                    'PublishedCourse.department_id is null'
                ),
                'contain' => array('Course')
            ));
        } else {
            if (!empty($department_id)) {
                $courses = $this->CourseRegistration->PublishedCourse->find('all', array(
                    'conditions' => array(
                        'PublishedCourse.academic_year' => $academic_year,
                        'PublishedCourse.semester' => $semester,
                        'PublishedCourse.program_id' => $program_id,
                        'PublishedCourse.program_type_id' => $program_type_id,
                        'PublishedCourse.department_id' => $department_id
                    ),
                    'contain' => array('Course')
                ));
            } else {
                if (!empty($college_id)) {
                    $courses = $this->CourseRegistration->PublishedCourse->find('all', array(
                        'conditions' => array(
                            'PublishedCourse.academic_year' => $academic_year,
                            'PublishedCourse.semester' => $semester,
                            'PublishedCourse.program_id' => $program_id,
                            'PublishedCourse.program_type_id' => $program_type_id,
                            'PublishedCourse.college_id' => $college_id
                        ),
                        'contain' => array('Course')
                    ));
                }
            }
        }

        if (!empty($courses)) {
            foreach ($courses as $k => $v) {
                $courseLists[$v['PublishedCourse']['course_id']] = $v['Course']['course_title'] . '(' . $v['Course']['course_code'] . '-' . $v['Course']['credit'] . ')';
            }
        }

        return $courseLists;
    }


    public function __init_search_course_lists()
    {

        if (!empty($this->request->data['CourseRegistration'])) {
            $search_session = $this->request->data['CourseRegistration'];
            // Session variable 'search_data'
            $this->Session->write('search_data_list_course', $search_session);
        } else {
            $search_session = $this->Session->read('search_data_list_course');
            $this->request->data['CourseRegistration'] = $search_session;
        }
    }

    public function index($academic_year = null, $semester = null)
    {

        if (empty($academic_year) && empty($semester)) {
            $this->__init_search_index();
        }

        $this->__view_registration($academic_year, $semester);
    }

    public function __view_registration($academic_year = null, $semester = null)
    {

        $options = array(
            'contain' => array(
                'Student' => array(
                    'Program' => array('id', 'name', 'shortname'),
                    'ProgramType' => array('id', 'name', 'shortname'),
                    'Department' => array('id', 'name', 'type'),
                    'College' => array('id', 'name', 'shortname', 'type', 'stream'),
                    'Curriculum' => array('id', 'name', 'type_credit', 'year_introduced', 'active'),
                    'order' => array(
                        'Student.academicyear' => 'DESC',
                        'Student.studentnumber' => 'ASC',
                        'Student.id' => 'ASC',
                        'Student.first_name' => 'ASC',
                        'Student.middle_name' => 'ASC',
                        'Student.last_name' => 'ASC',
                        'Student.program_id' => 'ASC',
                        'Student.program_type_id' => 'ASC'
                    ),
                ),
                'YearLevel',
                'CourseDrop',
                'PublishedCourse' => array(
                    //'Course',
                    'Course' => array(
                        'Curriculum' => array('id', 'name', 'type_credit', 'year_introduced', 'active'),
                        //'fields' => array('id', 'course_title', 'course_code', 'credit', 'lecture_hours', 'tutorial_hours', 'laboratory_hours')
                    ),
                    'Program' => array('id', 'name', 'shortname'),
                    'ProgramType' => array('id', 'name', 'shortname'),
                    'Department' => array('id', 'name', 'type'),
                    'College' => array('id', 'name', 'shortname', 'type', 'stream'),
                    'Section' => array(
                        'fields' => array('id', 'name', 'academicyear', 'archive'),
                        'YearLevel' => array('id', 'name'),
                        'Program' => array('id', 'name', 'shortname'),
                        'ProgramType' => array('id', 'name', 'shortname'),
                        'Department' => array('id', 'name', 'type'),
                        'College' => array('id', 'name', 'shortname', 'type', 'stream'),
                        'Curriculum' => array('id', 'name', 'type_credit', 'year_introduced', 'active'),
                    ),
                    'YearLevel' => array('id', 'name'),
                )
            ),
            'order' => array(
                'CourseRegistration.year_level_id' => 'ASC',
                'CourseRegistration.academic_year' => 'DESC',
                'CourseRegistration.semester' => 'DESC',
                'CourseRegistration.published_course_id' => 'ASC',
                'CourseRegistration.id' => 'DESC'
            )
            //'order' => array('CourseRegistration.created' => 'DESC')
        );

        $pre_college_id_via_dept_select_option = 0;

        if ((isset($this->request->data['generateRegisteredList']) && !empty($this->request->data['generateRegisteredList']))) {
            $options['group'] = array('CourseRegistration.student_id');
        }

        if (!empty($academic_year) && !empty($semester)) {
            $this->request->data['Search']['academic_year'] = str_replace('-', '/', $academic_year);
            $this->request->data['Search']['semester'] = $semester;
            if (!ALLOW_GRADE_REPORT_PDF_DOWNLOAD_CURRENT_SEMESTER_ONLY) {
                $this->request->data['search'] = true;
                $this->Session->write('search_data_index', $this->request->data);
                $this->redirect(array('action' => 'index'));
            }
        } else {
            $this->__init_search_index();
        }

        debug($this->request->data);

        if (isset($this->request->data) && !empty($this->request->data['Search'])) {
            if ($this->role_id == ROLE_STUDENT && isset($this->request->data['search'])) {
                $options['conditions'][]['CourseRegistration.student_id'] = $this->student_id;

                if (isset($this->request->data['Search']['semester']) && !empty($this->request->data['Search']['semester'])) {
                    $options['conditions'][] = array('CourseRegistration.semester' => $this->request->data['Search']['semester']);
                }

                if (isset($this->request->data['Search']['academic_year']) && !empty($this->request->data['Search']['academic_year'])) {
                    $options['conditions'][] = array('CourseRegistration.academic_year' => $this->request->data['Search']['academic_year']);
                }
            } else {
                if ($this->role_id != ROLE_STUDENT) {
                    if (!empty($this->request->data['Search']['department_id'])) {
                        $c_or_d = explode('~', $this->request->data['Search']['department_id']);
                        debug(count($c_or_d));
                        debug($c_or_d[0]);

                        if ($c_or_d[0] == 'c') {
                            $pre_college_id_via_dept_select_option = $c_or_d[1];
                            $options['conditions'][]['Student.college_id'] = $c_or_d[1];
                            //$options['conditions'][] =  array('Student.department_id IS NULL');
                        } else {
                            $options['conditions'][]['Student.department_id'] = $this->request->data['Search']['department_id'];
                        }
                        debug($options);
                        //$options['conditions'][]['Student.department_id'] = $this->request->data['Search']['department_id'];
                    } else {
                        if (isset($this->request->data['Search']['college_id']) && !empty($this->request->data['Search']['college_id'])) {
                            if ($this->onlyPre) {
                                $options['conditions'][] = array(
                                    'Student.college_id' => $this->request->data['Search']['college_id'],
                                    'Student.department_id is null'
                                );
                            } else {
                                $options['conditions'][] = array('Student.college_id' => $this->request->data['Search']['college_id']);
                            }
                        }
                    }

                    if (!empty($this->request->data['Search']['program_id'])) {
                        $options['conditions'][]['Student.program_id'] = $this->request->data['Search']['program_id'];
                    }

                    if (!empty($this->request->data['Search']['program_type_id'])) {
                        $options['conditions'][]['Student.program_type_id'] = $this->request->data['Search']['program_type_id'];
                    }

                    if (isset($this->request->data['Search']['semester']) && !empty($this->request->data['Search']['semester'])) {
                        $options['conditions'][] = array('CourseRegistration.semester' => $this->request->data['Search']['semester']);
                    }

                    if (isset($this->request->data['Search']['academic_year']) && !empty($this->request->data['Search']['academic_year'])) {
                        $options['conditions'][] = array('CourseRegistration.academic_year' => $this->request->data['Search']['academic_year']);
                    }


                    if (isset($this->request->data['Search']['section_id']) && !empty($this->request->data['Search']['section_id'])) {
                        if ($pre_college_id_via_dept_select_option) {
                            $options['conditions'][] = array(
                                '
							CourseRegistration.section_id' => $this->request->data['Search']['section_id'],
                                'OR' => array(
                                    'CourseRegistration.year_level_id IS NULL',
                                    'CourseRegistration.year_level_id = 0',
                                    'CourseRegistration.year_level_id = ""',
                                ),
                            );
                        } else {
                            $options['conditions'][] = array('CourseRegistration.section_id' => $this->request->data['Search']['section_id']);
                        }
                    }

                    //filter by student number
                    if (!empty(trim($this->request->data['Search']['studentnumber']))) {
                        $this->request->data['Search']['studentnumber'] = trim(
                            $this->request->data['Search']['studentnumber']
                        );
                        $student_id = $this->CourseRegistration->Student->field(
                            'Student.id',
                            array('Student.studentnumber' => $this->request->data['Search']['studentnumber'])
                        );

                        $dept_ids = array();
                        $coll_ids = array();

                        if (!empty($this->department_ids)) {
                            $dept_ids = $this->department_ids;
                        }

                        if (!empty($this->department_id)) {
                            array_push($dept_ids, $this->department_id);
                        }

                        if (!empty($this->college_ids)) {
                            $coll_ids = $this->college_ids;
                        }

                        if (!empty($this->college_id)) {
                            array_push($coll_ids, $this->college_id);
                        }

                        if (!empty($dept_ids) && !empty($coll_ids) && !$this->onlyPre) {
                            $elegible_user = $this->CourseRegistration->Student->find('count', array(
                                'conditions' => array(
                                    'Student.id' => $student_id,
                                    'OR' => array(
                                        'Student.department_id' => $dept_ids,
                                        'Student.college_id' => $coll_ids
                                    )
                                )
                            ));
                        } else {
                            if (!empty($dept_ids)) {
                                $elegible_user = $this->CourseRegistration->Student->find('count', array(
                                    'conditions' => array(
                                        'Student.id' => $student_id,
                                        'Student.department_id' => $dept_ids
                                    )
                                ));
                            } else {
                                if (!empty($coll_ids)) {
                                    $elegible_user = $this->CourseRegistration->Student->find('count', array(
                                        'conditions' => array(
                                            'Student.id' => $student_id,
                                            'Student.college_id' => $coll_ids
                                        )
                                    ));
                                }
                            }
                        }

                        if ($elegible_user == 0) {
                            $this->Flash->error('You do not have the privilage to view the selected student details');
                            $student_id = null;
                            unset($this->request->data);
                            $this->redirect(array('action' => 'index'));
                        } else {
                            unset($options['conditions']);
                            debug($options);

                            if (!empty($this->department_ids)) {
                                $options['conditions'][]['Student.department_id'] = $this->department_ids;
                            } else {
                                if (!empty($this->college_ids)) {
                                    $options['conditions'][]['Student.college_id'] = $this->college_ids;
                                } else {
                                    if ($pre_college_id_via_dept_select_option) {
                                        $options['conditions'][]['Student.college_id'] = $pre_college_id_via_dept_select_option;
                                        //$options['conditions'][] =  array('Student.department_id IS NULL');
                                    } else {
                                        $options['conditions'][]['Student.department_id'] = $this->request->data['Search']['department_id'];
                                    }
                                }
                            }

                            if (!empty($this->request->data['Search']['studentnumber'])) {
                                $options['conditions'][]['Student.studentnumber'] = trim(
                                    $this->request->data['Search']['studentnumber']
                                );
                            }
                        }
                    }
                }
            }
        } else {
            $options = array();
        }

        if (isset($options['conditions']) && !empty($options['conditions'])) {
            debug($options['conditions']);
            $courseRegistrations = $this->CourseRegistration->find('all', $options);
        } else {
            $courseRegistrations = array();
        }

        if ($this->role_id != ROLE_STUDENT) {
            if (empty($courseRegistrations) && ((isset($options['conditions']) && !empty($options['conditions'])) || isset($this->request->data['search']))) {
                $this->Flash->info('No Course Registration is found with the given search criteria.');
            }
        } else {
            if (empty($courseRegistrations) && ((isset($options['conditions']) && !empty($options['conditions'])) || isset($this->request->data['search']))) {
                if (ALLOW_GRADE_REPORT_PDF_DOWNLOAD_CURRENT_SEMESTER_ONLY) {
                    $this->Flash->info('You can check your latest registration here in Registration or Results tab.');
                    $this->redirect(array('controller' => 'students', 'action' => 'student_academic_profile'));
                } else {
                    $this->Flash->info('No Course Registration is found with the given search criteria.');
                }
            }
        }

        //Generate Slip
        if (isset($this->request->data['generateSlip']) || isset($this->request->data['getGradeReport'])) {
            if (!empty($courseRegistrations)) {
                $student_copies = array();

                $studentnumber = '';

                if (!empty($this->request->data['Search']['studentnumber']) && $this->role_id == ROLE_REGISTRAR) {
                    $studentnumber = $this->request->data['Search']['studentnumber'];

                    if (isset($student_id) && !empty($student_id) && $student_id > 0) {
                        $acSemylist = $this->CourseRegistration->find('all', array(
                            'conditions' => array(
                                'CourseRegistration.student_id' => $student_id,
                            ),
                            'fields' => array('CourseRegistration.academic_year', 'CourseRegistration.semester'),
                            'group' => array(
                                'CourseRegistration.student_id',
                                'CourseRegistration.academic_year',
                                'CourseRegistration.semester'
                            ),
                            'recursive' => -1,
                            'order' => array('CourseRegistration.created' => 'ASC')
                        ));
                    }


                    if (!empty($acSemylist)) {
                        $first_student_id = $count = 0;
                        foreach ($acSemylist as $acskey => $acsemval) {
                            $student_copy = ClassRegistry::init('ExamGrade')->getStudentCopy(
                                $student_id,
                                $acsemval['CourseRegistration']['academic_year'],
                                $acsemval['CourseRegistration']['semester']
                            );

                            if (!empty($student_copy['courses'])) {
                                $student_copy['University'] = ClassRegistry::init('University')->getStudentUnivrsity(
                                    $student_id
                                );
                                //$student_copy['RegistrationDate'] = $v['CourseRegistration']['created'];
                                $student_copies[$count] = $student_copy;
                                $count++;
                            }
                        }
                    }
                } else {
                    if (!empty($courseRegistrations)) {
                        $first_student_id = $courseRegistrations[0]['CourseRegistration']['student_id'];

                        foreach ($courseRegistrations as $k => $v) {
                            if (isset($this->request->data['getGradeReport'])) {
                                $status_generated = 0;
                                $check_status_generated0 = $this->CourseRegistration->Student->StudentExamStatus->get_student_exam_status(
                                    $v['CourseRegistration']['student_id'],
                                    $v['CourseRegistration']['academic_year'],
                                    $v['CourseRegistration']['semester']
                                );

                                $check_status_generated = $this->CourseRegistration->Student->StudentExamStatus->find(
                                    'count',
                                    array(
                                        'conditions' => array(
                                            'StudentExamStatus.student_id' => $v['CourseRegistration']['student_id'],
                                            'StudentExamStatus.academic_year' => $v['CourseRegistration']['academic_year'],
                                            'StudentExamStatus.semester' => $v['CourseRegistration']['semester']
                                        )
                                    )
                                );
                                debug($check_status_generated);

                                if ($check_status_generated) {
                                    if ($this->role_id == ROLE_STUDENT && $check_status_generated) {
                                        // regenerate student Status then generate report
                                        $status_regenetared_check = ClassRegistry::init(
                                            'StudentExamStatus'
                                        )->regenerate_all_status_of_student_by_student_id($this->student_id);

                                        debug($status_regenetared_check);
                                    }

                                    $student_copy = ClassRegistry::init('ExamGrade')->getStudentCopy(
                                        $v['CourseRegistration']['student_id'],
                                        $v['CourseRegistration']['academic_year'],
                                        $v['CourseRegistration']['semester']
                                    );
                                } else {
                                    continue;
                                }
                                //$student_copy = ClassRegistry::init('ExamGrade')->getStudentCopy($v['CourseRegistration']['student_id'], $v['CourseRegistration']['academic_year'], $v['CourseRegistration']['semester']);
                            } else {
                                $student_copy = ClassRegistry::init('ExamGrade')->getStudentCopy(
                                    $v['CourseRegistration']['student_id'],
                                    $v['CourseRegistration']['academic_year'],
                                    $v['CourseRegistration']['semester']
                                );
                            }

                            if (!empty($student_copy['courses'])) {
                                $student_copy['University'] = ClassRegistry::init('University')->getStudentUnivrsity(
                                    $v['CourseRegistration']['student_id']
                                );
                                $student_copy['RegistrationDate'] = $v['CourseRegistration']['created'];
                                $student_copies[$v['CourseRegistration']['student_id']] = $student_copy;
                            }
                        }
                    }
                }

                if ($this->role_id == ROLE_STUDENT) {
                    $studentnumber = $this->request->data['Search']['studentnumber'];
                }


                if (!empty($student_copies)) {
                    $this->set(compact('student_copies', 'studentnumber', 'first_student_id'));
                    $this->response->type('application/pdf');
                    $this->layout = '/pdf/default';

                    if (isset($this->request->data['generateSlip'])) {
                        $this->render('register_slip_pdf');
                    } else {
                        if (isset($this->request->data['getGradeReport'])) {
                            $this->render('grade_report_pdf');
                        }
                    }
                    return;
                } else {
                    $this->Flash->error('ERROR: No Data to Export to PDF!.');
                    $this->redirect(array('action' => 'index'));
                }
            } else {
                $this->Flash->info('No Course Registration is found with the given search criteria.');
            }
        }

        //Generate Registered List
        if (isset($this->request->data['generateRegisteredList'])) {
            if (!empty($courseRegistrations)) {
                //$this->paginate['limit']=800;

                $departmentName = $this->CourseRegistration->Student->Department->field(
                    'Department.name',
                    array('Department.id' => $this->request->data['Search']['department_id'])
                );

                if (empty($departmentName)) {
                    $departmentName = 'Pre/Freshman';
                }

                $programName = $this->CourseRegistration->Student->Program->field(
                    'Program.name',
                    array('Program.id' => $this->request->data['Search']['program_id'])
                );
                $programTypeName = $this->CourseRegistration->Student->ProgramType->field(
                    'ProgramType.name',
                    array('ProgramType.id' => $this->request->data['Search']['program_type_id'])
                );

                $sectionDetail = $this->CourseRegistration->Section->find(
                    'first',
                    array(
                        'conditions' => array('Section.id' => $this->request->data['Search']['section_id']),
                        'contain' => array('YearLevel')
                    )
                );

                if ($sectionDetail['Section']['program_id'] == PROGRAM_REMEDIAL) {
                    $departmentName = 'Remedial Program';
                }

                $sectionName = $sectionDetail['Section']['name'] . '(' . (!empty($sectionDetail['YearLevel']['name']) ? $sectionDetail['YearLevel']['name'] : ($sectionDetail['Section']['program_id'] == PROGRAM_REMEDIAL ? 'Remedial' : 'Pre/1st')) . ')';
                $registrationFormated[$this->request->data['Search']['academic_year'] . '~' . $this->request->data['Search']['semester'] . '~' . $departmentName . '~' . $programName . '~' . $programTypeName . '~' . $sectionName] = $courseRegistrations;

                $students_in_registration_list_pdf = $registrationFormated;

                if (!empty($students_in_registration_list_pdf)) {
                    $this->set(compact('students_in_registration_list_pdf'));
                    $this->response->type('application/pdf');
                    $this->layout = '/pdf/default';
                    $this->render('registeration_list_pdf');
                    return;
                } else {
                    $this->Flash->error('ERROR: No Data to Export to PDF!.');
                    $this->redirect(array('action' => 'index'));
                }
            } else {
                return array();
                //$this->Flash->info('No result found for the given search criteria.');
            }
        }

        $programs = $this->CourseRegistration->Student->Program->find(
            'list',
            array('conditions' => array('Program.id' => $this->program_ids))
        );
        $programTypes = $this->CourseRegistration->Student->ProgramType->find(
            'list',
            array('conditions' => array('ProgramType.id' => $this->program_type_ids))
        );

        if ($this->role_id == ROLE_REGISTRAR || ROLE_REGISTRAR == $this->Session->read(
                'Auth.User'
            )['Role']['parent_id']) {
            if (!empty($this->department_ids)) {
                //$departments = $this->CourseRegistration->Student->Department->allDepartmentInCollegeIncludingPre($this->department_ids, null);
                $departments = $this->CourseRegistration->Student->Department->find(
                    'list',
                    array(
                        'conditions' => array('Department.id' => $this->department_ids, 'Department.active' => 1),
                        'order' => array('Department.college_id' => 'ASC', 'Department.name' => 'ASC')
                    )
                );
            } else {
                if (!empty($this->college_ids)) {
                    $colleges = $this->CourseRegistration->Student->College->find(
                        'list',
                        array('conditions' => array('College.id' => $this->college_ids, 'College.active' => 1))
                    );
                }
            }

            if ($this->Session->read('Auth.User')['is_admin'] == 1) {
                $programs = $this->CourseRegistration->Student->Program->find('list');
                $programTypes = $this->CourseRegistration->Student->ProgramType->find('list');
            } else {
                $programs = $this->CourseRegistration->Student->Program->find(
                    'list',
                    array('conditions' => array('Program.id' => $this->program_id))
                );
                $programTypes = $this->CourseRegistration->Student->ProgramType->find(
                    'list',
                    array('conditions' => array('ProgramType.id' => $this->program_type_id))
                );
            }
        } else {
            if ($this->role_id == ROLE_COLLEGE) {
                $departments = $this->CourseRegistration->Student->Department->allDepartmentInCollegeIncludingPre(
                    null,
                    $this->college_id,
                    1,
                    1
                );
            } else {
                if ($this->role_id == ROLE_DEPARTMENT) {
                    $departments = $this->CourseRegistration->Student->Department->find(
                        'list',
                        array('conditions' => array('Department.id' => $this->department_id))
                    );
                }
            }
        }

        isset($departments) ? debug($departments) : '';

        if (!empty($this->request->data)) {
            if (!empty($this->request->data['Search']['college_id'])) {
                $sections = $this->CourseRegistration->Section->find('all', array(
                    'conditions' => array(
                        'Section.program_id' => $this->request->data['Search']['program_id'],
                        'Section.program_type_id' => $this->request->data['Search']['program_type_id'],
                        'Section.academicyear' => $this->request->data['Search']['academic_year'],
                        'Section.college_id' => $this->request->data['Search']['college_id'],
                        'Section.department_id IS NULL',
                        ($this->onlyPre || (count(
                                explode('c~', $this->request->data['Search']['department_id'])
                            ) == 2) ? 'Section.department_id IS NULL' : 'Section.department_id IS NOT NULL OR Section.department_id IS NULL OR Section.department_id = ""'),
                        'Section.archive' => 0,
                        //'Section.created >=' => date("Y-m-d 23:59:59", strtotime("-" . ACY_BACK_COURSE_REGISTRATION . " year")),
                    ),
                    'contain' => array(
                        'Program' => array('id', 'name'),
                        'ProgramType' => array('id', 'name'),
                        'Department' => array('id', 'name'),
                        'YearLevel' => array('id', 'name'),
                        'College' => array('id', 'name'),
                    ),
                    'order' => array(
                        'Section.college_id' => 'ASC',
                        'Section.department_id' => 'ASC',
                        'Section.year_level_id' => 'ASC',
                        'Section.id' => 'ASC',
                        'Section.name' => 'ASC'
                    )
                ));
            } else {
                if (!empty($this->request->data['Search']['department_id'])) {
                    debug(count(explode('c~', $this->request->data['Search']['department_id'])) == 2);

                    if ((count(
                            explode('c~', $this->request->data['Search']['department_id'])
                        ) == 2) /* || !empty($this->college_id) || !empty($this->college_ids) */) {
                        $sections = $this->CourseRegistration->Section->find('all', array(
                            'conditions' => array(
                                'Section.program_id' => $this->request->data['Search']['program_id'],
                                'Section.program_type_id' => $this->request->data['Search']['program_type_id'],
                                'Section.academicyear' => $this->request->data['Search']['academic_year'],
                                'Section.college_id' => ((count(
                                        explode('c~', $this->request->data['Search']['department_id'])
                                    ) == 2) ? (explode(
                                    'c~',
                                    $this->request->data['Search']['department_id']
                                )[1]) : ($this->role_id == ROLE_COLLEGE ? $this->college_id : array_values(
                                    $this->college_ids
                                )[0])),
                                ($this->onlyPre || (count(
                                        explode('c~', $this->request->data['Search']['department_id'])
                                    ) == 2) ? 'Section.department_id IS NULL' : 'Section.department_id IS NOT NULL'),
                                //'Section.archive' => 0,
                                //'Section.created >=' => date("Y-m-d 23:59:59", strtotime("-" . ACY_BACK_COURSE_REGISTRATION . " year")),
                            ),
                            'contain' => array(
                                'Program' => array('id', 'name'),
                                'ProgramType' => array('id', 'name'),
                                'Department' => array('id', 'name'),
                                'YearLevel' => array('id', 'name'),
                                'College' => array('id', 'name'),
                            ),
                            'order' => array(
                                'Section.college_id' => 'ASC',
                                'Section.department_id' => 'ASC',
                                'Section.year_level_id' => 'ASC',
                                'Section.id' => 'ASC',
                                'Section.name' => 'ASC'
                            )
                        ));
                        //debug($sections);
                    } else {
                        if ((count(explode('c~', $this->request->data['Search']['department_id'])) != 2)) {
                            $sections = $this->CourseRegistration->Section->find('all', array(
                                'conditions' => array(
                                    'Section.program_id' => $this->request->data['Search']['program_id'],
                                    'Section.program_type_id' => $this->request->data['Search']['program_type_id'],
                                    'Section.academicyear' => $this->request->data['Search']['academic_year'],
                                    'Section.department_id' => $this->request->data['Search']['department_id'],
                                    //'Section.archive' => 0,
                                    //'Section.created >=' => date("Y-m-d 23:59:59", strtotime("-" . ACY_BACK_COURSE_REGISTRATION . " year")),
                                ),
                                'contain' => array(
                                    'Program' => array('id', 'name'),
                                    'ProgramType' => array('id', 'name'),
                                    'Department' => array('id', 'name'),
                                    'YearLevel' => array('id', 'name'),
                                    'College' => array('id', 'name'),
                                ),
                                'order' => array(
                                    'Section.year_level_id' => 'ASC',
                                    'Section.college_id' => 'ASC',
                                    'Section.department_id' => 'ASC',
                                    'Section.id' => 'ASC',
                                    'Section.name' => 'ASC'
                                )
                            ));
                            //debug($sections);
                        }
                    }
                }
            }
        } else {
            if (empty($this->request->data) && $this->role_id != ROLE_STUDENT) {
                if ((!empty($this->college_ids) && $this->role_id != ROLE_DEPARTMENT) || (!empty($this->college_id) && $this->role_id == ROLE_COLLEGE)) {
                    $sections = $this->CourseRegistration->Section->find('all', array(
                        'conditions' => array(
                            'Section.program_id' => (array_values($this->program_ids)[0]),
                            'Section.program_type_id' => (array_values($this->program_type_ids)[0]),
                            'Section.academicyear' => $this->AcademicYear->current_academicyear(),
                            'Section.college_id' => ($this->role_id == ROLE_COLLEGE ? $this->college_id : (array_values(
                                $this->college_ids
                            )[0])),
                            (($this->onlyPre || $this->role_id == ROLE_COLLEGE) ? 'Section.department_id IS NULL' : 'Section.department_id LIKE "%"'),
                            //'Section.archive' => 0,
                            //'Section.created >=' => date("Y-m-d 23:59:59", strtotime("-" . ACY_BACK_COURSE_REGISTRATION . " year")),
                        ),
                        'contain' => array(
                            'Program' => array('id', 'name'),
                            'ProgramType' => array('id', 'name'),
                            'Department' => array('id', 'name'),
                            'YearLevel' => array('id', 'name'),
                            'College' => array('id', 'name'),
                        ),
                        'order' => array(
                            'Section.year_level_id' => 'ASC',
                            'Section.college_id' => 'ASC',
                            'Section.department_id' => 'ASC',
                            'Section.id' => 'ASC',
                            'Section.name' => 'ASC'
                        )
                    ));
                    //debug($sections);
                } else {
                    if ((!empty($this->department_ids) && $this->role_id != ROLE_COLLEGE) || (!empty($this->department_id) && $this->role_id == ROLE_DEPARTMENT)) {
                        $sections = $this->CourseRegistration->Section->find('all', array(
                            'conditions' => array(
                                'Section.program_id' => (array_values($this->program_ids)[0]),
                                'Section.program_type_id' => (array_values($this->program_type_ids)[0]),
                                'Section.academicyear' => $this->AcademicYear->current_academicyear(),
                                'Section.department_id' => ($this->role_id == ROLE_DEPARTMENT ? $this->department_id : (array_values(
                                    $this->department_ids
                                )[0])),
                                //'Section.archive' => 0,
                                //'Section.created >=' => date("Y-m-d 23:59:59", strtotime("-" . ACY_BACK_COURSE_REGISTRATION . " year")),
                            ),
                            'contain' => array(
                                'Program' => array('id', 'name'),
                                'ProgramType' => array('id', 'name'),
                                'Department' => array('id', 'name'),
                                'YearLevel' => array('id', 'name'),
                                'College' => array('id', 'name'),
                            ),
                            'order' => array(
                                'Section.year_level_id' => 'ASC',
                                'Section.college_id' => 'ASC',
                                'Section.department_id' => 'ASC',
                                'Section.id' => 'ASC',
                                'Section.name' => 'ASC'
                            )
                        ));
                        //debug($sections);
                    }
                }
            }
        }

        if ($this->role_id != ROLE_STUDENT && !empty($sections)) {
            $sectionOrganizedByYearLevel = array();

            if (!empty($sections)) {
                foreach ($sections as $k => $v) {
                    if (!empty($v['YearLevel']['name'])) {
                        $sectionOrganizedByYearLevel[$v['Section']['id']] = $v['Section']['name'] . " (" . $v['Section']['academicyear'] . ", " . $v['YearLevel']['name'] . ")";
                    } else {
                        $sectionOrganizedByYearLevel[$v['Section']['id']] = $v['Section']['name'] . " (" . $v['Section']['academicyear'] . "," . ($v['Section']['program_id'] == PROGRAM_REMEDIAL ? ' Remedial' : ' Pre/1st') . ")";
                    }
                }
            } else {
                $sectionOrganizedByYearLevel['-1'] = '[ No Results, Try Changing Search Filters ]';
            }

            $sections = $sectionOrganizedByYearLevel;

            //debug($sections);
            $this->set(compact('sections'));
        } else {
            $sections['-1'] = '[ No Results, Try Changing Search Filters ]';
            $sections = array();
            debug($sections);
            $this->set(compact('sections'));
        }

        if ($this->role_id == ROLE_STUDENT) {
            $student_ay_s_list = $this->CourseRegistration->ExamGrade->getListOfAyAndSemester($this->student_id);
            $acadamic_years = array();

            if (!empty($student_ay_s_list)) {
                $status_generated_acy_semester = array();

                foreach ($student_ay_s_list as $key => $ay_s) {
                    $acadamic_years[$ay_s['academic_year']] = $ay_s['academic_year'];

                    $check_status_generated = $this->CourseRegistration->Student->StudentExamStatus->find(
                        'count',
                        array(
                            'conditions' => array(
                                'StudentExamStatus.student_id' => $this->student_id,
                                'StudentExamStatus.academic_year' => $ay_s['academic_year'],
                                'StudentExamStatus.semester' => $ay_s['semester']
                            )
                        )
                    );
                    debug($check_status_generated);

                    if ($check_status_generated) {
                        if (ALLOW_GRADE_REPORT_PDF_DOWNLOAD_CURRENT_SEMESTER_ONLY) {
                            // to allow only the last active semester only no other choices, and only if status is generated
                            $status_generated_acy_semester['academic_year'] = $ay_s['academic_year'];
                            $status_generated_acy_semester['semester'] = $ay_s['semester'];
                        } else {
                            // to allow grade report pdf download for any semester selected if status is generated
                            if (isset($this->request->data['Search']) && $this->request->data['Search']['academic_year'] == $ay_s['academic_year'] && $this->request->data['Search']['semester'] == $ay_s['semester']) {
                                $status_generated_acy_semester['academic_year'] = $ay_s['academic_year'];
                                $status_generated_acy_semester['semester'] = $ay_s['semester'];
                            }
                        }
                    }
                }

                $this->set(compact('status_generated_acy_semester'));
            } elseif (isset($this->request->data['Search']) && !empty($this->request->data['Search']['academic_year'])) {
                $acadamic_years[$this->request->data['Search']['academic_year']] = $this->request->data['Search']['academic_year'];
            }

            $this->set(compact('acadamic_years'));
        }

        $this->set(compact('courseRegistrations'));

        $this->set(compact('departments', 'colleges', 'acyear_array_data', 'programs', 'colleges', 'programTypes'));

        $this->render('view_registration');
    }

    //department_id+'~'+college_id+'~'+academic_year+'~'+semester+'~'+program_id+'~'+program_type_id+'d'+'~'+year_level_name
    public function get_section_combo($paramaters)
    {

        $this->layout = 'ajax';
        $criteriaLists = explode('~', $paramaters);
        debug($criteriaLists);

        if (!empty($criteriaLists) && count($criteriaLists) > 4) {
            $department_college_id = $criteriaLists[0];
            $academicYear = str_replace('-', '/', $criteriaLists[1]);
            $program_id = $criteriaLists[2];
            $program_type_id = $criteriaLists[3];
            $type = $criteriaLists[4];
            $ylname = $criteriaLists[5];

            $options = array(
                'conditions' => array(
                    'Section.academicyear' => $academicYear,
                    'Section.program_id' => $program_id,
                    'Section.program_type_id' => $program_type_id,
                    'Section.archive' => 0,
                ),
                'contain' => array(
                    'Program',
                    'ProgramType',
                    'Department',
                    'YearLevel',
                    'College'
                ),
                'order' => array(
                    'Section.year_level_id' => 'ASC',
                    'Section.college_id' => 'ASC',
                    'Section.department_id' => 'ASC',
                    'Section.id' => 'ASC',
                    'Section.name' => 'ASC'
                )
            );

            if ($type == 'c') {
                $options['conditions'][] = array(
                    'Section.college_id' => $department_college_id,
                    'Section.department_id IS NULL'
                );
            } else {
                $options['conditions'][] = array(
                    'Section.department_id' => $department_college_id,
                    'YearLevel.name LIKE ' => $ylname,
                );
            }

            debug($options);

            $sections = $this->CourseRegistration->Section->find('all', $options);
            $sectionOrganizedByYearLevel = array();

            if (!empty($sections)) {
                $sectionOrganizedByYearLevel[''] = '[ Select Section ]';
                foreach ($sections as $k => $v) {
                    if (!empty($v['YearLevel']['name'])) {
                        $sectionOrganizedByYearLevel[$v['Section']['id']] = $v['Section']['name'] . " (" . $v['Section']['academicyear'] . ", " . $v['YearLevel']['name'] . ")";
                    } else {
                        $sectionOrganizedByYearLevel[$v['Section']['id']] = $v['Section']['name'] . " (" . $v['Section']['academicyear'] . ", " . ($v['Section']['program_id'] == PROGRAM_REMEDIAL ? ' Remedial' : ' Pre/1st') . ")";
                    }
                }
            } else {
                $sectionOrganizedByYearLevel[''] = '[ No Active Sections, Try Changing Filters ]';
            }
        }

        $this->set(compact('sectionOrganizedByYearLevel'));
    }

    //department_id+'~'+college_id+'~'+academic_year+'~'+semester+'~'+program_id+'~'+program_type_id+'d'
    public function get_section_combo_for_view($paramaters)
    {

        $this->layout = 'ajax';
        $criteriaLists = explode('~', $paramaters);
        debug($criteriaLists);

        if (!empty($criteriaLists) && count($criteriaLists) > 4) {
            $c_or_d = explode('~', $criteriaLists[0]);

            if ($c_or_d[0] == 'c') {
                $department_college_id = $criteriaLists[1];
                $academicYear = str_replace('-', '/', $criteriaLists[2]);
                $program_id = $criteriaLists[3];
                $program_type_id = $criteriaLists[4];
                $type = $criteriaLists[5];
            } else {
                $department_college_id = $criteriaLists[0];
                $academicYear = str_replace('-', '/', $criteriaLists[1]);
                $program_id = $criteriaLists[2];
                $program_type_id = $criteriaLists[3];
                $type = $criteriaLists[4];
            }

            $options = array(
                'conditions' => array(
                    'Section.academicyear' => $academicYear,
                    'Section.program_id' => $program_id,
                    'Section.program_type_id' => $program_type_id,
                    //'Section.archive' => 0,
                    'Section.created >=' => date(
                        "Y-m-d 23:59:59",
                        strtotime("-" . ACY_BACK_COURSE_REGISTRATION . " year")
                    ),
                ),
                'contain' => array(
                    'Program',
                    'ProgramType',
                    'Department',
                    'YearLevel',
                    'College',
                ),
                'order' => array(
                    'Section.year_level_id' => 'ASC',
                    'Section.college_id' => 'ASC',
                    'Section.department_id' => 'ASC',
                    'Section.id' => 'ASC',
                    'Section.name' => 'ASC'
                )
            );

            if ($type == 'c') {
                $options['conditions'][] = array(
                    'Section.college_id' => $department_college_id,
                    'Section.department_id IS NULL',
                );
            } else {
                if ($c_or_d[0] == 'c') {
                    $options['conditions'][] = array(
                        'Section.college_id' => $department_college_id,
                        'Section.department_id IS NULL',
                    );
                } else {
                    $options['conditions'][] = array(
                        'Section.department_id' => $department_college_id,
                        //'YearLevel.name LIKE ' => $ylname,
                    );
                }
            }

            debug($options);

            $sections = $this->CourseRegistration->Section->find('all', $options);
            $sectionOrganizedByYearLevel = array();

            if (!empty($sections)) {
                foreach ($sections as $k => $v) {
                    if (!empty($v['YearLevel']['name'])) {
                        $sectionOrganizedByYearLevel[$v['Section']['id']] = $v['Section']['name'] . " (" . $v['Section']['academicyear'] . ", " . $v['YearLevel']['name'] . ")";
                    } else {
                        $sectionOrganizedByYearLevel[$v['Section']['id']] = $v['Section']['name'] . " (" . $v['Section']['academicyear'] . "," . ($v['Section']['program_id'] == PROGRAM_REMEDIAL ? ' Remedial' : ' Pre/1st') . ")";
                    }
                }
            } else {
                $sectionOrganizedByYearLevel['-1'] = '[ No Results, Try Changing Search Filters ]';
            }
        }

        $this->set(compact('sectionOrganizedByYearLevel'));
    }

    public function manage_missing_registration($student_id)
    {

        $this->layout = 'ajax';

        $academicYearList = array();

        $current_acy = $this->AcademicYear->current_academicyear();

        $student_admission_year = $this->CourseRegistration->Student->field(
            'academicyear',
            array('Student.id' => $student_id)
        );

        $start_yr = $end_yr = $current_yr = (explode('/', $current_acy)[0]);

        if (!empty($student_admission_year)) {
            $start_yr = (explode('/', $student_admission_year)[0]);

            if (empty($start_yr)) {
                $start_yr = $current_yr;
            }

            $academicYearList = $this->AcademicYear->academicYearInArray($start_yr, $current_yr);

            // TO DO:  check if the student is dismissed and not readmitted and set $end_year to last year the student is dismissed from student_exam_statuses, not very much important now since Maintain Missing registrarion Link is not vissible if the student is dismissed and not readmitted since then.

            if ($this->Session->read('Auth.User')['role_id'] == ROLE_REGISTRAR && $this->Session->read(
                    'Auth.User'
                )['is_admin'] != 1 && is_numeric(
                    RESTRICT_NON_ADMIN_REGISTRAR_TO_ACY_BACK_COURSE_REGISTRATION
                ) && RESTRICT_NON_ADMIN_REGISTRAR_TO_ACY_BACK_COURSE_REGISTRATION) {
                if (is_numeric(ACY_BACK_COURSE_REGISTRATION) && ACY_BACK_COURSE_REGISTRATION > 0 && count(
                        $academicYearList
                    ) > ACY_BACK_COURSE_REGISTRATION) {
                    $adjustAcademicYearRangeBasedOnRole = new $this->AcademicYear(new ComponentCollection());
                    if ($start_yr > ($current_yr - ACY_BACK_COURSE_REGISTRATION)) {
                        //start from student admitted year
                        $academicYearList = $adjustAcademicYearRangeBasedOnRole->academicYearInArray(
                            $start_yr,
                            $end_yr
                        );
                    } else {
                        //start from the allowed years back by substructiong the years back from current academic year
                        $start_yr = ($current_yr - ACY_BACK_COURSE_REGISTRATION);
                        $academicYearList = $adjustAcademicYearRangeBasedOnRole->academicYearInArray(
                            $start_yr,
                            $end_yr
                        );
                    }
                }
            }

            if (empty($academicYearList)) {
                $academicYearList[$current_acy] = $current_acy;
            }
        } else {
            $academicYearList[$current_acy] = $current_acy;
        }

        $studentID = $student_id;

        $this->set(compact('academicYearList', 'studentID'));
    }

    public function update_missing_registration()
    {

        $selected_student_id = null;

        if (!empty($this->request->data) && $this->Session->read('Auth.User')['role_id'] == ROLE_REGISTRAR) {
            $selectedStudentDetail = ClassRegistry::init('Student')->find(
                'first',
                array(
                    'conditions' => array('Student.id' => $this->request->data['Student']['selected_student_id']),
                    'recursive' => -1
                )
            );
            $ngGradeDeleationList = array();
            $courseAddandRegistrationExamGradeIds = array();

            $selected_student_id = $selectedStudentDetail['Student']['id'];
            $have_atleast_one_NG_with_assesment = false;
            $generated_student_exam_status = false;

            //cancel ng
            if (isset($this->request->data) && !empty($this->request->data['cancelNG'])) {
                //debug($this->request->data);
                $count = 0;
                foreach ($this->request->data['CourseRegistration'] as $key => $student) {
                    if (isset($student['gp']) && $student['gp'] == 1 && $student['grade'] == 'NG' && isset($student['ng_grade_with_assesment']) && $student['ng_grade_with_assesment'] != 1) {
                        $isThereAnyGradeChange = $this->CourseRegistration->ExamGrade->find('first', array(
                            'conditions' => array(
                                'ExamGrade.course_registration_id' => (isset($student['course_registration_id']) ? $student['course_registration_id'] : $student['id']),
                            ),
                            'contain' => array('ExamGradeChange'),
                            'recursive' => -1
                        ));

                        debug($isThereAnyGradeChange);

                        //I noticed There are some some grades changes from NG grades, Neway
                        if (isset($student['gp']) && $student['gp'] == 1 && !empty($student['id']) && !empty($selectedStudentDetail) && empty($isThereAnyGradeChange['ExamGradeChange'])) {
                            $ngGradeDeleationList['ExamGrade'][] = $student['id'];
                        }

                        //debug($student);

                        $tmp = $this->CourseRegistration->ExamGrade->find('first', array(
                            'conditions' => array('ExamGrade.id' => $student['grade_id']),
                            'contain' => array(
                                'CourseAdd' => array(
                                    'PublishedCourse' => array(
                                        'fields' => array('id', 'course_id'),
                                        'Course' => array('id', 'course_title', 'course_code', 'credit'),
                                    ),
                                    'ExamResult' => array(
                                        'conditions' => array(
                                            'ExamResult.course_add' => 0
                                        ),
                                        'limit' => 1
                                    ),
                                ),
                                'CourseRegistration' => array(
                                    'PublishedCourse' => array(
                                        'fields' => array('id', 'course_id'),
                                        'Course' => array('id', 'course_title', 'course_code', 'credit'),
                                    ),
                                    'ExamResult' => array(
                                        'limit' => 1
                                    ),
                                ),
                                'MakeupExam' => array(
                                    'PublishedCourse' => array(
                                        'fields' => array('id', 'course_id'),
                                        'Course' => array('id', 'course_title', 'course_code', 'credit'),
                                    ),
                                    'ExamResult' => array(
                                        'limit' => 1
                                    ),
                                ),
                                'ExamGradeChange'
                            )
                        ));

                        debug($tmp);

                        if (empty($tmp['ExamGradeChange'])) {
                            if (isset($tmp['CourseRegistration']) && !empty($tmp['CourseRegistration']) && $tmp['ExamGrade']['grade'] == 'NG' && empty($tmp['CourseRegistration']['ExamResult'])) {
                                $courseAddandRegistrationExamGradeIds['CourseRegistration'][] = $tmp['CourseRegistration']['id'];
                                $courseAddandRegistrationExamGradeIds['ExamGrade'][] = $tmp['ExamGrade']['id'];
                                $selected_student_id = $tmp['CourseRegistration']['student_id'];
                            } else {
                                if (isset($tmp['CourseAdd']) && !empty($tmp['CourseAdd']['id']) && $tmp['ExamGrade']['grade'] == 'NG' && empty($tmp['CourseAdd']['ExamResult'])) {
                                    $courseAddandRegistrationExamGradeIds['CourseAdd'][] = $tmp['CourseAdd']['id'];
                                    $courseAddandRegistrationExamGradeIds['ExamGrade'][] = $tmp['ExamGrade']['id'];
                                    $selected_student_id = $tmp['CourseAdd']['student_id'];
                                } else {
                                    if (isset($tmp['MakeupExam']) && !empty($tmp['MakeupExam']['id']) && $tmp['ExamGrade']['grade'] == 'NG' && empty($tmp['MakeupExam']['ExamResult'])) {
                                        $courseAddandRegistrationExamGradeIds['MakeupExam'][] = $tmp['MakeupExam']['id'];
                                        $courseAddandRegistrationExamGradeIds['ExamGrade'][] = $tmp['ExamGrade']['id'];
                                        $selected_student_id = $tmp['MakeupExam']['student_id'];
                                    }
                                }
                            }
                        }
                    } else {
                        if (isset($student['ng_grade_with_assesment']) && $student['ng_grade_with_assesment'] == 1) {
                            $have_atleast_one_NG_with_assesment = true;
                        }
                    }
                }

                debug($courseAddandRegistrationExamGradeIds);
                //exit();

                /* if (!empty($ngGradeDeleationList['ExamGrade'])) {
					if ($this->CourseRegistration->ExamGrade->deleteAll(array('ExamGrade.course_registration_id' => $ngGradeDeleationList['ExamGrade']), false)) {
						$this->Flash->success('NG Cancellation for ' . ($selectedStudentDetail['Student']['full_name'] . ' ('. $selectedStudentDetail['Student']['studentnumber'] . ')' ). ' was successful for ' . (count($ngGradeDeleationList['ExamGrade'])) . ' ' .  (count($ngGradeDeleationList['ExamGrade']) > 1 ? 'courses':'course') . '.');
						$this->redirect(array('controller' => 'students', 'action' => 'student_academic_profile', (isset($selectedStudentDetail['Student']['id']) ? $selectedStudentDetail['Student']['id'] : $this->request->data['Student']['selected_student_id'])));
					}
				} else {
					$this->Flash->error('The selected NG Grade(s) could not be cancelled for ' . ($selectedStudentDetail['Student']['full_name'] . ' ('. $selectedStudentDetail['Student']['studentnumber'] . ')' ). '. Check if there is a grade change from the selected NG Grade.');
					$this->redirect(array('controller' => 'students', 'action' => 'student_academic_profile', (isset($selectedStudentDetail['Student']['id']) ? $selectedStudentDetail['Student']['id'] : $this->request->data['Student']['selected_student_id'])));
				} */


                if (isset($courseAddandRegistrationExamGradeIds['ExamGrade']) && !empty($courseAddandRegistrationExamGradeIds['ExamGrade']) && !$have_atleast_one_NG_with_assesment) {
                    debug($courseAddandRegistrationExamGradeIds);
                    if (!empty($courseAddandRegistrationExamGradeIds['CourseRegistration'])) {
                        if (!$have_atleast_one_NG_with_assesment) {
                            // there is no grade with NG from the selected courses for student so better to registration and grade,
                            // helpful for students registered with mass registration but donot attanted any course. course registration can be maintained when needed
                            if ($this->CourseRegistration->deleteAll(
                                array('CourseRegistration.id' => $courseAddandRegistrationExamGradeIds['CourseRegistration']),
                                false
                            )) {
                                $this->CourseRegistration->ExamGrade->deleteAll(
                                    array('ExamGrade.id' => $courseAddandRegistrationExamGradeIds['ExamGrade']),
                                    false
                                );
                                if (!empty($selected_student_id) && $selected_student_id > 0) {
                                    $generated_student_exam_status = $this->CourseRegistration->Student->StudentExamStatus->regenerate_all_status_of_student_by_student_id(
                                        $selected_student_id,
                                        0
                                    );
                                }
                                $this->Flash->success(
                                    'You have cancelled ' . (count(
                                        $courseAddandRegistrationExamGradeIds['ExamGrade']
                                    )) . ' NG grades and course registration.' . (!empty($generated_student_exam_status) && $generated_student_exam_status ? ' Student academic status is also regenerated.' : '')
                                );
                                $this->redirect(
                                    array(
                                        'controller' => 'students',
                                        'action' => 'student_academic_profile',
                                        (isset($selected_student_id) ? $selected_student_id : $this->request->data['Student']['selected_student_id'])
                                    )
                                );
                            }
                        } else {
                            if (DELETE_ASSESMENT_AND_ASSOCIATED_RECORDS_ON_NG_CANCELATION) {
                                // original
                                if ($this->CourseRegistration->deleteAll(
                                    array('CourseRegistration.id' => $courseAddandRegistrationExamGradeIds['CourseRegistration']),
                                    false
                                )) {
                                    $this->CourseRegistration->ExamGrade->deleteAll(
                                        array('ExamGrade.id' => $courseAddandRegistrationExamGradeIds['ExamGrade']),
                                        false
                                    );
                                    if (!empty($selected_student_id) && $selected_student_id > 0) {
                                        $generated_student_exam_status = $this->CourseRegistration->Student->StudentExamStatus->regenerate_all_status_of_student_by_student_id(
                                            $selected_student_id,
                                            0
                                        );
                                    }
                                    $this->Flash->success(
                                        'You have cancelled ' . (count(
                                            $courseAddandRegistrationExamGradeIds['ExamGrade']
                                        )) . ' NG grades and course registration.' . (!empty($generated_student_exam_status) && $generated_student_exam_status ? ' Student academic status is also regenerated.' : '')
                                    );
                                    $this->redirect(
                                        array(
                                            'controller' => 'students',
                                            'action' => 'student_academic_profile',
                                            (isset($selected_student_id) ? $selected_student_id : $this->request->data['Student']['selected_student_id'])
                                        )
                                    );
                                }
                            } else {
                                // check if there is continues assesment and delete only Exam Grade and Exam Grade Changes if any, Neway
                                if ($this->CourseRegistration->ExamGrade->deleteAll(
                                    array('ExamGrade.id' => $courseAddandRegistrationExamGradeIds['ExamGrade']),
                                    false
                                )) {
                                    if (!empty($selected_student_id) && $selected_student_id > 0) {
                                        $generated_student_exam_status = $this->CourseRegistration->Student->StudentExamStatus->regenerate_all_status_of_student_by_student_id(
                                            $selected_student_id,
                                            0
                                        );
                                    }
                                    $this->Flash->success(
                                        'You have cancelled ' . (count(
                                            $courseAddandRegistrationExamGradeIds['ExamGrade']
                                        )) . ' NG  ' . (count(
                                            $courseAddandRegistrationExamGradeIds['ExamGrade']
                                        ) > 1 ? 'grades' : ' grade') . '.' . (!empty($generated_student_exam_status) && $generated_student_exam_status ? ' Student academic status is also regenerated by retaining course registration and assesment data.' : ' Course registration and assesment data is retained.')
                                    );
                                    $this->redirect(
                                        array(
                                            'controller' => 'students',
                                            'action' => 'student_academic_profile',
                                            (isset($selected_student_id) ? $selected_student_id : $this->request->data['Student']['selected_student_id'])
                                        )
                                    );
                                }
                            }
                        }
                    }

                    if (!empty($courseAddandRegistrationExamGradeIds['CourseAdd'])) {
                        if (!$have_atleast_one_NG_with_assesment) {
                            // original
                            if (ClassRegistry::init('CourseAdd')->deleteAll(
                                array('CourseAdd.id' => $courseAddandRegistrationExamGradeIds['CourseAdd']),
                                false
                            )) {
                                $this->CourseRegistration->ExamGrade->deleteAll(
                                    array('ExamGrade.id' => $courseAddandRegistrationExamGradeIds['ExamGrade']),
                                    false
                                );
                                if (!empty($selected_student_id) && $selected_student_id > 0) {
                                    $generated_student_exam_status = $this->CourseRegistration->Student->StudentExamStatus->regenerate_all_status_of_student_by_student_id(
                                        $selected_student_id,
                                        0
                                    );
                                }
                                $this->Flash->success(
                                    'You have cancelled ' . (count(
                                        $courseAddandRegistrationExamGradeIds['ExamGrade']
                                    )) . ' NG grades and course adds.' . (!empty($generated_student_exam_status) && $generated_student_exam_status ? ' Student academic status is also regenerated.' : '')
                                );
                                $this->redirect(
                                    array(
                                        'controller' => 'students',
                                        'action' => 'student_academic_profile',
                                        (isset($selected_student_id) ? $selected_student_id : $this->request->data['Student']['selected_student_id'])
                                    )
                                );
                            }
                        } else {
                            if (DELETE_ASSESMENT_AND_ASSOCIATED_RECORDS_ON_NG_CANCELATION) {
                                // original
                                if (ClassRegistry::init('CourseAdd')->deleteAll(
                                    array('CourseAdd.id' => $courseAddandRegistrationExamGradeIds['CourseAdd']),
                                    false
                                )) {
                                    $this->CourseRegistration->ExamGrade->deleteAll(
                                        array('ExamGrade.id' => $courseAddandRegistrationExamGradeIds['ExamGrade']),
                                        false
                                    );
                                    if (!empty($selected_student_id) && $selected_student_id > 0) {
                                        $generated_student_exam_status = $this->CourseRegistration->Student->StudentExamStatus->regenerate_all_status_of_student_by_student_id(
                                            $selected_student_id,
                                            0
                                        );
                                    }
                                    $this->Flash->success(
                                        'You have cancelled ' . (count(
                                            $courseAddandRegistrationExamGradeIds['ExamGrade']
                                        )) . ' NG grades and course adds.' . (!empty($generated_student_exam_status) && $generated_student_exam_status ? ' Student academic status is also regenerated.' : '')
                                    );
                                    $this->redirect(
                                        array(
                                            'controller' => 'students',
                                            'action' => 'student_academic_profile',
                                            (isset($selected_student_id) ? $selected_student_id : $this->request->data['Student']['selected_student_id'])
                                        )
                                    );
                                }
                            } else {
                                // check if there is continues assesment and delete only Exam Grade and Exam Grade Changes if any, Neway
                                if ($this->CourseRegistration->ExamGrade->deleteAll(
                                    array('ExamGrade.id' => $courseAddandRegistrationExamGradeIds['ExamGrade']),
                                    false
                                )) {
                                    if (!empty($selected_student_id) && $selected_student_id > 0) {
                                        $generated_student_exam_status = $this->CourseRegistration->Student->StudentExamStatus->regenerate_all_status_of_student_by_student_id(
                                            $selected_student_id,
                                            0
                                        );
                                    }
                                    $this->Flash->success(
                                        'You have cancelled ' . (count(
                                            $courseAddandRegistrationExamGradeIds['ExamGrade']
                                        )) . ' NG  ' . (count(
                                            $courseAddandRegistrationExamGradeIds['ExamGrade']
                                        ) > 1 ? 'grades' : ' grade') . '.' . (!empty($generated_student_exam_status) && $generated_student_exam_status ? ' Student academic status is also regenerated by retaining course add and assesment data.' : ' Course add and assesment data is retained.')
                                    );
                                    $this->redirect(
                                        array(
                                            'controller' => 'students',
                                            'action' => 'student_academic_profile',
                                            (isset($selected_student_id) ? $selected_student_id : $this->request->data['Student']['selected_student_id'])
                                        )
                                    );
                                }
                            }
                        }
                    }


                    if (!empty($courseAddandRegistrationExamGradeIds['MakeupExam'])) {
                        if (!$have_atleast_one_NG_with_assesment) {
                            if (ClassRegistry::init('MakeupExam')->deleteAll(
                                array('MakeupExam.id' => $courseAddandRegistrationExamGradeIds['MakeupExam']),
                                false
                            )) {
                                $this->CourseRegistration->ExamGrade->deleteAll(
                                    array('ExamGrade.id' => $courseAddandRegistrationExamGradeIds['ExamGrade']),
                                    false
                                );
                                if (!empty($selected_student_id) && $selected_student_id > 0) {
                                    $generated_student_exam_status = $this->CourseRegistration->Student->StudentExamStatus->regenerate_all_status_of_student_by_student_id(
                                        $selected_student_id,
                                        0
                                    );
                                }
                                $this->Flash->success(
                                    'You have cancelled ' . count(
                                        $courseAddandRegistrationExamGradeIds['ExamGrade']
                                    ) . ' NG grades and makeup results.' . (!empty($generated_student_exam_status) && $generated_student_exam_status ? ' Student academic status is also regenerated.' : '')
                                );
                                $this->redirect(
                                    array(
                                        'controller' => 'students',
                                        'action' => 'student_academic_profile',
                                        (isset($selected_student_id) ? $selected_student_id : $this->request->data['Student']['selected_student_id'])
                                    )
                                );
                            }
                        } else {
                            if (DELETE_ASSESMENT_AND_ASSOCIATED_RECORDS_ON_NG_CANCELATION) {
                                if (ClassRegistry::init('MakeupExam')->deleteAll(
                                    array('MakeupExam.id' => $courseAddandRegistrationExamGradeIds['MakeupExam']),
                                    false
                                )) {
                                    $this->CourseRegistration->ExamGrade->deleteAll(
                                        array('ExamGrade.id' => $courseAddandRegistrationExamGradeIds['ExamGrade']),
                                        false
                                    );
                                    if (!empty($selected_student_id) && $selected_student_id > 0) {
                                        $generated_student_exam_status = $this->CourseRegistration->Student->StudentExamStatus->regenerate_all_status_of_student_by_student_id(
                                            $selected_student_id,
                                            0
                                        );
                                    }
                                    $this->Flash->success(
                                        'You have cancelled ' . count(
                                            $courseAddandRegistrationExamGradeIds['ExamGrade']
                                        ) . ' NG grades and makeup results.' . (!empty($generated_student_exam_status) && $generated_student_exam_status ? ' Student academic status is also regenerated.' : '')
                                    );
                                    $this->redirect(
                                        array(
                                            'controller' => 'students',
                                            'action' => 'student_academic_profile',
                                            (isset($selected_student_id) ? $selected_student_id : $this->request->data['Student']['selected_student_id'])
                                        )
                                    );
                                }
                            } else {
                                // check if there is continues assesment and delete only Exam Grade and Exam Grade Changes if any, Neway
                                if ($this->CourseRegistration->ExamGrade->deleteAll(
                                    array('ExamGrade.id' => $courseAddandRegistrationExamGradeIds['ExamGrade']),
                                    false
                                )) {
                                    if (!empty($selected_student_id) && $selected_student_id > 0) {
                                        $generated_student_exam_status = $this->CourseRegistration->Student->StudentExamStatus->regenerate_all_status_of_student_by_student_id(
                                            $selected_student_id,
                                            0
                                        );
                                    }
                                    $this->Flash->success(
                                        'You have cancelled ' . count(
                                            $courseAddandRegistrationExamGradeIds['ExamGrade']
                                        ) . ' NG  ' . (count(
                                            $courseAddandRegistrationExamGradeIds['ExamGrade']
                                        ) > 1 ? 'grades' : ' grade') . '.' . (!empty($generated_student_exam_status) && $generated_student_exam_status ? ' Student academic status is also regenerated by retaining makeup assesment data.' : ' Makeup assesment data is retained.')
                                    );
                                    $this->redirect(
                                        array(
                                            'controller' => 'students',
                                            'action' => 'student_academic_profile',
                                            (isset($selected_student_id) ? $selected_student_id : $this->request->data['Student']['selected_student_id'])
                                        )
                                    );
                                }
                            }
                        }
                    }

                    //unset($this->request->data);
                    $this->redirect(
                        array(
                            'controller' => 'students',
                            'action' => 'student_academic_profile',
                            (isset($selected_student_id) ? $selected_student_id : $this->request->data['Student']['selected_student_id'])
                        )
                    );
                } else {
                    if (DELETE_ASSESMENT_AND_ASSOCIATED_RECORDS_ON_NG_CANCELATION || $have_atleast_one_NG_with_assesment) {
                        if ($have_atleast_one_NG_with_assesment) {
                            $this->Flash->error(
                                'The selected NG Grade(s) could not be cancelled for ' . ($selectedStudentDetail['Student']['full_name'] . ' (' . $selectedStudentDetail['Student']['studentnumber'] . ')') . '. Check if there is a NG grade which have assesment data for the selected NG Grades.'
                            );
                        } else {
                            $this->Flash->error(
                                'The selected NG Grade(s) could not be cancelled for ' . ($selectedStudentDetail['Student']['full_name'] . ' (' . $selectedStudentDetail['Student']['studentnumber'] . ')') . '. Check if there is a grade change from the selected NG Grades.'
                            );
                        }
                    } else {
                        $this->Flash->error(
                            'The selected NG Grade(s) could not be cancelled for ' . ($selectedStudentDetail['Student']['full_name'] . ' (' . $selectedStudentDetail['Student']['studentnumber'] . ')') . '. Check if there is a grade change or assesment data from/for the selected NG Grades.'
                        );
                    }
                    $this->redirect(
                        array(
                            'controller' => 'students',
                            'action' => 'student_academic_profile',
                            (isset($selected_student_id) ? $selected_student_id : $this->request->data['Student']['selected_student_id'])
                        )
                    );
                }

                $this->redirect(
                    array(
                        'controller' => 'students',
                        'action' => 'student_academic_profile',
                        (isset($selectedStudentDetail['Student']['id']) ? $selectedStudentDetail['Student']['id'] : (isset($selected_student_id) ? $selected_student_id : $this->request->data['Student']['selected_student_id']))
                    )
                );
            }

            //register missing courses
            if (isset($this->request->data) && !empty($this->request->data['registerMissingCourse'])) {
                $registrationLists = array();
                $count = 0;

                if (!empty($this->request->data['CourseRegistration'])) {
                    foreach ($this->request->data['CourseRegistration'] as $key => $student) {
                        if (isset($student['gp']) && $student['gp'] == 1 && !empty($student['published_course_id']) && empty($student['id']) && !empty($selectedStudentDetail)) {
                            $publishedCourseDetail = $this->CourseRegistration->PublishedCourse->find('first', array(
                                'conditions' => array('PublishedCourse.id' => $student['published_course_id']),
                                'contain' => array()
                            ));

                            if (!empty($publishedCourseDetail)) {
                                $registrationLists['CourseRegistration'][$count]['year_level_id'] = (is_numeric(
                                    $publishedCourseDetail['PublishedCourse']['year_level_id']
                                ) && $publishedCourseDetail['PublishedCourse']['year_level_id'] > 0 ? $publishedCourseDetail['PublishedCourse']['year_level_id'] : null);
                                $registrationLists['CourseRegistration'][$count]['section_id'] = $publishedCourseDetail['PublishedCourse']['section_id'];
                                $registrationLists['CourseRegistration'][$count]['semester'] = $publishedCourseDetail['PublishedCourse']['semester'];
                                $registrationLists['CourseRegistration'][$count]['academic_year'] = $publishedCourseDetail['PublishedCourse']['academic_year'];
                                $registrationLists['CourseRegistration'][$count]['student_id'] = $selectedStudentDetail['Student']['id'];
                                $registrationLists['CourseRegistration'][$count]['published_course_id'] = $student['published_course_id'];

                                $check_registered = $this->CourseRegistration->find('first', array(
                                    'conditions' => array(
                                        'CourseRegistration.academic_year' => $publishedCourseDetail['PublishedCourse']['academic_year'],
                                        'CourseRegistration.student_id' => $student['student_id'],
                                        'CourseRegistration.semester' => $publishedCourseDetail['PublishedCourse']['semester'],
                                    ),
                                    'contain' => array(),
                                    'order' => array(
                                        'CourseRegistration.academic_year' => 'DESC',
                                        'CourseRegistration.semester' => 'DESC',
                                        'CourseRegistration.id' => 'DESC'
                                    ),
                                    'recursive' => -1
                                ));

                                //debug($check_registered);

                                if (!empty($check_registered)) {
                                    debug($check_registered['CourseRegistration']['created']);
                                    $registrationLists['CourseRegistration'][$count]['created'] = $check_registered['CourseRegistration']['created'];
                                    $registrationLists['CourseRegistration'][$count]['modified'] = $check_registered['CourseRegistration']['created'];
                                } else {
                                    $current_acy_and_semester = $this->AcademicYear->current_acy_and_semester();

                                    //debug($current_acy_and_semester);

                                    if ($current_acy_and_semester['academic_year'] == $publishedCourseDetail['PublishedCourse']['academic_year'] && $current_acy_and_semester['semester'] == $publishedCourseDetail['PublishedCourse']['semester']) {
                                        $registrationLists['CourseRegistration'][$count]['created'] = date(
                                            'Y-m-d h:i:s'
                                        );
                                        $registrationLists['CourseRegistration'][$count]['modified'] = date(
                                            'Y-m-d h:i:s'
                                        );
                                    } else {
                                        $registrationLists['CourseRegistration'][$count]['created'] = $this->AcademicYear->getAcademicYearBegainingDate(
                                            $publishedCourseDetail['PublishedCourse']['academic_year'],
                                            $publishedCourseDetail['PublishedCourse']['semester']
                                        );
                                        $registrationLists['CourseRegistration'][$count]['modified'] = $this->AcademicYear->getAcademicYearBegainingDate(
                                            $publishedCourseDetail['PublishedCourse']['academic_year'],
                                            $publishedCourseDetail['PublishedCourse']['semester']
                                        );
                                    }
                                }

                                $count++;
                            }
                        }
                    }
                }

                debug($registrationLists);

                if (!empty($registrationLists)) {
                    debug($registrationLists);
                    if ($this->CourseRegistration->saveAll(
                        $registrationLists['CourseRegistration'],
                        array('validate' => false)
                    )) {
                        $this->Flash->success(
                            'Missing course registration for ' . ($selectedStudentDetail['Student']['full_name'] . ' (' . $selectedStudentDetail['Student']['studentnumber'] . ')') . ' was successful for ' . (count(
                                $registrationLists['CourseRegistration']
                            )) . ' ' . (count(
                                $registrationLists['CourseRegistration']
                            ) > 1 ? 'courses' : 'course') . ' for ' . $registrationLists['CourseRegistration'][0]['academic_year'] . ' academic year semester ' . $registrationLists['CourseRegistration'][0]['semester'] . ', Dated: ' . (CakeTime::format(
                                "F j, Y h:i:s A",
                                $registrationLists['CourseRegistration'][0]['created'],
                                false,
                                null
                            )) . '.'
                        );
                        $this->redirect(
                            array(
                                'controller' => 'students',
                                'action' => 'student_academic_profile',
                                (isset($selectedStudentDetail['Student']['id']) ? $selectedStudentDetail['Student']['id'] : $this->request->data['Student']['selected_student_id'])
                            )
                        );
                    }
                } else {
                    $this->Flash->error(
                        'The Missing registration could not be added to for ' . ($selectedStudentDetail['Student']['full_name'] . ' (' . $selectedStudentDetail['Student']['studentnumber'] . ')') . '. Please, try again.'
                    );
                    $this->redirect(
                        array(
                            'controller' => 'students',
                            'action' => 'student_academic_profile',
                            (isset($selectedStudentDetail['Student']['id']) ? $selectedStudentDetail['Student']['id'] : $this->request->data['Student']['selected_student_id'])
                        )
                    );
                }
            }
        }

        $this->redirect(
            array(
                'controller' => 'students',
                'action' => 'student_academic_profile',
                (isset($selectedStudentDetail['Student']['id']) ? $selectedStudentDetail['Student']['id'] : (isset($selected_student_id) && is_numeric(
                    $selected_student_id
                ) ? $selected_student_id : ''))
            )
        );
    }

    public function getIndividualRegistration($parameters)
    {

        $this->layout = 'ajax';

        $courseLists = array();
        $criteriaLists = explode('~', $parameters);
        $publishedCourses = array();
        $show_manage_ng_link = false;
        $last_semester_status_is_not_generated = false;

        if (!empty($criteriaLists) && count($criteriaLists) > 2) {
            $academicYear = str_replace('-', '/', $criteriaLists[0]);
            $semester = $criteriaLists[1];
            $student = $criteriaLists[2];

            $getStudentSection = $this->CourseRegistration->Section->getStudentSectionInGivenAcademicYear(
                $academicYear,
                $student
            );

            $studentDetail = $this->CourseRegistration->Student->find('first', array(
                'conditions' => array(
                    'Student.id' => $student
                ),
                'contain' => array(
                    'Department',
                    'College',
                    'Program',
                    'ProgramType',
                    'Curriculum'
                )
            ));


            //$exemptedCourses = ClassRegistry::init('CourseExemption')->find('all', array('conditions' => array('CourseExemption.student_id' => $student, 'CourseExemption.registrar_confirm_deny' => 1), 'contain' => array('Course')));
            $exemptedCoursesCourseIds = ClassRegistry::init('CourseExemption')->find(
                'list',
                array(
                    'conditions' => array(
                        'CourseExemption.student_id' => $student,
                        'CourseExemption.registrar_confirm_deny' => 1
                    ),
                    'fields' => array('CourseExemption.course_id', 'CourseExemption.course_id')
                )
            );

            /* if (!empty($exemptedCoursesCourseIds)) {
				debug($exemptedCoursesCourseIds);
			} */

            $latest_academic_year['academic_year'] = $academicYear;
            $latest_academic_year['semester'] = $semester;

            $passed_or_failed = $this->CourseRegistration->Student->StudentExamStatus->get_student_exam_status(
                $student,
                $latest_academic_year['academic_year'],
                $semester
            );
            debug($passed_or_failed);

            // remove || $passed_or_failed == 2 to force students must have valid status for registration, Neway, 2 means last semester status is not generated.

            if (($passed_or_failed == 1 || $passed_or_failed == 3 || $passed_or_failed == 2) && $passed_or_failed != DISMISSED_ACADEMIC_STATUS_ID) {
                if ($passed_or_failed == 2 && $passed_or_failed != 1) {
                    $last_semester_status_is_not_generated = true;
                }

                if (!empty($getStudentSection)) {
                    $publishedCourses = $this->CourseRegistration->PublishedCourse->find('all', array(
                        'conditions' => array(
                            'PublishedCourse.semester' => $semester,
                            'PublishedCourse.academic_year' => $academicYear,
                            'PublishedCourse.section_id' => $getStudentSection['Section']['id'],
                            // 'PublishedCourse.published' => 1,
                            // 'PublishedCourse.drop <> 1',
                            // 'PublishedCourse.add <> 1',
                        ),
                        'contain' => array(
                            'Course' => array(
                                'Prerequisite',
                                'fields' => array('id', 'course_title', 'course_code', 'credit'),
                                'GradeType' => array(
                                    'Grade' => array('fields' => array('id', 'grade'))
                                )
                            )
                        )
                    ));
                }

                //debug($publishedCourses);
                $failedAnyPrerequistie['freq'] = 0;

                if (!empty($publishedCourses)) {
                    foreach ($publishedCourses as $k => &$vv) {
                        if (!empty($exemptedCoursesCourseIds) && in_array(
                                $vv['Course']['id'],
                                $exemptedCoursesCourseIds
                            )) {
                            $vv['PublishedCourse']['isExemptedCourse'] = true;
                        } else {
                            $vv['PublishedCourse']['isExemptedCourse'] = false;
                        }

                        $courseRegistration = $this->CourseRegistration->find('first', array(
                            'conditions' => array(
                                'CourseRegistration.student_id' => $student,
                                'CourseRegistration.published_course_id' => $vv['PublishedCourse']['id']
                            ),
                            'contain' => array(
                                'ExamResult' => array(
                                    'ExamType' => array(
                                        //'order' => array('ExamType.order' => 'ASC', 'ExamType.mandatory' => 'DESC', 'ExamType.percent' => 'ASC', 'ExamType.id' => 'ASC'),
                                        'limit' => 1
                                    ),
                                    //'order' => array('ExamResult.result' => 'DESC', 'ExamResult.id' => 'ASC', 'ExamResult.exam_type_id' => 'ASC'),
                                    'order' => array('ExamResult.result' => 'ASC'),
                                    'limit' => 1
                                )
                            )
                        ));

                        if (!empty($vv['Course']['Prerequisite'])) {
                            foreach ($vv['Course']['Prerequisite'] as $preValue) {
                                $failed = ClassRegistry::init('CourseDrop')->prequisite_taken(
                                    $student,
                                    $preValue['prerequisite_course_id']
                                );
                                debug($failed);
                                debug($preValue);

                                if ($failed == 0 && $preValue['co_requisite'] != true) {
                                    $failedAnyPrerequistie['freq']++;
                                }
                            }
                        }

                        if ($failedAnyPrerequistie['freq'] > 0) {
                            $vv['PublishedCourse']['prerequisiteFailed'] = true;
                            $failedAnyPrerequistie['freq'] = 0;
                        } else {
                            $vv['PublishedCourse']['prerequisiteFailed'] = 0;
                        }

                        $vv['PublishedCourse']['mass_dropped'] = $vv['PublishedCourse']['drop'];
                        $vv['PublishedCourse']['mass_added'] = $vv['PublishedCourse']['add'];

                        //course registration
                        if (!empty($courseRegistration)) {
                            $approvedGrade = $this->CourseRegistration->ExamGrade->getApprovedGrade(
                                $courseRegistration['CourseRegistration']['id'],
                                1
                            );

                            if (!empty($approvedGrade) && $approvedGrade['grade'] == 'NG') {
                                $vv['PublishedCourse']['readOnly'] = false;
                                $vv['PublishedCourse']['grade'] = $approvedGrade['grade'];
                                $vv['PublishedCourse']['grade_id'] = $approvedGrade['grade_id'];

                                if (isset($approvedGrade['grade_change_id'])) {
                                    $vv['PublishedCourse']['grade_change_id'] = $approvedGrade['grade_change_id'];
                                    $vv['PublishedCourse']['haveGradeChange'] = 1;
                                } else {
                                    $vv['PublishedCourse']['haveGradeChange'] = 0;
                                }

                                $vv['PublishedCourse']['haveAssesmentData'] = false;

                                if (isset($courseRegistration['ExamResult']) && !empty($courseRegistration['ExamResult'])) {
                                    $vv['PublishedCourse']['readOnly'] = true;
                                    $vv['PublishedCourse']['haveAssesmentData'] = true;
                                    //debug($approvedGrade);
                                    //debug($courseRegistration);

                                    if ($this->onlyPre && !empty($this->college_ids) && !empty($vv['PublishedCourse']['college_id']) && in_array(
                                            $vv['PublishedCourse']['college_id'],
                                            $this->college_ids
                                        )) {
                                        $show_manage_ng_link = true;
                                    } else {
                                        if (!$this->onlyPre && !empty($this->department_ids) && !empty($vv['PublishedCourse']['department_id']) && in_array(
                                                $vv['PublishedCourse']['department_id'],
                                                $this->department_ids
                                            )) {
                                            $show_manage_ng_link = true;
                                        }
                                    }

                                    if ($this->Session->read('Auth.User')['is_admin'] == 1) {
                                        $show_manage_ng_link = true;
                                    }
                                }
                            } else {
                                if (!empty($approvedGrade) && $approvedGrade['grade'] != 'NG') {
                                    $vv['PublishedCourse']['readOnly'] = true;
                                    $vv['PublishedCourse']['grade'] = $approvedGrade['grade'];
                                    $vv['PublishedCourse']['grade_id'] = $approvedGrade['grade_id'];

                                    if (isset($approvedGrade['grade_change_id'])) {
                                        $vv['PublishedCourse']['grade_change_id'] = $approvedGrade['grade_change_id'];
                                        $vv['PublishedCourse']['haveGradeChange'] = 1;
                                    } else {
                                        $vv['PublishedCourse']['haveGradeChange'] = 0;
                                    }
                                } else {
                                    $vv['PublishedCourse']['readOnly'] = false;
                                }
                            }

                            $vv['PublishedCourse']['course_registration_id'] = $courseRegistration['CourseRegistration']['id'];
                        } else {
                            $vv['PublishedCourse']['readOnly'] = false;
                            $vv['PublishedCourse']['grade'] = '';
                        }

                        if ($vv['PublishedCourse']['isExemptedCourse']) {
                            $vv['PublishedCourse']['readOnly'] = true;
                            $vv['PublishedCourse']['grade'] = 'TR';
                            $vv['PublishedCourse']['grade_id'] = null;
                        }
                    }
                }
            } else {
                if ($passed_or_failed == 2) {
                    $last_semester_status_is_not_generated = true;
                } else {
                    if ($passed_or_failed == DISMISSED_ACADEMIC_STATUS_ID) {
                        $status = $studentDetail['Student']['full_name'] . ' (' . $studentDetail['Student']['studentnumber'] . ") is dismissed. You can not register the student for " . (isset($latest_academic_year['semester']) ? ($latest_academic_year['semester'] == 'I' ? '1st' : ($latest_academic_year['semester'] == 'II' ? '2nd' : ($latest_academic_year['semester'] == 'III' ? '3rd' : $latest_academic_year['semester']))) . ' semester of ' : '') . $latest_academic_year['academic_year'] . ". Please advise the student to apply for readmission if applicable.";
                        $this->set(compact('status'));
                    }

                    // check for invalid grades except I and F and failed grades

                    //$status = $studentDetail['Student']['full_name'] . ' (' . $studentDetail['Student']['studentnumber'] .  ") is dismissed. You can not register the student for " . (isset($latest_academic_year['semester']) ?  ($latest_academic_year['semester'] == 'I' ? '1st' : ($latest_academic_year['semester'] == 'II' ? '2nd' : ($latest_academic_year['semester'] == 'III' ? '3rd' : $latest_academic_year['semester']))) . ' semester of '  : '') . $latest_academic_year['academic_year'] . ". Please advise the student to apply for readmission if applicable.";
                    //$this->set(compact('status'));
                }
            }
        }

        //debug($publishedCourses);

        $this->set(
            compact('publishedCourses', 'studentDetail', 'show_manage_ng_link', 'last_semester_status_is_not_generated')
        );
    }

    public function __isDate($variable)
    {

        try {
            new DateTime($variable);
            return true;
        } catch (Exception $e) {
            return false;
        }
    }
}
