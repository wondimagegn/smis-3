<?php
namespace App\Controller;

use Cake\Core\Configure;
use Cake\Event\Event;

class CourseAddsController extends AppController
{

    public $name = 'CourseAdds';

    public $menuOptions = array(
        'parent' => 'registrations',
        'alias' => array(
            'index' => 'List Course Adds',
            'add' => 'Request Course Add for Student',
            'approveAdds' => 'Approve Course Add Requests',
            'studentAddCourses' => 'Add Courses',
            'massAdd' => 'Approve Mass Add Requests',
            'cancelMassAdd' => 'Cancel Approved Mass Adds',
            'cancelCourseAdd' => 'Cancel Course Add of Student',
        ),
        'exclude' => array(
            'getPublishedAddCourses',
            'approveAutoRejectedCourseAdd',
            'search'
        )
    );

    //var $components =array('AcademicYear', 'Security');
    public $components = array('AcademicYear');

    public $paginate = array();

    public function beforeRender(Event $event)
    {

        parent::beforeRender($event);
        $defaultacademicyear = $this->AcademicYear->currentAcademicYear();

        if (is_numeric(ACY_BACK_COURSE_ADD_DROP_APPROVAL) && ACY_BACK_COURSE_ADD_DROP_APPROVAL) {
            $acyear_array_data = $this->AcademicYear->academicYearInArray(
                ((explode('/', $defaultacademicyear)[0]) - ACY_BACK_COURSE_ADD_DROP_APPROVAL),
                (explode('/', $defaultacademicyear)[0])
            );

            $acYearMinuSeparated = $this->AcademicYear->acYearMinuSeparated(
                ((explode('/', $defaultacademicyear)[0]) - ACY_BACK_COURSE_ADD_DROP_APPROVAL),
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

        //////////////////////////////////// END DONT EDIT /////////////////////////////////////////////


        $programs = ClassRegistry::init('Program')->find(
            'list',
            array('conditions' => array('Program.id' => $this->program_ids, 'Program.active' => 1))
        );
        $program_types = $programTypes = ClassRegistry::init('ProgramType')->find(
            'list',
            array('conditions' => array('ProgramType.id' => $this->program_type_ids, 'ProgramType.active' => 1))
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

        if (isset($this->request->data['User']['password'])) {
            unset($this->request->data['User']['password']);
        }
    }

    public function beforeFilter(Event $event)
    {

        parent::beforeFilter($event);
        $this->Auth->allow(
            'getPublishedAddCourses',
            //'invalid',
            'search'
        //'approve_auto_rejected_course_add'
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

    public function __init_search()
    {

        if (!empty($this->request->data['Student'])) {
            $this->Session->write('search_data', $this->request->data['Student']);
        } else {
            if ($this->Session->check('search_data')) {
                $this->request->data['Student'] = $this->Session->read('search_data');
            }
        }
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

    public function __init_clear_session_filters()
    {

        if ($this->Session->check('search_data')) {
            $this->Session->delete('search_data');
        }

        if ($this->Session->check('search_data_index')) {
            $this->Session->delete('search_data_index');
        }
    }

    public function index()
    {

        $limit = 100;
        $name = '';
        //$studentnumber = '';
        $default_department_id = '';
        $default_college_id = '';
        $selected_academic_year = '';
        $page = 1;
        $direction = 'desc';
        $sort = 'CourseAdd.created';

        $options = array();

        $default_ac_year = $this->AcademicYear->current_academicyear();
        $allowed_academic_years_for_add_drop[$default_ac_year] = $default_ac_year;

        if (is_numeric(ACY_BACK_COURSE_ADD_DROP_APPROVAL) && ACY_BACK_COURSE_ADD_DROP_APPROVAL) {
            $allowed_academic_years_for_add_drop = $this->AcademicYear->academicYearInArray(
                ((explode('/', $default_ac_year)[0]) - ACY_BACK_COURSE_ADD_DROP_APPROVAL),
                (explode('/', $default_ac_year)[0])
            );
        }

        if (/* isset($this->passedArgs) &&  */ !empty($this->passedArgs)) {
            //debug($this->passedArgs);

            if (!empty($this->passedArgs['Search.limit'])) {
                $limit = $this->request->data['Search']['limit'] = $this->passedArgs['Search.limit'];
            }

            if (!empty($this->passedArgs['Search.name'])) {
                $name = $this->request->data['Search']['name'] = str_replace(
                    '-',
                    '/',
                    trim($this->passedArgs['Search.name'])
                );
            }

            if (!empty($this->passedArgs['Search.department_id'])) {
                $default_department_id = $this->request->data['Search']['department_id'] = $this->passedArgs['Search.department_id'];
            }

            if (!empty($this->passedArgs['Search.college_id'])) {
                $default_college_id = $this->request->data['Search']['college_id'] = $this->passedArgs['Search.college_id'];
            }

            if (!empty($this->passedArgs['Search.academic_year'])) {
                $selected_academic_year = $this->request->data['Search']['academic_year'] = str_replace(
                    '-',
                    '/',
                    trim($this->passedArgs['Search.academic_year'])
                );
                $allowed_academic_years_for_add_drop[$selected_academic_year] = $selected_academic_year;
            }

            if (!empty($this->passedArgs['Search.semester'])) {
                $this->request->data['Search']['semester'] = $this->passedArgs['Search.semester'];
            }

            if (!empty($this->passedArgs['Search.program_id'])) {
                $this->request->data['Search']['program_id'] = $this->passedArgs['Search.program_id'];
            }

            if (isset($this->passedArgs['Search.program_type_id'])) {
                $this->request->data['Search']['program_type_id'] = $this->passedArgs['Search.program_type_id'];
            }

            if (isset($this->passedArgs['Search.graduated'])) {
                $this->request->data['Search']['graduated'] = $this->passedArgs['Search.graduated'];
            }

            /* if (isset($this->passedArgs['Search.studentnumber'])) {
				$studentnumber = $this->request->data['Search']['studentnumber'] = str_replace('-', '/', trim($this->passedArgs['Search.studentnumber']));
			} */

            if (isset($this->passedArgs['Search.status']) && !empty($this->passedArgs['Search.status'])) {
                $this->request->data['Search']['status'] = $this->passedArgs['Search.status'];
            }

            if (isset($this->passedArgs['page'])) {
                $page = $this->request->data['Search']['page'] = $this->passedArgs['page'];
            }

            if (isset($this->passedArgs['sort'])) {
                $sort = $this->request->data['Search']['sort'] = $this->passedArgs['sort'];
            }

            if (isset($this->passedArgs['direction'])) {
                $direction = $this->request->data['Search']['direction'] = $this->passedArgs['direction'];
            }

            //$this->__init_search();
            $this->__init_search_index();
            //$this->request->data['search'] = true;
        }

        if (isset($data) && !empty($data['Search'])) {
            $this->request->data['Search'] = $data['Search'];
            $this->__init_clear_session_filters();
            $this->__init_search_index();
        }

        if (isset($this->request->data['search'])) {
            unset($this->passedArgs);
            $this->__init_clear_session_filters();
            $this->__init_search_index();
        }


        if (!empty($this->request->data)) {
            //debug($this->request->data);

            if (!empty($page) && !isset($this->request->data['search'])) {
                $this->request->data['Search']['page'] = $page;
            }

            if (isset($this->request->data['Search']['status']) && $this->request->data['Search']['status'] == 'auto_rejected' && (empty($this->request->data['Search']['academic_year']) || empty($this->request->data['Search']['semester']))) {
                $curr_acy_semester = $this->AcademicYear->current_acy_and_semester();
                if (empty($this->request->data['Search']['academic_year'])) {
                    $this->request->data['Search']['academic_year'] = $curr_acy_semester['academic_year'];
                }
                if (empty($this->request->data['Search']['semester'])) {
                    $this->request->data['Search']['semester'] = $curr_acy_semester['semester'];
                }
            }

            if ($this->Session->read('Auth.User')['role_id'] == ROLE_DEPARTMENT) {
                $departments = $this->CourseAdd->Student->Department->find(
                    'list',
                    array('conditions' => array('Department.id' => $this->department_id, 'Department.active' => 1))
                );
                $options['conditions'][] = array('Student.department_id' => $this->department_id);
                $default_department_id = $this->request->data['Search']['department_id'] = $this->department_id;

                if ($this->request->data['Search']['status'] == 'notprocessed') {
                    $options['conditions'][] = array(
                        'CourseAdd.department_approval is null',
                        'CourseAdd.auto_rejected' => 0,
                        'CourseAdd.cron_job' => 0
                    );
                } else {
                    if ($this->request->data['Search']['status'] == 'accepted') {
                        $options['conditions'][] = array(
                            'OR' => array(
                                'CourseAdd.department_approval' => 1,
                                'CourseAdd.registrar_confirmation' => 1
                            )
                        );
                    } else {
                        if ($this->request->data['Search']['status'] == 'rejected') {
                            $options['conditions'][] = array(
                                'OR' => array(
                                    'CourseAdd.department_approval' => 0,
                                    'CourseAdd.registrar_confirmation' => 0
                                ),
                            );
                        } else {
                            if ($this->request->data['Search']['status'] == 'auto_rejected') {
                                $options['conditions'][] = array('CourseAdd.auto_rejected' => 1);
                            }
                        }
                    }
                }
            } else {
                if ($this->Session->read('Auth.User')['role_id'] == ROLE_COLLEGE) {
                    $departments = $this->CourseAdd->Student->Department->find(
                        'list',
                        array(
                            'conditions' => array(
                                'Department.college_id' => $this->college_id,
                                'Department.active' => 1
                            )
                        )
                    );
                    $default_college_id = $this->request->data['Search']['department_id'] = $this->college_id;

                    if (!empty($this->request->data['Search']['department_id'])) {
                        $options['conditions'][] = array('Student.department_id' => $this->request->data['Search']['department_id']);
                    } else {
                        $options['conditions'][] = array('Student.college_id' => $this->college_id);
                    }

                    if ($this->request->data['Search']['status'] == 'notprocessed') {
                        $options['conditions'][] = array(
                            'CourseAdd.department_approval is null',
                            'CourseAdd.auto_rejected' => 0,
                            'CourseAdd.cron_job' => 0
                        );
                    } else {
                        if ($this->request->data['Search']['status'] == 'accepted') {
                            $options['conditions'][] = array(
                                'OR' => array(
                                    'CourseAdd.department_approval' => 1,
                                    'CourseAdd.registrar_confirmation' => 1
                                )
                            );
                        } else {
                            if ($this->request->data['Search']['status'] == 'rejected') {
                                $options['conditions'][] = array(
                                    'OR' => array(
                                        'CourseAdd.department_approval' => 0,
                                        'CourseAdd.registrar_confirmation' => 0
                                    ),
                                );
                            } else {
                                if ($this->request->data['Search']['status'] == 'auto_rejected') {
                                    $options['conditions'][] = array('CourseAdd.auto_rejected' => 1);
                                }
                            }
                        }
                    }
                } else {
                    if ($this->Session->read('Auth.User')['role_id'] == ROLE_REGISTRAR) {
                        if (!empty($this->department_ids)) {
                            $colleges = array();
                            $departments = $this->CourseAdd->Student->Department->find(
                                'list',
                                array(
                                    'conditions' => array(
                                        'Department.id' => $this->department_ids,
                                        'Department.active' => 1
                                    )
                                )
                            );

                            if (!empty($this->request->data['Search']['department_id'])) {
                                $options['conditions'][] = array('Student.department_id' => $this->request->data['Search']['department_id']);
                            } else {
                                $options['conditions'][] = array('Student.department_id' => $this->department_ids);
                            }
                        } else {
                            if (!empty($this->college_ids)) {
                                $departments = array();
                                $colleges = $this->CourseAdd->Student->College->find(
                                    'list',
                                    array(
                                        'conditions' => array(
                                            'College.id' => $this->college_ids,
                                            'College.active' => 1
                                        )
                                    )
                                );

                                if (!empty($this->request->data['Search']['college_id'])) {
                                    $options['conditions'][] = array(
                                        'Student.college_id' => $this->request->data['Search']['college_id'],
                                        'Student.department_id IS NULL'
                                    );
                                } else {
                                    $options['conditions'][] = array(
                                        'Student.college_id' => $this->college_ids,
                                        'Student.department_id IS NULL'
                                    );
                                }
                            }
                        }

                        if ($this->request->data['Search']['status'] == 'notprocessed') {
                            $options['conditions'][] = array(
                                'CourseAdd.department_approval' => 1,
                                'CourseAdd.registrar_confirmation is null',
                                'CourseAdd.auto_rejected' => 0,
                                'CourseAdd.cron_job' => 0
                            );
                        } else {
                            if ($this->request->data['Search']['status'] == 'accepted') {
                                $options['conditions'][] = array(
                                    'CourseAdd.registrar_confirmation' => 1,
                                    'CourseAdd.department_approval' => 1,
                                    /* 'OR' => array(
							'CourseAdd.department_approval' => 1,
							'CourseAdd.registrar_confirmation' => 1
						) */
                                );
                            } else {
                                if ($this->request->data['Search']['status'] == 'rejected') {
                                    $options['conditions'][] = array(
                                        'CourseAdd.department_approval' => 1,
                                        'CourseAdd.registrar_confirmation' => 0
                                    );
                                } else {
                                    if ($this->request->data['Search']['status'] == 'auto_rejected') {
                                        $options['conditions'][] = array('CourseAdd.auto_rejected' => 1);
                                    }
                                }
                            }
                        }
                    } else {
                        if ($this->Session->read('Auth.User')['role_id'] == ROLE_STUDENT) {
                            unset($this->passedArgs);
                            unset($this->request->data);
                            return $this->redirect(array('action' => 'index'));
                        } else {
                            $departments = $this->CourseAdd->Student->Department->find(
                                'list',
                                array(
                                    'conditions' => array(
                                        'Department.id' => $this->department_ids,
                                        'Department.active' => 1
                                    )
                                )
                            );
                            $colleges = $this->CourseAdd->Student->College->find(
                                'list',
                                array('conditions' => array('College.id' => $this->college_ids, 'College.active' => 1))
                            );

                            if (!empty($this->request->data['Search']['department_id'])) {
                                $options['conditions'][] = array('Student.department_id' => $this->request->data['Search']['department_id']);
                            } else {
                                if (empty($this->request->data['Search']['department_id']) && !empty($this->request->data['Search']['college_id'])) {
                                    $departments = $this->CourseAdd->Student->Department->find(
                                        'list',
                                        array(
                                            'conditions' => array(
                                                'Department.college_id' => $this->request->data['Search']['college_id'],
                                                'Department.active' => 1
                                            )
                                        )
                                    );
                                    $options['conditions'][] = array(
                                        'OR' => array(
                                            'Student.college_id' => $this->request->data['Search']['college_id'],
                                            'Student.department_id' => array_keys($departments)
                                        )
                                    );
                                } else {
                                    $options['conditions'][] = array(
                                        'OR' => array(
                                            'Student.college_id' => array_keys($colleges),
                                            'Student.department_id' => array_keys($departments)
                                        )
                                    );
                                }
                            }

                            if ($this->request->data['Search']['status'] == 'notprocessed') {
                                $options['conditions'][] = array(
                                    'CourseAdd.department_approval is null',
                                    'CourseAdd.auto_rejected' => 0,
                                    'CourseAdd.cron_job' => 0
                                );
                            } else {
                                if ($this->request->data['Search']['status'] == 'accepted') {
                                    $options['conditions'][] = array(
                                        'OR' => array(
                                            'CourseAdd.department_approval' => 1,
                                            'CourseAdd.registrar_confirmation' => 1
                                        )
                                    );
                                } else {
                                    if ($this->request->data['Search']['status'] == 'rejected') {
                                        $options['conditions'][] = array(
                                            'OR' => array(
                                                'CourseAdd.department_approval' => 0,
                                                'CourseAdd.registrar_confirmation' => 0
                                            ),
                                        );
                                    } else {
                                        if ($this->request->data['Search']['status'] == 'auto_rejected') {
                                            $options['conditions'][] = array('CourseAdd.auto_rejected' => 1);
                                        }
                                    }
                                }
                            }
                        }
                    }
                }
            }

            if (!empty($selected_academic_year)) {
                $options['conditions'][] = array('CourseAdd.academic_year' => $selected_academic_year);
            } else {
                $options['conditions'][] = array('CourseAdd.academic_year' => $allowed_academic_years_for_add_drop);
            }

            if (!empty($this->request->data['Search']['program_id'])) {
                $options['conditions'][] = array('Student.program_id' => $this->request->data['Search']['program_id']);
            } else {
                if (empty($this->request->data['Search']['program_id']) && $this->Session->read(
                        'Auth.User'
                    )['role_id'] == ROLE_REGISTRAR) {
                    $options['conditions'][] = array('Student.program_id' => $this->program_ids);
                }
            }

            if (!empty($this->request->data['Search']['program_type_id'])) {
                $options['conditions'][] = array('Student.program_type_id' => $this->request->data['Search']['program_type_id']);
            } else {
                if (empty($this->request->data['Search']['program_type_id']) && $this->Session->read(
                        'Auth.User'
                    )['role_id'] == ROLE_REGISTRAR) {
                    $options['conditions'][] = array('Student.program_type_id' => $this->program_type_ids);
                }
            }

            if (isset($this->request->data['Search']['college_id']) && !empty($this->request->data['Search']['college_id']) && $this->Session->read(
                    'Auth.User'
                )['role_id'] != ROLE_REGISTRAR) {
                $departments = $this->CourseAdd->Student->Department->find(
                    'list',
                    array(
                        'conditions' => array(
                            'Department.college_id' => $this->request->data['Search']['college_id'],
                            'Department.active' => 1
                        )
                    )
                );
            }

            if ($this->request->data['Search']['graduated'] == 0) {
                $options['conditions'][] = array('Student.graduated = 0');
            } else {
                if ($this->request->data['Search']['graduated'] == 1) {
                    $options['conditions'][] = array('Student.graduated = 1');
                }
            }

            /* if (isset($studentnumber) && !empty($studentnumber)) {
				//unset($options['conditions']);
				$options['conditions'][] = array('Student.studentnumber' => trim($studentnumber));
			} */

            if (isset($name) && !empty($name)) {
                //unset($options['conditions']);
                $options['conditions'][] = array(
                    'OR' => array(
                        'Student.first_name LIKE ' => '%' . $name . '%',
                        'Student.middle_name LIKE ' => '%' . $name . '%',
                        'Student.last_name LIKE ' => '%' . $name . '%',
                        'Student.studentnumber LIKE ' => '%' . $name . '%',
                    )
                );
            }
        } else {
            if ($this->Session->read('Auth.User')['role_id'] == ROLE_COLLEGE) {
                $departments = $this->CourseAdd->Student->Department->find(
                    'list',
                    array('conditions' => array('Department.college_id' => $this->college_id, 'Department.active' => 1))
                );

                $options['conditions'][] = array(
                    'CourseAdd.academic_year' => $allowed_academic_years_for_add_drop,
                    'CourseAdd.auto_rejected' => 0,
                    'CourseAdd.cron_job' => 0,
                    'CourseAdd.department_approval is null',
                    'OR' => array(
                        'Student.college_id' => $this->college_id,
                        'Student.department_id' => array_keys($departments)
                    )
                );

                $default_college_id = $this->college_id;
            } else {
                if ($this->Session->read('Auth.User')['role_id'] == ROLE_DEPARTMENT) {
                    $departments = $this->CourseAdd->Student->Department->find(
                        'list',
                        array('conditions' => array('Department.id' => $this->department_id))
                    );

                    $options['conditions'][] = array(
                        'CourseAdd.academic_year' => $allowed_academic_years_for_add_drop,
                        'CourseAdd.auto_rejected' => 0,
                        'CourseAdd.cron_job' => 0,
                        'CourseAdd.department_approval is null',
                        'Student.department_id' => $this->department_id
                    );

                    $default_department_id = $this->department_id;
                } else {
                    if ($this->Session->read('Auth.User')['role_id'] == ROLE_REGISTRAR) {
                        if (!empty($this->department_ids)) {
                            $colleges = array();

                            $departments = $this->CourseAdd->Student->Department->find(
                                'list',
                                array(
                                    'conditions' => array(
                                        'Department.id' => $this->department_ids,
                                        'Department.active' => 1
                                    )
                                )
                            );

                            $options['conditions'][] = array(
                                'Student.department_id' => $this->department_ids,
                                'Student.program_id' => $this->program_ids,
                                'Student.program_type_id' => $this->program_type_ids
                            );
                        } else {
                            if (!empty($this->college_ids)) {
                                $departments = array();

                                $colleges = $this->CourseAdd->Student->College->find(
                                    'list',
                                    array(
                                        'conditions' => array(
                                            'College.id' => $this->college_ids,
                                            'College.active' => 1
                                        )
                                    )
                                );

                                $options['conditions'][] = array(
                                    'Student.college_id' => $this->college_ids,
                                    'Student.department_id IS NULL',
                                    'Student.program_id' => $this->program_ids,
                                    'Student.program_type_id' => $this->program_type_ids
                                );
                            }
                        }

                        $options['conditions'][] = array(
                            'CourseAdd.academic_year' => $allowed_academic_years_for_add_drop,
                            'CourseAdd.auto_rejected' => 0,
                            'CourseAdd.cron_job' => 0,
                            'CourseAdd.department_approval' => 1,
                            'CourseAdd.registrar_confirmation is null'
                        );
                    } else {
                        if ($this->Session->read('Auth.User')['role_id'] == ROLE_STUDENT) {
                            $options['conditions'][] = array('Student.id' => $this->student_id);
                        } else {
                            $departments = $this->CourseAdd->Student->Department->find(
                                'list',
                                array(
                                    'conditions' => array(
                                        'Department.id' => $this->department_ids,
                                        'Department.active' => 1
                                    )
                                )
                            );
                            $colleges = $this->CourseAdd->Student->College->find(
                                'list',
                                array('conditions' => array('College.id' => $this->college_ids, 'College.active' => 1))
                            );

                            if (!empty($departments) && !empty($colleges)) {
                                $options['conditions'][] = array(
                                    'CourseAdd.department_approval is null',
                                    'CourseAdd.academic_year' => $allowed_academic_years_for_add_drop,
                                    'CourseAdd.auto_rejected' => 0,
                                    'CourseAdd.cron_job' => 0,
                                    'OR' => array(
                                        'Student.department_id' => $this->department_ids,
                                        'Student.college_id' => $this->college_ids
                                    )
                                );
                            } else {
                                if (!empty($departments)) {
                                    $options['conditions'][] = array(
                                        'CourseAdd.academic_year' => $allowed_academic_years_for_add_drop,
                                        'CourseAdd.auto_rejected' => 0,
                                        'CourseAdd.cron_job' => 0,
                                        'CourseAdd.department_approval is null',
                                        'Student.department_id' => $this->department_ids
                                    );
                                } else {
                                    if (!empty($colleges)) {
                                        $options['conditions'][] = array(
                                            'CourseAdd.academic_year' => $allowed_academic_years_for_add_drop,
                                            'CourseAdd.auto_rejected' => 0,
                                            'CourseAdd.cron_job' => 0,
                                            'CourseAdd.department_approval is null',
                                            'Student.college_id' => $this->college_ids
                                        );
                                    }
                                }
                            }
                        }
                    }
                }
            }

            if (!empty($options['conditions'])) {
                $options['conditions'][] = array('Student.graduated = 0');
            }
        }

        debug($options['conditions']);

        $courseAdds = array();

        if (!empty($options['conditions'])) {
            $this->Paginator->settings = array(
                'conditions' => $options['conditions'],
                'contain' => array(
                    'PublishedCourse' => array(
                        'Course' => array(
                            'Curriculum' => array('id', 'name', 'year_introduced', 'type_credit', 'active'),
                        ),
                        'GivenByDepartment' => array(
                            'College' => array('id', 'name', 'type', 'stream'),
                        ),
                        'Department' => array(
                            'College' => array('id', 'name', 'type', 'stream'),
                            'fields' => array('id', 'name', 'type'),
                        ),
                        'College' => array('id', 'name', 'type', 'stream'),
                        'Section' => array(
                            'fields' => array(
                                'id',
                                'name',
                                'academicyear',
                                'curriculum_id',
                                'program_id',
                                'program_type_id',
                                'archive'
                            ),
                            'YearLevel' => array('id', 'name'),
                            'Curriculum' => array('id', 'name', 'year_introduced', 'type_credit', 'active'),
                        ),
                    ),
                    'Student' => array(
                        'fields' => array(
                            'id',
                            'full_name',
                            'gender',
                            'studentnumber',
                            'college_id',
                            'department_id',
                            'program_id',
                            'program_type_id',
                            'graduated',
                            'academicyear',
                            'admissionyear'
                        ),
                        'Curriculum' => array('id', 'name', 'year_introduced', 'type_credit', 'active'),
                    ),
                    'YearLevel' => array('id', 'name'),
                ),
                'order' => array($sort => $direction),
                'limit' => (!empty($limit) ? $limit : 100),
                'maxLimit' => 1000,
                'page' => $page,
                'recursive' => -1
            );

            //$courseAdds = $this->paginate($options);

            try {
                $courseAdds = $this->Paginator->paginate($this->modelClass);
                $this->set(compact('courseAdds'));
            } catch (NotFoundException $e) {
                if (!empty($this->request->data['Search'])) {
                    unset($this->request->data['Search']['page']);
                    unset($this->request->data['Search']['sort']);
                    unset($this->request->data['Search']['direction']);
                }
                unset($this->passedArgs);
                return $this->redirect(array('action' => 'index'));
            } catch (Exception $e) {
                if (!empty($this->request->data['Search'])) {
                    unset($this->request->data['Search']['page']);
                    unset($this->request->data['Search']['sort']);
                    unset($this->request->data['Search']['direction']);
                }
                unset($this->passedArgs);
                return $this->redirect(array('action' => 'index'));
            }
        } else {
            $this->set(compact('courseAdds'));
        }

        if (empty($courseAdds) && !empty($options)) {
            $this->Flash->info('There is no Course Add found with the given criteria.');
            $turn_off_search = false;
        } else {
            //debug($courseAdds[0]);
            $turn_off_search = false;
            if ((isset($this->request->data['Search']['status']) && $this->request->data['Search']['status'] == 'auto_rejected')) {
                $current_load = array();
                $graduatingClassStudent = array();
                foreach ($courseAdds as $key => $courseAdd) {
                    if (isset($courseAdd['CourseAdd']['id']) && !empty($courseAdd['CourseAdd']['id']) && isset($courseAdd['Student']['id']) && !empty($courseAdd['Student']['id']) && $courseAdd['Student']['graduated'] == 0 && $courseAdd['CourseAdd']['academic_year'] == $this->request->data['Search']['academic_year'] && $courseAdd['CourseAdd']['semester'] == $this->request->data['Search']['semester'] && isset($courseAdd['PublishedCourse']['Section']['archive']) && !($courseAdd['PublishedCourse']['Section']['archive'])) {
                        $current_load[$courseAdd['Student']['id']] = $this->CourseAdd->Student->calculateStudentLoad(
                            $courseAdd['Student']['id'],
                            $courseAdd['CourseAdd']['semester'],
                            $courseAdd['CourseAdd']['academic_year']
                        );
                        if (ClassRegistry::init('StudentStatusPattern')->isgraduatingClassStudent(
                            $courseAdd['Student']['id']
                        )) {
                            $graduatingClassStudent[$courseAdd['Student']['id']] = true;
                        } else {
                            $graduatingClassStudent[$courseAdd['Student']['id']] = false;
                        }
                    }
                }

                if (!empty($current_load)) {
                    //debug($current_load);
                    $this->set(compact('current_load'));
                }

                //debug($graduatingClassStudent);

                $this->set(compact('graduatingClassStudent'));
            }
        }

        $this->set(
            compact(/* 'courseAdds', */ 'programs',
                'programTypes',
                'departments',
                'colleges',
                'limit',
                'name',
                'studentnumber',
                'turn_off_search',
                'default_department_id',
                'default_college_id',
                'allowed_academic_years_for_add_drop'
            )
        );
    }

    public function view($id = null)
    {

        if (!$id) {
            $this->Flash->error(__('Invalid Course Add ID.'));
            return $this->redirect(array('action' => 'index'));
        }

        $this->CourseAdd->id = $id;

        if (!$this->CourseAdd->exists()) {
            $this->Flash->error(__('Invalid Course Add ID.'));
            return $this->redirect(array('action' => 'index'));
        }

        $this->set('courseAdd', $this->CourseAdd->read(null, $id));
    }

    public function add($id = null)
    {

        $logged_user_detail = ClassRegistry::init('User')->find(
            'first',
            array('conditions' => array('User.id' => $this->Auth->user('id')), 'contain' => array('Staff', 'Student'))
        );

        $hide_search = false;

        $collegess = $this->CourseAdd->PublishedCourse->College->find(
            'list',
            array('conditions' => array('College.active' => 1), 'order' => array('College.name ASC'))
        );
        $departments = array();

        if (isset($id) && !empty($id)) {
            $hide_search = true;

            $this->__init_search_index();

            $department_ids = array();
            $college_ids = array();
            $everyThingOk = false;
            $selectedStudent = array();

            if ($this->Session->read('Auth.User')['role_id'] == ROLE_REGISTRAR && !empty($this->department_ids)) {
                $department_ids = $this->department_ids;
            } else {
                if ($this->Session->read('Auth.User')['role_id'] == ROLE_REGISTRAR && !empty($this->college_ids)) {
                    $college_ids = $this->college_ids;
                }
            }

            if (!empty($department_ids)) {
                $selectedStudent = $this->CourseAdd->Student->get_student_section($id);
                //debug($selectedStudent);

                if (empty($selectedStudent)) {
                    $this->Flash->error('Student ID Not found.');
                } else {
                    if (!in_array($selectedStudent['StudentBasicInfo']['department_id'], $department_ids) || !in_array(
                            $selectedStudent['StudentBasicInfo']['program_id'],
                            $this->program_ids
                        ) || !in_array(
                            $selectedStudent['StudentBasicInfo']['program_type_id'],
                            $this->program_type_ids
                        )) {
                        $this->Flash->error(
                            'You don\'t have the privilage to Add course add for ' . $selectedStudent['StudentBasicInfo']['full_name'] . ' (' . $selectedStudent['StudentBasicInfo']['studentnumber'] . ').'
                        );
                        ClassRegistry::init('AutoMessage')->sendPermissionManagementBreakAttempt(
                            Configure::read('User.user'),
                            '<u>' . $this->Session->read(
                                'Auth.User'
                            )['full_name'] . '</u> is trying to add courses for ' . $selectedStudent['StudentBasicInfo']['full_name'] . ' (' . $selectedStudent['StudentBasicInfo']['studentnumber'] . ') without assigned privilage. Please give appropriate warning.'
                        );
                        $selectedStudent = array();
                    } else {
                        if ($selectedStudent['StudentBasicInfo']['graduated']) {
                            $this->Flash->error(
                                $selectedStudent['StudentBasicInfo']['full_name'] . ' (' . $selectedStudent['StudentBasicInfo']['studentnumber'] . ') is graduated Student.'
                            );
                            $everyThingOk = false;
                            $selectedStudent = array();
                        } else {
                            $everyThingOk = true;
                        }
                    }
                }
            } elseif (!empty($college_ids)) {
                $selectedStudent = $this->CourseAdd->Student->get_student_section($id);

                if (empty($selectedStudent)) {
                    $this->Flash->error('Student ID Not found.');
                } elseif (!in_array($selectedStudent['StudentBasicInfo']['college_id'], $college_ids) || !in_array(
                        $selectedStudent['StudentBasicInfo']['program_id'],
                        $this->program_ids
                    ) || !in_array($selectedStudent['StudentBasicInfo']['program_type_id'], $this->program_type_ids)) {
                    $this->Flash->error(
                        'You don\'t have the privilage to add a course for ' . $selectedStudent['StudentBasicInfo']['full_name'] . ' (' . $selectedStudent['StudentBasicInfo']['studentnumber'] . ').'
                    );
                    ClassRegistry::init('AutoMessage')->sendPermissionManagementBreakAttempt(
                        Configure::read('User.user'),
                        '<u>' . $this->Session->read(
                            'Auth.User'
                        )['full_name'] . '</u> is trying to add courses for ' . $selectedStudent['StudentBasicInfo']['full_name'] . ' (' . $selectedStudent['StudentBasicInfo']['studentnumber'] . ') without assigned privilage. Please give appropriate warning.'
                    );
                    $selectedStudent = array();
                } else {
                    if ($selectedStudent['StudentBasicInfo']['graduated']) {
                        $this->Flash->error(
                            $selectedStudent['StudentBasicInfo']['full_name'] . ' (' . $selectedStudent['StudentBasicInfo']['studentnumber'] . ') is graduated Student. You can\'t add courses.'
                        );
                        $everyThingOk = false;
                        $selectedStudent = array();
                    } else {
                        $everyThingOk = true;
                    }
                }
            }

            if (!empty($selectedStudent)) {
                //debug($selectedStudent);
            }

            if (!$everyThingOk) {
                $this->redirect('add');
            } else {
                //debug($selectedStudent);

                $system_current_acy_and_semester = $this->AcademicYear->current_acy_and_semester();
                debug($system_current_acy_and_semester['academic_year']);
                debug($system_current_acy_and_semester['semester']);

                if (!empty($this->request->data['Search']['academicyear'])) {
                    $current_academic_year = $this->request->data['Search']['academicyear'];
                    $semester = $this->request->data['Search']['semester'];
                } else {
                    /* $current_academic_year = $this->AcademicYear->current_academicyear();
					$latestAcSemester =  ClassRegistry::init('CourseRegistration')->getLastestStudentSemesterAndAcademicYear($id, $current_academic_year, 1);
					$semester = $latestAcSemester['semester']; */

                    $current_academic_year = $system_current_acy_and_semester['academic_year'];
                    $semester = $system_current_acy_and_semester['semester'];
                    $this->Flash->warning('You are required to select academic year');
                    $this->redirect('/');
                }

                $student_section_exam_status = $this->CourseAdd->Student->get_student_section(
                    $id,
                    $current_academic_year
                );

                debug($student_section_exam_status);

                $getRegistrationDeadLine = false;

                if (empty($student_section_exam_status['Section'])) {
                    $this->Flash->warning(
                        'The selected student is section-less. Please advise his/her department to assign him/her a section for ' . $current_academic_year . ' ACY.'
                    );
                    $this->redirect('/');
                }

                $year_level_id = '';

                if (!empty($this->department_ids)) {
                    $year_level_id = $this->CourseAdd->YearLevel->field(
                        'name',
                        array('id' => $student_section_exam_status['Section']['year_level_id'])
                    );
                    $getRegistrationDeadLine = ClassRegistry::init(
                        'CourseRegistration'
                    )->AcademicCalendar->check_add_date_end(
                        $current_academic_year,
                        $semester,
                        $student_section_exam_status['StudentBasicInfo']['department_id'],
                        $year_level_id
                    );
                } else {
                    if (!empty($this->college_ids)) {
                        $getRegistrationDeadLine = ClassRegistry::init(
                            'CourseRegistration'
                        )->AcademicCalendar->check_add_date_end(
                            $current_academic_year,
                            $semester,
                            $student_section_exam_status['StudentBasicInfo']['college_id'],
                            0
                        );
                    }
                }

                if ($getRegistrationDeadLine == 0 || $getRegistrationDeadLine == 1) {
                } else {
                    $add_start_date = $getRegistrationDeadLine;
                    $getRegistrationDeadLine = 0;
                }

                if (!$getRegistrationDeadLine) {
                    if (0 && isset($add_start_date) && !empty($add_start_date)) {
                        $this->Flash->info(
                            'Course add start date is at ' . $add_start_date . ' Please come back when course add starts.'
                        );
                        $this->redirect(array('controller' => 'courseAdds', 'action' => 'add'));
                    } else {
                        debug($getRegistrationDeadLine);
                        $this->Flash->info(
                            'Course add dead line is passed for students but as registrar you can maintain student adds. Beware that student should have to register for  the semester and academic year before adding courses.'
                        );
                    }
                }

                $student_section = $this->CourseAdd->Student->student_academic_detail($id, $current_academic_year);

                if (empty($student_section_exam_status['Section']['year_level_id'])) {
                    $published_detail = array(
                        'academic_year' => $current_academic_year,
                        'semester' => $semester,
                        'student_id' => $id,
                        'OR' => array(
                            'year_level_id = 0',
                            'year_level_id = ""',
                            'year_level_id IS NULL'
                        )
                    );
                } else {
                    $published_detail = array(
                        'academic_year' => $current_academic_year,
                        'semester' => $semester,
                        'student_id' => $id,
                        'year_level_id' => $student_section_exam_status['Section']['year_level_id']
                    );
                }

                if (!empty($student_section_exam_status['Section'])) {
                    $collegess = $this->CourseAdd->PublishedCourse->College->find('list', array(
                        'conditions' => array(
                            'OR' => array(
                                'College.campus_id' => $student_section_exam_status['College']['campus_id'],
                                'College.stream' => $student_section_exam_status['College']['stream'],
                            ),
                            'College.active' => 1
                        ),
                        'order' => array('College.campus_id ASC', 'College.name ASC')
                    ));

                    //debug($student_section_exam_status);

                    if (isset($student_section_exam_status['College']['stream']) && $student_section_exam_status['College']['stream']) {
                        $collegess = $this->CourseAdd->PublishedCourse->College->find(
                            'list',
                            array(
                                'conditions' => array(
                                    'College.active' => 1,
                                    'College.stream' => $student_section_exam_status['College']['stream'],
                                    'OR' => array(
                                        'College.campus_id' => $student_section_exam_status['College']['campus_id'],
                                        'College.id' => Configure::read('only_stream_based_colleges_pre_social_natural')
                                    )
                                )
                            )
                        );
                    }

                    if (!is_null($student_section_exam_status['College']['id']) && is_null(
                            $student_section_exam_status['StudentBasicInfo']['department_id']
                        ) && in_array(
                            $student_section_exam_status['StudentBasicInfo']['college_id'],
                            Configure::read('only_stream_based_colleges_pre_social_natural')
                        )) {
                        $collegess = $this->CourseAdd->PublishedCourse->College->find(
                            'list',
                            array(
                                'conditions' => array(
                                    'College.id' => Configure::read('only_stream_based_colleges_pre_social_natural'),
                                    'College.active' => 1
                                )
                            )
                        );
                    }
                } else {
                    $this->Flash->warning(
                        'The student is sectionless,  S/he should be assigned to section by department. Please advice department.'
                    );
                }

                $this->set(compact('student_section', 'student_section_exam_status'));
                $this->set('year_level_id', $year_level_id);
            }

            if (!empty($this->request->data['CourseAdd'])) {
                $selected = array_sum($this->request->data['CourseAdd']['add']);

                if ($selected > 0) {
                    $selected_courses_for_add = $this->request->data['CourseAdd']['add'];

                    unset($this->request->data['CourseAdd']['add']);
                    unset($this->request->data['Student']['department_id']);

                    $add_selected_to_registration = array();

                    if (!empty($selected_courses_for_add)) {
                        foreach ($selected_courses_for_add as $k => $v) {
                            if ($v == 1) {
                                $published_detail['published_course_id'] = $k;
                                $add_selected_to_registration['CourseAdd'][] = $published_detail['published_course_id'];
                            }
                        }
                    }

                    $this->request->data['CourseAdd'] = $add_selected_to_registration['CourseAdd'];

                    //check for duplicate entry
                    $already_added_courses = array();
                    $selected_courses_add = array();
                    $count = 0;

                    if (!empty($this->request->data['CourseAdd'])) {
                        foreach ($this->request->data['CourseAdd'] as $cdd => $cdv) {
                            $check = $this->CourseAdd->find('count', array(
                                'conditions' => array(
                                    'CourseAdd.published_course_id' => $cdv,
                                    'CourseAdd.student_id' => $id
                                ),
                                'recursive' => -1
                            ));

                            // already added, unset it
                            if ($check > 0) {
                                $already_added_courses[] = $cdv;
                            } else {
                                $publishedCourseDetail = $this->CourseAdd->PublishedCourse->find(
                                    'first',
                                    array('conditions' => array('PublishedCourse.id' => $cdv), 'contain' => array())
                                );
                                debug($publishedCourseDetail);

                                $is_mass_add = $this->CourseAdd->PublishedCourse->field('add', array('id' => $cdv));

                                $selected_courses_add['CourseAdd'][$count]['published_course_id'] = $cdv;

                                if ($is_mass_add == 1) {
                                    $selected_courses_add['CourseAdd'][$count]['department_approval'] = 1;
                                    $selected_courses_add['CourseAdd'][$count]['registrar_confirmation'] = 1;
                                }

                                $selected_courses_add['CourseAdd'][$count]['student_id'] = $id;
                                $selected_courses_add['CourseAdd'][$count]['semester'] = $semester;
                                $selected_courses_add['CourseAdd'][$count]['academic_year'] = $current_academic_year;

                                if (empty($student_section['Section'][0]['year_level_id']) || $student_section['Section'][0]['year_level_id'] == 0) {
                                    $selected_courses_add['CourseAdd'][$count]['year_level_id'] = 0;
                                } else {
                                    $selected_courses_add['CourseAdd'][$count]['year_level_id'] = $student_section['Section'][0]['year_level_id'];
                                }

                                $check_registered = ClassRegistry::init('CourseRegistration')->find('first', array(
                                    'conditions' => array(
                                        'CourseRegistration.academic_year' => $publishedCourseDetail['PublishedCourse']['academic_year'],
                                        'CourseRegistration.student_id' => $id,
                                        'CourseRegistration.semester' => $publishedCourseDetail['PublishedCourse']['semester'],
                                    ),
                                    'contain' => array(),
                                    'order' => array(
                                        'CourseRegistration.academic_year' => 'DESC',
                                        'CourseRegistration.semester' => 'DESC',
                                        'CourseRegistration.id' => 'DESC'
                                    ),
                                ));

                                debug($check_registered);

                                if (!empty($check_registered)) {
                                    debug($check_registered['CourseRegistration']['created']);
                                    $selected_courses_add['CourseAdd'][$count]['created'] = $check_registered['CourseRegistration']['created'];
                                    $selected_courses_add['CourseAdd'][$count]['modified'] = $check_registered['CourseRegistration']['created'];
                                } else {
                                    $current_acy_and_semester = $this->AcademicYear->current_acy_and_semester();
                                    debug($current_acy_and_semester);

                                    if ($current_acy_and_semester['academic_year'] == $publishedCourseDetail['PublishedCourse']['academic_year'] && $current_acy_and_semester['semester'] == $publishedCourseDetail['PublishedCourse']['semester']) {
                                        $selected_courses_add['CourseAdd'][$count]['created'] = date('Y-m-d h:i:s');
                                        $selected_courses_add['CourseAdd'][$count]['modified'] = date('Y-m-d h:i:s');
                                    } else {
                                        $selected_courses_add['CourseAdd'][$count]['created'] = $this->AcademicYear->getAcademicYearBegainingDate(
                                            $publishedCourseDetail['PublishedCourse']['academic_year'],
                                            $publishedCourseDetail['PublishedCourse']['semester']
                                        );
                                        $selected_courses_add['CourseAdd'][$count]['modified'] = $this->AcademicYear->getAcademicYearBegainingDate(
                                            $publishedCourseDetail['PublishedCourse']['academic_year'],
                                            $publishedCourseDetail['PublishedCourse']['semester']
                                        );
                                    }
                                }
                            }

                            $count++;
                        }
                    }

                    $already_added_courses_for_the_semester = $this->CourseAdd->find('count', array(
                        'conditions' => array(
                            'CourseAdd.academic_year' => $current_academic_year,
                            'CourseAdd.semester' => $semester,
                            'CourseAdd.student_id' => $id
                        )
                    ));

                    //debug($already_added_courses_for_the_semester);

                    if (count($already_added_courses) == count($this->request->data['CourseAdd'])) {
                        $this->Flash->info(
                            'All the selected courses are already added. You do not need to add it again.'
                        );
                        //$this->redirect(array('action'=>'index'));
                    } else {
                        if (is_numeric(
                                MAXIMUM_COURSES_TO_ADD_PER_SEMESTER
                            ) && MAXIMUM_COURSES_TO_ADD_PER_SEMESTER > 0 && (($already_added_courses_for_the_semester > MAXIMUM_COURSES_TO_ADD_PER_SEMESTER) || (($already_added_courses_for_the_semester + count(
                                            $this->request->data['CourseAdd']
                                        )) > MAXIMUM_COURSES_TO_ADD_PER_SEMESTER))) {
                            $this->Flash->warning(
                                'You can\'t add more than ' . MAXIMUM_COURSES_TO_ADD_PER_SEMESTER . ' courses per semester for the selected student. Cancel one or more courses if not approved/rejected by the department or uncheck some from your selection and try again.'
                            );
                            $this->redirect(array('action' => 'add'));
                        } else {
                            if (is_numeric(
                                    MAXIMUM_COURSES_TO_ADD_PER_SEMESTER
                                ) && MAXIMUM_COURSES_TO_ADD_PER_SEMESTER > 0 && count(
                                    $this->request->data['CourseAdd']
                                ) > MAXIMUM_COURSES_TO_ADD_PER_SEMESTER) {
                                $this->Flash->warning(
                                    'You can\'t add more than ' . MAXIMUM_COURSES_TO_ADD_PER_SEMESTER . ' courses per semester for the selected student. Uncheck one or more courses from your selection and try again.'
                                );
                                $this->redirect(array('action' => 'add'));
                            } else {
                                // unset($this->request->data);
                                $this->request->data['CourseAdd'] = $selected_courses_add;
                            }
                        }
                    }

                    $this->request->data['CourseAdd'] = $this->request->data['CourseAdd']['CourseAdd'];

                    if (!empty($this->request->data['CourseAdd'])) {
                        //debug($this->request->data);
                        //debug($this->request->data['CourseAdd']);
                        if ($this->CourseAdd->saveAll($this->request->data['CourseAdd'], array('validate' => 'first')
                        )) {
                            $this->Flash->success('The course add has successful and sent to department for approval.');
                            // dont forget to add to registration table after approval
                            $this->redirect('add');
                        } else {
                            $this->Flash->error('The course add could not be saved. Please, try again.');
                        }
                    }
                } else {
                    $this->Flash->error('Please select atleast one course you want to add.');
                }

                debug($this->request->data);
            }
        }

        if (!empty($this->request->data) && isset($this->request->data['add'])) {
            if (!empty($this->request->data['Search']['academicyear'])) {
                $current_academic_year = $this->request->data['Search']['academicyear'];
                $semester = $this->request->data['Search']['semester'];
            } else {
                $current_academic_year = $this->AcademicYear->current_academicyear();
                $latestAcSemester = ClassRegistry::init('CourseRegistration')->getLastestStudentSemesterAndAcademicYear(
                    $id,
                    $current_academic_year,
                    1
                );
                $semester = $latestAcSemester['semester'];


                $latestRegistration = ClassRegistry::init('CourseRegistration')->find('first', array(
                    'conditions' => array(
                        'CourseRegistration.student_id' => $id
                    ),
                    'fields' => array(
                        'CourseRegistration.semester',
                        'CourseRegistration.academic_year'
                    ),
                    'order' => array(
                        'CourseRegistration.academic_year' => 'DESC',
                        'CourseRegistration.semester' => 'DESC',
                        'CourseRegistration.id' => 'DESC'
                    ),
                    'recursive' => -1
                ));

                debug($latestRegistration);
            }

            debug($this->request->data);

            $selected = array_sum($this->request->data['CourseAdd']['add']);
            $student_section = $this->CourseAdd->Student->student_academic_detail(
                $this->request->data['Student']['id'],
                $current_academic_year
            );

            if ($selected > 0 && isset($this->request->data['addSelected'])) {
                $selected_courses_for_add = $this->request->data['CourseAdd']['add'];

                unset($this->request->data['CourseAdd']['add']);
                unset($this->request->data['Student']['department_id']);

                $add_selected_to_registration = array();

                if (!empty($selected_courses_for_add)) {
                    foreach ($selected_courses_for_add as $k => $v) {
                        if ($v == 1) {
                            $published_detail['published_course_id'] = $k;
                            $add_selected_to_registration['CourseAdd'][] = $published_detail['published_course_id'];
                        }
                    }
                }

                $this->request->data['CourseAdd'] = $add_selected_to_registration['CourseAdd'];

                $already_added_courses = array();
                $selected_courses_add = array();
                $count = 0;

                if (!empty($this->request->data['CourseAdd'])) {
                    foreach ($this->request->data['CourseAdd'] as $cdd => $cdv) {
                        $check = $this->CourseAdd->find('count', array(
                            'conditions' => array(
                                'CourseAdd.published_course_id' => $cdv,
                                'CourseAdd.student_id' => $this->request->data['Student']['id']
                            ),
                            'recursive' => -1
                        ));

                        // already added, unset it
                        if ($check > 0) {
                            $already_added_courses[] = $cdv;
                        } else {
                            $is_mass_add = $this->CourseAdd->PublishedCourse->field('add', array('id' => $cdv));
                            $selected_courses_add['CourseAdd'][$count]['published_course_id'] = $cdv;

                            if ($is_mass_add == 1) {
                                $selected_courses_add['CourseAdd'][$count]['department_approval'] = 1;
                                $selected_courses_add['CourseAdd'][$count]['registrar_confirmation'] = 1;
                            } else {
                                $selected_courses_add['CourseAdd'][$count]['department_approval'] = 1;
                                $selected_courses_add['CourseAdd'][$count]['registrar_confirmation'] = 1;
                                $selected_courses_add['CourseAdd'][$count]['egistrar_confirmed_by'] = $this->Auth->user(
                                    'id'
                                );
                            }

                            $selected_courses_add['CourseAdd'][$count]['student_id'] = $this->request->data['Student']['id'];
                            $selected_courses_add['CourseAdd'][$count]['semester'] = $semester;
                            $selected_courses_add['CourseAdd'][$count]['academic_year'] = $current_academic_year;

                            if (empty($student_section['Section'][0]['year_level_id'])) {
                                $selected_courses_add['CourseAdd'][$count]['year_level_id'] = 0;
                            } else {
                                $selected_courses_add['CourseAdd'][$count]['year_level_id'] = $student_section['Section'][0]['year_level_id'];
                            }
                        }

                        $count++;
                    }
                }

                if (count($already_added_courses) == count($this->request->data['CourseAdd'])) {
                    $this->Flash->info('All the selected courses were already added. You do not need to add it again.');
                    unset($this->request->data['CourseAdd']);
                    if ($this->Session->check('search_data_index')) {
                        $this->Session->delete('search_data_index');
                    }
                    $this->redirect(array('action' => 'index'));
                } else {
                    // unset($this->request->data);
                    $this->request->data['CourseAdd'] = $selected_courses_add;
                }

                $this->request->data['CourseAdd'] = $this->request->data['CourseAdd']['CourseAdd'];

                if (!empty($this->request->data['CourseAdd'])) {
                    if ($this->CourseAdd->saveAll($this->request->data['CourseAdd'], array('validate' => 'first'))) {
                        $this->Flash->success(
                            'The course add has been successful and sent to department for approval. Please don\'t forget to confirm again after department approval is done by the department.'
                        );
                        unset($this->request->data['CourseAdd']);
                        if ($this->Session->check('search_data_index')) {
                            $this->Session->delete('search_data_index');
                        }
                        $this->redirect(array('action' => 'index'));
                    } else {
                        $this->Flash->error('The course add could not be saved. Please, try again.');
                    }
                }
            } else {
                $this->Flash->error('Please select atleast one course you want to add.');
            }
        }

        $department_ids = array();
        $college_ids = array();
        $yearLevels = array();

        if (!empty($this->department_ids)) {
            $departments = $this->CourseAdd->PublishedCourse->Department->find(
                'list',
                array('conditions' => array('Department.id' => $this->department_ids, 'Department.active' => 1))
            );
            if (!empty($departments)) {
                $department_ids = array_keys($departments);
                //$yearLevels = $this->CourseAdd->YearLevel->distinct_year_level_based_on_role($this->Session->read('Auth.User')['role_id'], null , $department_ids);
            }
        } else {
            if (!empty($this->college_ids)) {
                $colleges = $this->CourseAdd->PublishedCourse->College->find(
                    'list',
                    array('conditions' => array('College.id' => $this->college_ids, 'College.active' => 1))
                );
                if (!empty($colleges)) {
                    $college_ids = array_keys($colleges);
                    //$yearLevels = $this->CourseAdd->YearLevel->distinct_year_level_based_on_role($this->Session->read('Auth.User')['role_id'], $college_ids);
                }
            } else {
                //$yearLevels = $this->CourseAdd->YearLevel->distinct_year_level();
            }
        }

        if (isset($this->request->data['Search']['college_id']) && !empty($this->request->data['Search']['college_id'])) {
            $departments = $this->CourseAdd->PublishedCourse->Department->find(
                'list',
                array(
                    'conditions' => array(
                        'Department.college_id' => $this->request->data['Search']['college_id'],
                        'Department.active' => 1
                    )
                )
            );
        }


        if (!empty($this->request->data) && isset($this->request->data['continue'])) {
            debug($this->request->data);

            $this->__init_search_index();

            $options = array();
            $program_ids = array();
            $program_type_ids = array();
            $studentnumber = '';
            $department_name = '';
            $college_name = '';

            $student_lists = array();

            if (!empty($this->request->data['Search']['program_id'])) {
                $program_ids[] = $this->request->data['Search']['program_id'];
            } else {
                $program_ids = $this->program_id;
            }

            if (!empty($this->request->data['Search']['program_type_id'])) {
                $program_type_ids = $this->__getEquivalentProgramTypes(
                    $this->request->data['Search']['program_type_id']
                );
            } else {
                $program_type_ids = $this->program_type_id;
            }

            debug($program_type_ids);
            debug($yearLevels);

            if (!empty($this->request->data['Search']['year_level_name'])) {
                $year_level_name = $this->request->data['Search']['year_level_name'];
            } else {
                $year_level_name = '1st';
            }

            if (!empty($this->request->data['Search']['academicyear'])) {
                $acadamic_year = $this->request->data['Search']['academicyear'];
            } else {
                $acadamic_year = $this->AcademicYear->current_academicyear();
            }

            if (!empty($this->request->data['Search']['semester'])) {
                $semester = $this->request->data['Search']['semester'];
            } else {
                $semester = 'I';
            }

            if (!empty($this->request->data['Search']['department_id'])) {
                $department_ids = array();
                $department_name = $this->CourseAdd->Student->Department->field(
                    'Department.name',
                    array('Department.id' => $this->request->data['Search']['department_id'])
                );
                $department_ids[] = $this->request->data['Search']['department_id'];
            }

            if (!empty($this->request->data['Search']['college_id'])) {
                $college_ids = array();
                $college_name = $this->CourseAdd->Student->College->field(
                    'College.name',
                    array('College.id' => $this->request->data['Search']['college_id'])
                );
                $college_ids[] = $this->request->data['Search']['college_id'];
            }

            if (!empty($this->request->data['Search']['studentnumber'])) {
                $studentnumber = $this->request->data['Search']['studentnumber'];
            }

            if (!empty($department_ids)) {
                $student_lists = ClassRegistry::init('StudentExamStatus')->getRegisteredStudentListForAddDrop(
                    $acadamic_year,
                    $semester,
                    $program_ids,
                    $program_type_ids,
                    $department_ids,
                    $year_level_name,
                    $freshman = 0,
                    $studentnumber
                );
            } else {
                if (!empty($college_ids)) {
                    $student_lists = ClassRegistry::init('StudentExamStatus')->getRegisteredStudentListForAddDrop(
                        $acadamic_year,
                        $semester,
                        $program_ids,
                        $program_type_ids,
                        $college_ids,
                        $year_level_name = '',
                        $freshman = 1,
                        $studentnumber
                    );
                }
            }

            if (empty($student_lists)) {
                $this->Flash->info(
                    'There is no  ' . (!empty($department_name) ? $year_level_name . ' year ' . $department_name . ' department' : $college_name . ' freshman ') . ' student who is registered for ' . $acadamic_year . ' academic year, ' . $semester . ' semester to process course add.'
                );
                $hide_search = true;
            }

            $this->set(compact('student_lists', 'collegess', 'yearLevels'));
        }

        $this->set(compact('yearLevels', 'departments', 'colleges', 'collegess', 'hide_search'));
    }

    public function approveAdds()
    {

        $flag = false;

        $current_acy = $this->AcademicYear->currentAcademicYear();

        if (is_numeric(ACY_BACK_COURSE_ADD_DROP_APPROVAL) && ACY_BACK_COURSE_ADD_DROP_APPROVAL) {
            $ac_yearsAddDrop = $this->AcademicYear->academicYearInArray(
                ((explode('/', $current_acy)[0]) - ACY_BACK_COURSE_ADD_DROP_APPROVAL),
                (explode('/', $current_acy)[0])
            );
        } else {
            $ac_yearsAddDrop[$current_acy] = $current_acy;
        }

        debug($ac_yearsAddDrop);

        $ac_yearsAddDropForDropDown = $ac_yearsAddDrop;
        $ac_yearsAddDrop = array_keys($ac_yearsAddDrop);
        $acy_ranges_by_comma_quoted = "'" . implode("', '", $ac_yearsAddDrop) . "'";


        if (!empty($this->request->data['CourseAdd']) && isset($this->request->data['approverejectadd'])) {
            $requestedAddRequestCount = count($this->request->data['CourseAdd']);
            $alreadyAdddedCount = 0;
            $alreadyProcessedCount = 0;
            $aboveMaxCreditPerSemesterCount = 0;
            $noRegistrationOnTheSemesterCount = 0;

            $autoRejectedRequests = 0;
            $acceptedRequests = 0;
            $rejectedRequests = 0;
            $processedRequests = 0;



            foreach ($this->request->data['CourseAdd'] as $k => &$v) {
                $alreadyAddded = $this->CourseAdd->find('count', array(
                    'conditions' => array(
                        'CourseAdd.student_id' => $v['student_id'],
                        'CourseAdd.published_course_id' => $v['published_course_id'],
                        'CourseAdd.registrar_confirmation = 1',
                    )
                ));

                if (!$alreadyAddded) {
                    if ($this->role_id == ROLE_DEPARTMENT || $this->role_id == ROLE_COLLEGE) {
                        if ($v['department_approval'] == '') {
                            unset($this->request->data['CourseAdd'][$k]);
                            //continue;
                        } else {
                            if ($v['auto_rejected'] == 1) {
                                $autoRejectedRequests++;
                            } else {
                                if ($v['department_approval'] == 1) {
                                    $acceptedRequests++;
                                    $processedRequests++;
                                } else {
                                    if ($v['department_approval'] == 0) {
                                        $rejectedRequests++;
                                        $processedRequests++;
                                    }
                                }
                            }
                        }

                        $already_processed = $this->CourseAdd->find('count', array(
                            'conditions' => array(
                                'CourseAdd.student_id' => $v['student_id'],
                                'CourseAdd.published_course_id' => $v['published_course_id'],
                                'OR' => array(
                                    'CourseAdd.department_approval = 1',
                                    'CourseAdd.department_approval = 0',
                                )
                            )
                        ));

                        if ($already_processed) {
                            $alreadyProcessedCount++;
                            unset($this->request->data['CourseAdd'][$k]);
                            //continue;
                        }
                    } else {
                        if ($this->role_id == ROLE_REGISTRAR) {
                            if ($v['registrar_confirmation'] == '') {
                                unset($this->request->data['CourseAdd'][$k]);
                                //continue;
                            } else {
                                if ($v['auto_rejected'] == 1) {
                                    $autoRejectedRequests++;
                                } else {
                                    if ($v['registrar_confirmation'] == 1) {
                                        $acceptedRequests++;
                                        $processedRequests++;
                                    } else {
                                        if ($v['registrar_confirmation'] == 0) {
                                            $rejectedRequests++;
                                            $processedRequests++;
                                        }
                                    }
                                }
                            }

                            $already_processed = $this->CourseAdd->find('count', array(
                                'conditions' => array(
                                    'CourseAdd.student_id' => $v['student_id'],
                                    'CourseAdd.published_course_id' => $v['published_course_id'],
                                    'OR' => array(
                                        'CourseAdd.registrar_confirmation = 1',
                                        'CourseAdd.registrar_confirmation = 0',
                                    )
                                )
                            ));

                            if ($already_processed) {
                                $alreadyProcessedCount++;
                                unset($this->request->data['CourseAdd'][$k]);
                                //continue;
                            }
                        }
                    }

                    //debug($v);

                    debug($v['student_id']);

                    $maxLoad = $this->CourseAdd->Student->calculateStudentLoad(
                        $v['student_id'],
                        $v['semester'],
                        $v['academic_year']
                    );
                    //todo read maximum load for course add from general settings
                    debug($maxLoad);

                    $allowedMaximum = ClassRegistry::init('AcademicCalendar')->maximumCreditPerSemester(
                        $v['student_id']
                    );
                    debug($allowedMaximum);

                    if (is_numeric($maxLoad) && $maxLoad > 0) {
                        // TODO: check max load + current Add < allowed credit for semester
                        if ($maxLoad <= $allowedMaximum || (($this->role_id == ROLE_REGISTRAR && ($maxLoad + $v['credit']) > $allowedMaximum && $v['department_approval'] == 1))) {
                            if ($this->role_id == ROLE_DEPARTMENT || $this->role_id == ROLE_COLLEGE) {
                                if ($v['department_approval'] == '') {
                                    unset($this->request->data['CourseAdd'][$k]);
                                } else {
                                    $v['department_approved_by'] = $this->Auth->user('id');
                                    $v['modified'] = date('Y-m-d H:i:s');
                                }
                            } else {
                                if ($this->role_id == ROLE_REGISTRAR) {
                                    if ($v['registrar_confirmation'] == '') {
                                        unset($this->request->data['CourseAdd'][$k]);
                                    } else {
                                        $v['registrar_confirmed_by'] = $this->Auth->user('id');
                                        $v['modified'] = date('Y-m-d H:i:s');
                                    }
                                }
                            }
                        } else {
                            if (($maxLoad + $v['credit']) > $allowedMaximum) {
                                $aboveMaxCreditPerSemesterCount++;
                                //unset($this->request->data['CourseAdd'][$k]);
                            }
                        }
                    } else {
                        // Max Load = 0, there is no registratiom
                        $noRegistrationOnTheSemesterCount++;
                        //unset($this->request->data['CourseAdd'][$k]);
                    }
                } else {
                    $alreadyAdddedCount++;
                    unset($this->request->data['CourseAdd'][$k]);
                }
            }

            //debug($this->request->data['CourseAdd']);
            //$this->set($this->request->data);

            if (!empty($this->request->data['CourseAdd']) && isset($this->request->data['approverejectadd'])) {
                debug($this->request->data['CourseAdd']);
                if ($this->CourseAdd->saveAll($this->request->data['CourseAdd'], array('validate' => 'first'))) {
                    if ($requestedAddRequestCount == $processedRequests) {
                        $this->Flash->success(
                            'All of the selected (' . $requestedAddRequestCount . ') course add ' . ($processedRequests <= 1 ? 'request is' : 'requests are') . ' ' . ($this->role_id == ROLE_DEPARTMENT ? ' approved successfully and sent to registrar for confirmation' : ' confirmed successfully') . '.'
                        );
                    } else {
                        $this->Flash->success(
                            'Out of ' . $requestedAddRequestCount . ' available course add ' . ($requestedAddRequestCount <= 1 ? 'request' : 'requests') . ', ' . $acceptedRequests . ' add ' . ($acceptedRequests <= 1 ? 'request is' : 'requests are') . ' ' . ($this->role_id == ROLE_DEPARTMENT ? ' approved successfully and sent to registrar for confirmation' . ($rejectedRequests ? ' and ' . $rejectedRequests . ' add ' . ($rejectedRequests == 1 ? 'request is rejected' : 'requests are rejected') : '') . '.' : ' confirmed successfully' . ($rejectedRequests ? ' and ' . $rejectedRequests . ' add ' . ($rejectedRequests == 1 ? 'request is rejected' : 'requests are rejected') : '') . '.') . ($autoRejectedRequests ? ' Auto rejected: ' . $autoRejectedRequests . ' requests. (Over Credit: ' . $aboveMaxCreditPerSemesterCount . ', With No Registration: ' . $noRegistrationOnTheSemesterCount . ')' : '') . ''
                        );
                    }
                    $flag = true;
                    $this->redirect(array('action' => 'approve_adds'));
                } else {
                    $this->Flash->error(
                        'Could not ' . ($this->role_id == ROLE_REGISTRAR ? 'confirm' : 'approve') . ' ' . $requestedAddRequestCount . ' available course add ' . ($requestedAddRequestCount <= 1 ? 'request' : 'requests') . '. Please, try again.'
                    );
                }
            } else {
                $this->Flash->error(
                    'Could not ' . ($this->role_id == ROLE_REGISTRAR ? 'confirm' : 'approve') . ' ' . $requestedAddRequestCount . ' available course add ' . ($requestedAddRequestCount <= 1 ? 'request' : 'requests') . '. Please try again.'
                );
            }
        }

        //read from session
        // Function to load/save search criteria.

        /* if ($this->Session->read('search_data')) {
			$this->request->data['getaddsection'] = true;
			//$this->request->data['Student'] = $this->Session->read('search_data');
			$this->set('hide_search', true);
		} */

        if (!empty($this->request->data) && isset($this->request->data['getaddsection'])) {
            $everythingfine = false;
            switch ($this->request->data) {
                case empty($this->request->data['Student']['semester']):
                    $this->Flash->error('Please select the semester you want to approve add requests.');
                    break;
                case empty($this->request->data['Student']['program_id']):
                    $this->Flash->error('Please select the program you want to approve course add request.');
                    break;
                case empty($this->request->data['Student']['program_type_id']):
                    $this->Flash->error('Please select the program type you want to approve course add.');
                    break;
                default:
                    $everythingfine = true;
            }

            if ($everythingfine) {
                $section_organized_published_course = array();
                $department_id = null;

                if (!empty($this->request->data['Student']['department_id'])) {
                    $department_id = $this->request->data['Student']['department_id'];
                } else {
                    if ($this->role_id == ROLE_DEPARTMENT) {
                        $department_id = $this->department_id;
                    } else {
                        if ($this->role_id == ROLE_REGISTRAR && !empty($this->department_ids)) {
                            $department_id = $this->department_ids;
                        }
                    }
                }

                $program_type_id = $this->AcademicYear->equivalent_program_type(
                    $this->request->data['Student']['program_type_id']
                );

                if (!empty($this->request->data['Student']['year_level_id'])) {
                    $sections = $this->CourseAdd->Student->Section->find('list', array(
                        'conditions' => array(
                            'Section.department_id' => $department_id,
                            'Section.year_level_id' => $this->request->data['Student']['year_level_id'],
                            'Section.program_id' => $this->request->data['Student']['program_id'],
                            'Section.program_type_id' => $program_type_id,
                            'Section.archive' => 0,
                        ),
                        'order' => array(
                            'Section.academicyear DESC',
                            'Section.name ASC',
                            'Section.year_level_id DESC',
                        ),
                    ));
                } else {
                    $sections = $this->CourseAdd->Student->Section->find('list', array(
                        'conditions' => array(
                            'Section.department_id' => $department_id,
                            'Section.program_id' => $this->request->data['Student']['program_id'],
                            'Section.program_type_id' => $program_type_id,
                            'Section.archive' => 0,
                        ),
                        'order' => array(
                            'Section.academicyear DESC',
                            'Section.name ASC',
                            'Section.year_level_id DESC',
                        ),
                    ));
                }

                // query according their roles
                $this->CourseAdd->Student->bindModel(array('hasMany' => array('StudentsSection')));

                if ($this->role_id == ROLE_REGISTRAR) {
                    $courseAdds = $this->CourseAdd->find('all', array(
                        'conditions' => array(
                            'Student.department_id' => $this->request->data['Student']['department_id'],
                            'Student.program_id' => $this->request->data['Student']['program_id'],
                            'Student.program_type_id' => $program_type_id,
                            'CourseAdd.semester' => $this->request->data['Student']['semester'],
                            'CourseAdd.academic_year like' => $this->request->data['Student']['academic_year'] . '%',
                            //'CourseAdd.academic_year IN (' . $acy_ranges_by_comma_quoted . ')',
                            'CourseAdd.department_approval = 1',
                            'CourseAdd.registrar_confirmed_by is null',
                            'Student.graduated' => 0,
                            'PublishedCourse.section_id is not null',
                            'PublishedCourse.course_id is not null'
                        ),
                        'contain' => array(
                            'PublishedCourse' => array(
                                'Course' => array(
                                    'Prerequisite',
                                    'Curriculum' => array('id', 'name', 'year_introduced', 'type_credit', 'active'),
                                    'fields' => array(
                                        'id',
                                        'credit',
                                        'course_detail_hours',
                                        'course_title',
                                        'course_code'
                                    )
                                ),
                                'Section' => array(
                                    'Department' => array('id', 'name', 'type', 'college_id'),
                                    'College' => array('id', 'name', 'type', 'stream', 'campus_id'),
                                    'Program' => array('id', 'name'),
                                    'ProgramType' => array('id', 'name'),
                                    'YearLevel' => array('id', 'name'),
                                    'Curriculum' => array('id', 'name', 'year_introduced', 'type_credit', 'active'),
                                    'fields' => array('id', 'name'),
                                ),
                                'fields' => array('PublishedCourse.id')
                            ),
                            'Student' => array(
                                'StudentsSection' => array(
                                    'conditions' => array(
                                        'StudentsSection.archive = 0'
                                    )
                                ),
                                'CourseRegistration' => array(
                                    'ExamGrade',
                                    'PublishedCourse' => array(
                                        'Course' => array(
                                            'Prerequisite',
                                            'fields' => array(
                                                'credit',
                                                'id',
                                                'course_detail_hours',
                                                'course_title',
                                                'course_code'
                                            )
                                        ),
                                        'Program' => array('id', 'name'),
                                        'ProgramType' => array('id', 'name'),
                                        'fields' => array('PublishedCourse.id')
                                    ),
                                    'conditions' => array(
                                        'CourseRegistration.semester' => $this->request->data['Student']['semester'],
                                        'CourseRegistration.academic_year like' => $this->request->data['Student']['academic_year'] . '%'
                                    ),
                                    'fields' => array('id', 'published_course_id')
                                ),
                                'Curriculum' => array('id', 'name', 'year_introduced', 'type_credit', 'active'),
                                'fields' => array(
                                    'Student.id',
                                    'Student.full_name',
                                    'Student.gender',
                                    'Student.studentnumber',
                                    'Student.graduated',
                                    'Student.academicyear'
                                ),
                                'order' => array(
                                    'Student.academicyear' => 'DESC',
                                    'Student.studentnumber' => 'ASC',
                                    'Student.id' => 'ASC',
                                    'Student.full_name' => 'ASC'
                                ),
                            )
                        )
                    ));
                } else {
                    //Invistage reassigment of variable while there is a containable after find all which we expect to be override but distrbuing the code.
                    //$courseAdds = $this->CourseAdd->find('all');
                    //debug($courseAdds);

                    if (!empty($this->request->data['Student']['year_level_id'])) {
                        $year_level_id = $this->request->data['Student']['year_level_id'];
                    } else {
                        $year_level_id = $this->CourseAdd->YearLevel->find(
                            'list',
                            array('conditions' => array('YearLevel.department_id' => $this->department_id))
                        );
                    }

                    $courseAdds = $this->CourseAdd->find('all', array(
                        'conditions' => array(
                            'Student.department_id' => $department_id,
                            'CourseAdd.year_level_id' => $year_level_id,
                            'Student.program_id' => $this->request->data['Student']['program_id'],
                            'Student.program_type_id' => $program_type_id,
                            'CourseAdd.semester' => $this->request->data['Student']['semester'],
                            'CourseAdd.academic_year like' => $this->request->data['Student']['academic_year'] . '%',
                            //'CourseAdd.academic_year IN (' . $acy_ranges_by_comma_quoted . ')',
                            "OR" => array(
                                'CourseAdd.department_approved_by is null',
                                'CourseAdd.department_approved_by = ""'
                            ),
                            'Student.graduated' => 0,
                            //'Student.id NOT IN (select student_id from graduate_lists)'
                        ),
                        'contain' => array(
                            'PublishedCourse' => array(
                                'Course' => array(
                                    'Prerequisite',
                                    'Curriculum' => array('id', 'name', 'year_introduced', 'type_credit', 'active'),
                                    'fields' => array(
                                        'id',
                                        'credit',
                                        'course_detail_hours',
                                        'course_title',
                                        'course_code'
                                    )
                                ),
                                'Section' => array(
                                    'Department' => array('id', 'name', 'type', 'college_id'),
                                    'College' => array('id', 'name', 'type', 'stream', 'campus_id'),
                                    'Program' => array('id', 'name'),
                                    'ProgramType' => array('id', 'name'),
                                    'YearLevel' => array('id', 'name'),
                                    'Curriculum' => array('id', 'name', 'year_introduced', 'type_credit', 'active'),
                                    'fields' => array('id', 'name'),
                                ),
                                'fields' => array('PublishedCourse.id')
                            ),
                            'Student' => array(
                                'StudentsSection' => array(
                                    'conditions' => array(
                                        'StudentsSection.archive = 0'
                                    )
                                ),
                                'CourseRegistration' => array(
                                    'ExamGrade',
                                    'PublishedCourse' => array(
                                        'Course' => array(
                                            'Prerequisite',
                                            'fields' => array(
                                                'id',
                                                'credit',
                                                'course_detail_hours',
                                                'course_title',
                                                'course_code'
                                            )
                                        ),
                                        'Program' => array('id', 'name'),
                                        'ProgramType' => array('id', 'name'),
                                        'fields' => array('PublishedCourse.id')
                                    ),
                                    'conditions' => array(
                                        'CourseRegistration.semester' => $this->request->data['Student']['semester'],
                                        'CourseRegistration.academic_year like' => $this->request->data['Student']['academic_year'] . '%'
                                    ),
                                    'fields' => array('id', 'published_course_id')
                                ),
                                'Curriculum' => array('id', 'name', 'year_introduced', 'type_credit', 'active'),
                                'fields' => array(
                                    'Student.id',
                                    'Student.full_name',
                                    'Student.gender',
                                    'Student.studentnumber',
                                    'Student.graduated',
                                    'Student.academicyear'
                                ),
                                'order' => array(
                                    'Student.academicyear' => 'DESC',
                                    'Student.studentnumber' => 'ASC',
                                    'Student.id' => 'ASC',
                                    'Student.full_name' => 'ASC'
                                ),
                            )
                        )
                    ));
                }

                if (empty($courseAdds)) {
                    $this->Flash->info(
                        'No add request found ' . (isset($this->request->data['Student']['academic_year']) ? $this->request->data['Student']['academic_year'] . ' ACY ' . (isset($this->request->data['Student']['semester']) ? ' ' . $this->request->data['Student']['semester'] . ' semester' : '') : 'in the given criteria') . ' that needs your ' . ($this->role_id == ROLE_REGISTRAR ? 'confirmation' : 'approval') . '.'
                    );
                } else {
                    $this->__init_search();

                    /* if (!empty($courseAdds)) {
						foreach ($courseAdds as $pk => &$pv) {
							if (array_key_exists($pv['Student']['StudentsSection'][0]['section_id'], $sections)) {
								//$pv['Student']['max_load'] = $this->CourseAdd->Student->calculateStudentLoad($pv['Student']['id'], $this->request->data['Student']['semester'], $this->request->data['Student']['academic_year']);
								$section_organized_published_course[$pv['Student']['StudentsSection'][0]['section_id']][] = $pv;
							}
						}
					} */

                    $section_organized_published_course = $this->CourseAdd->reformatApprovalRequest($courseAdds);

                    $this->set('hide_search', true);
                    $this->set('coursesss', $section_organized_published_course);
                    $this->set(compact('sections'));
                }

                $program_name = $this->CourseAdd->Student->Program->field(
                    'Program.name',
                    array('Program.id' => $this->request->data['Student']['program_id'])
                );
                $program_type_name = $this->CourseAdd->Student->ProgramType->field(
                    'ProgramType.name',
                    array('ProgramType.id' => $this->request->data['Student']['program_type_id'])
                );
                $academic_year = $this->request->data['Student']['academic_year'];
                $semester = $this->request->data['Student']['semester'];
                $department_name = $this->CourseAdd->Student->Department->field(
                    'Department.name',
                    array('Department.id' => $department_id)
                );

                $this->set(
                    compact(
                        'sections',
                        'year_level_id',
                        'program_name',
                        'program_type_name',
                        'academic_year',
                        'semester',
                        'department_name'
                    )
                );
            }
        }

        if (isset($this->request->data['Student']['academic_year']) && !empty($this->request->data['Student']['academic_year'])) {
            $acYear = $this->request->data['Student']['academic_year'];
        } else {
            $acYear = $this->AcademicYear->current_academicyear();
        }

        $programTypes = $this->CourseAdd->PublishedCourse->ProgramType->find('list');
        $programs = $this->CourseAdd->PublishedCourse->Program->find('list');

        if ($this->role_id == ROLE_REGISTRAR) {
            $department_ids = array();
            $college_ids = array();

            if (!empty($this->department_ids)) {
                $departments = $this->CourseAdd->PublishedCourse->Department->find('list', array(
                    'conditions' => array(
                        'Department.id' => $this->department_ids,
                        'Department.active' => 1
                    )
                ));
            } else {
                if (!empty($this->college_ids)) {
                    $departments = $this->CourseAdd->PublishedCourse->Department->find('list', array(
                        'conditions' => array(
                            'Department.college_id' => $this->college_ids,
                            'Department.active' => 1
                        )
                    ));
                }
            }

            $programTypes = $this->CourseAdd->PublishedCourse->ProgramType->find(
                'list',
                array('conditions' => array('ProgramType.id' => $this->program_type_id))
            );
            $programs = $this->CourseAdd->PublishedCourse->Program->find(
                'list',
                array('conditions' => array('Program.id' => $this->program_id))
            );
        } else {
            if ($this->role_id == ROLE_COLLEGE) {
                $departments = $this->CourseAdd->PublishedCourse->Department->find('list', array(
                    'conditions' => array(
                        'Department.college_id' => $this->college_id,
                        'Department.active' => 1
                    )
                ));

                $courseAdds = $this->CourseAdd->courseAddRequestWaitingApproval(
                    null,
                    0,
                    $this->college_id,
                    1,
                    null,
                    null,
                    $ac_yearsAddDrop
                );

                if (empty($courseAdds) && !$flag) {
                    $this->Flash->info('No add request requests found that needs your approval.');
                } else {
                    $this->set(
                        'coursesss',
                        $this->CourseAdd->reformatApprovalRequest($courseAdds, null, $acYear, $this->college_id)
                    );
                }
            }
        }

        if ($this->role_id == ROLE_REGISTRAR) {
            $yearLevels = $this->CourseAdd->YearLevel->distinct_year_level();
            $this->set(compact('yearLevels'));

            $courseAdds = $this->CourseAdd->courseAddRequestWaitingApproval(
                $this->department_ids,
                1,
                null,
                1,
                $this->program_id,
                $this->program_type_id,
                $ac_yearsAddDrop
            );

            if (empty($courseAdds) && !$flag) {
                $this->Flash->info('No add request request found that needs your confirmation.');
            } else {
                $this->set(
                    'coursesss',
                    $this->CourseAdd->reformatApprovalRequest($courseAdds, $this->department_ids, $acYear)
                );
            }
        } else {
            if ($this->role_id == ROLE_DEPARTMENT) {
                $courseAdds = $this->CourseAdd->courseAddRequestWaitingApproval(
                    $this->department_id,
                    2,
                    null,
                    0,
                    null,
                    null,
                    $ac_yearsAddDrop
                );
                //debug($courseAdds);

                if (empty($courseAdds) && !$flag) {
                    $this->Flash->info('No add request in the system that needs approval.');
                } else {
                    $this->set(
                        'coursesss',
                        $this->CourseAdd->reformatApprovalRequest($courseAdds, $this->department_id, $acYear)
                    );
                }

                $yearLevels = $this->CourseAdd->YearLevel->find(
                    'list',
                    array('conditions' => array('YearLevel.department_id' => $this->department_id))
                );
                $this->set(compact('yearLevels'));
            } else {
                $yearLevels = $this->CourseAdd->YearLevel->find('list');
                $this->set(compact('yearLevels'));
            }
        }

        $this->set(compact('departments', 'programTypes', 'programs'));
    }


    public function delete($id = null)
    {

        if (!$id) {
            $this->Flash->error('Invalid Course Add ID');
            return $this->redirect(array('action' => 'index'));
        }

        $this->CourseAdd->id = $id;

        if (!$this->CourseAdd->exists()) {
            $this->Flash->error('Invalid Course Add ID');
            return $this->redirect(array('action' => 'index'));
        }

        $courseAddDetails = $this->CourseAdd->find('first', array(
            'conditions' => array('CourseAdd.id' => $id),
            'contain' => array(
                'PublishedCourse' => array(
                    'Course' => array(
                        'Curriculum' => array('id', 'name', 'year_introduced', 'type_credit', 'active'),
                    ),
                ),
                'ExamGrade',
                'ExamResult.course_add=1',
                'YearLevel' => array('fields' => array('id', 'name')),
                'Student' => array(
                    'fields' => array(
                        'id',
                        'full_name',
                        'gender',
                        'studentnumber',
                        'college_id',
                        'department_id',
                        'program_id',
                        'program_type_id',
                        'graduated',
                        'academicyear',
                        'admissionyear'
                    ),
                    'Curriculum' => array('id', 'name', 'year_introduced', 'type_credit', 'active'),
                ),
            ),
            'recursive' => -1
        ));

        debug($courseAddDetails);

        $deletion_allowed = false;
        $error_message = '';

        $course_name = (isset($courseAddDetails['PublishedCourse']['Course']['id']) && !empty($courseAddDetails['PublishedCourse']['Course']['course_code_title']) ? $courseAddDetails['PublishedCourse']['Course']['course_code_title'] : '');
        $student_full_name = (isset($courseAddDetails['Student']['id']) && !empty($courseAddDetails['Student']['id']) ? $courseAddDetails['Student']['full_name'] . ' (' . $courseAddDetails['Student']['studentnumber'] . ')' : '');

        if ((isset($courseAddDetails['ExamResult']) && !empty($courseAddDetails['ExamResult'])) || (isset($courseAddDetails['ExamGrade']) && !empty($courseAddDetails['ExamGrade']))) {
            debug('Course Add is associated with Exam Result or Exam Grade.');
            $error_message .= ' This course add is associated with exam result or exam grade.';
            $deletion_allowed = false;
        } else {
            if (empty($course_name)) {
                debug(' Coudn\'t load Course Title for the Published Course.');
                $error_message .= ' Coudn\'t load course title for the published course.';
                $deletion_allowed = false;

                if ($this->Session->read('Auth.User')['role_id'] == ROLE_REGISTRAR) {
                    $deletion_allowed = true;
                }
            }

            if ((isset($courseAddDetails['CourseAdd']['registrar_confirmation']) && !empty($courseAddDetails['CourseAdd']['registrar_confirmation'])) || (isset($courseAddDetails['CourseAdd']['department_approval']) && !empty($courseAddDetails['CourseAdd']['department_approval']))) {
                debug('Course Add is approved by either Registrar or Departmnt.');
                $error_message .= ' This course add is approved by either Registrar or Departmnt.';
                $deletion_allowed = false;

                if ($this->Session->read('Auth.User')['role_id'] == ROLE_REGISTRAR) {
                    $deletion_allowed = true;
                }
            }

            if (!isset($courseAddDetails['PublishedCourse']) || (isset($courseAddDetails['PublishedCourse']) && empty($courseAddDetails['PublishedCourse']))) {
                debug('Course Add have is no longer associated to PublishedCourse (PublishedCourse ID not found)');
                $error_message .= ' This course add is no longer associated to a published course (PublishedCourse ID not found).';
                $deletion_allowed = true;
            }

            if (isset($courseAddDetails['PublishedCourse']['id']) && !isset($courseAddDetails['PublishedCourse']['Course']['id'])) {
                debug('Course Add is no longer associated to Published Course (Course ID not found)');
                $error_message .= ' This course add is no longer associated to a published course (Course ID not found).';
                $deletion_allowed = true;
            }


            if (isset($courseAddDetails['CourseAdd']['auto_rejected']) && !empty($courseAddDetails['CourseAdd']['auto_rejected'])) {
                debug('Course Add is from Auto Rejected Request.');
                $error_message .= ' This course add is from auto rejected request.';
                $deletion_allowed = false;
            }

            if (isset($courseAddDetails['CourseAdd']['cron_job']) && !empty($courseAddDetails['CourseAdd']['cron_job'])) {
                debug('Course Add is from Cron Job.');
                $error_message .= ' This course add is from cron job.';
                $deletion_allowed = false;
            }

            if (isset($courseAddDetails['Student']['graduated']) && $courseAddDetails['Student']['graduated']) {
                debug(
                    (!empty($student_full_name) ? $student_full_name : 'The selected student') . ' is Graduated Student.'
                );
                $error_message .= (!empty($student_full_name) ? $student_full_name : 'The selected student') . ' is Graduated Student.';
                $deletion_allowed = false;
            }
        }

        if ($this->Session->read(
                'Auth.User'
            )['role_id'] == ROLE_REGISTRAR && !empty($courseAddDetails['Student']['id'])) {
            if (!empty($this->department_ids) && !empty($courseAddDetails['Student']['department_id']) && !in_array(
                    $courseAddDetails['Student']['department_id'],
                    $this->department_ids
                )) {
                //$error_message .= 'You are not authorized to drop courses for '. (!empty($student_full_name) ? $student_full_name : 'the selected student');
                $error_message .= 'You are not authorized to cancel course adds for the selected student.';
                $deletion_allowed = false;
            }
            if (!empty($this->college_ids) && !empty($courseAddDetails['Student']['college_id']) && !in_array(
                    $courseAddDetails['Student']['college_id'],
                    $this->college_ids
                )) {
                //$error_message .= 'You are not authorized to cancel course adds for '. (!empty($student_full_name) ? $student_full_name : 'the selected student');
                $error_message .= 'You are not authorized to cancel course adds for the selected student.';
                $deletion_allowed = false;
            }
        }


        if ($this->Session->read(
                'Auth.User'
            )['role_id'] == ROLE_STUDENT && !empty($courseAddDetails['Student']['id']) && $this->student_id !== $courseAddDetails['Student']['id']) {
            $error_message .= 'You are not authorized to cancel course adds for ' . (!empty($student_full_name) ? $student_full_name : 'the selected student');
            $error_message .= 'You are not authorized to cancel course adds for other student. Don\'t try this again, your action is logged and reported.';
        }

        //exit();
        //$this->request->allowMethod('post', 'delete');

        if ($this->Session->read('Auth.User')['role_id'] == ROLE_STUDENT && empty($error_message)) {
            if ($this->CourseAdd->delete($id)) {
                $this->Flash->success(
                    'Course add cancellation successfull' . (!empty($course_name) ? ' for ' . $course_name . '.' : '.')
                );
                //return $this->redirect(array('action' => 'index'));
                $this->redirect(Router::url($this->referer(), true));
            }
        } else {
            if ($this->Session->read('Auth.User')['role_id'] == ROLE_REGISTRAR && $deletion_allowed) {
                if ($this->CourseAdd->delete($id)) {
                    $this->Flash->success(
                        'Course add cancellation is successfull for ' . (!empty($student_full_name) ? $student_full_name : 'the selected student') . (!empty($course_name) ? ' for ' . $course_name . '.' : '.') . (!empty($error_message) ? ' With the following additional alerts: ' . $error_message : '')
                    );
                    //return $this->redirect(array('action' => 'index'));
                    $this->redirect(Router::url($this->referer(), true));
                }
            }
        }

        $this->Flash->error(
            'Course add was not cancelled.' . (!empty($error_message) ? $error_message : ' It is associated to Exam Grades.')
        );
        //return $this->redirect(array('action' => 'index'));
        $this->redirect(Router::url($this->referer(), true));
    }

    public function studentAddCourses()
    {

        $current_academic_year = $this->AcademicYear->currentAcademicYear();

        $student_section_exam_status = $this->CourseAdd->Student->getStudentSection(
            $this->student_id,
            $current_academic_year
        );

        $studentDetails = $this->CourseAdd->Student->find(
            'first',
            array('conditions' => array('Student.id' => $this->student_id), 'recursive' => -1)
        );
        $getRegistrationDeadLine = false;

        $latestAcSemester = ClassRegistry::init('CourseRegistration')->getLastestStudentSemesterAndAcademicYear(
            $this->student_id,
            $current_academic_year,
            1
        );

        $latest_academic_year = $latestAcSemester['academic_year'];
        $latestSemester = $semester = $latestAcSemester['semester'];

        $current_section_id = (!empty($student_section_exam_status['Section']['id']) ? $student_section_exam_status['Section']['id'] : '');


        if (empty($student_section_exam_status)) {
            $this->Flash->warning(
                'You are not assiged to any section for ' . (isset($latest_academic_year) && !empty($latest_academic_year) ? $latest_academic_year : $this->AcademicYear->current_academicyear(
                )) . ' academic year. Communicate your department and make sure you have a proper section assignment in ' . (isset($latest_academic_year) && !empty($latest_academic_year) ? $latest_academic_year : $this->AcademicYear->current_academicyear(
                )) . ' before trying to register or add courses.'
            );
            $this->redirect(array('controller' => 'students', 'action' => 'student_academic_profile', $this->student_id)
            );
            // $student_active_section_ac_year = 0;
            // $this->redirect('/');
        } else {
            if (isset($student_section_exam_status['Section']) && !$student_section_exam_status['Section']['archive'] && !$student_section_exam_status['Section']['StudentsSection']['archive']) {
                $current_academic_year = $latest_academic_year = $student_active_section_ac_year = $student_section_exam_status['Section']['academicyear'];
                $latestAcSemester = ClassRegistry::init('CourseRegistration')->getLastestStudentSemesterAndAcademicYear(
                    $this->student_id,
                    $current_academic_year,
                    1
                );
                $latestSemester = $semester = $latestAcSemester['semester'];
                $current_section_id = $student_section_exam_status['Section']['id'];
            }
        }

        $check_for_registration = ClassRegistry::init('CourseRegistration')->find(
            'count',
            array(
                'conditions' => array(
                    'CourseRegistration.student_id' => $this->student_id,
                    'CourseRegistration.academic_year' => $current_academic_year,
                    'CourseRegistration.semester' => $semester,
                    'CourseRegistration.section_id' => $current_section_id
                )
            )
        );
        debug($check_for_registration);

        if (!$check_for_registration) {
            $this->Flash->info(
                'You can\'t add courses for ' . ($latestSemester == 'I' ? '1st' : ($latestSemester == 'II' ? '2nd' : '3rd')) . ' semester of ' . $latest_academic_year . ' academic year.  You have to register first for at least one course from ' . $student_section_exam_status['Section']['name'] . ' section.'
            );
            $this->redirect(array('controller' => 'students', 'action' => 'student_academic_profile', $this->student_id)
            );
            /* $student_active_section_ac_year = 0;
			$this->redirect('/'); */
        }

        $year_level_name = '';

        if (!empty($this->department_id)) {
            $year_level_name = $year_level_id = $this->CourseAdd->YearLevel->field(
                'name',
                array('id' => $student_section_exam_status['Section']['year_level_id'])
            );
            $getRegistrationDeadLine = ClassRegistry::init('CourseRegistration')->AcademicCalendar->check_add_date_end(
                $current_academic_year,
                $semester,
                $this->department_id,
                $year_level_id,
                $studentDetails['Student']['program_id'],
                $studentDetails['Student']['program_type_id']
            );
        } else {
            if (!empty($this->college_id)) {
                $getRegistrationDeadLine = ClassRegistry::init(
                    'CourseRegistration'
                )->AcademicCalendar->check_add_date_end(
                    $current_academic_year,
                    $semester,
                    'pre_' . $this->college_id,
                    0,
                    $studentDetails['Student']['program_id'],
                    $studentDetails['Student']['program_type_id']
                );
            }
        }

        debug($getRegistrationDeadLine);

        $this->set(
            compact(
                'student_section_exam_status',
                'current_academic_year',
                'student_active_section_ac_year',
                'year_level_name'
            )
        );

        /* if ($getRegistrationDeadLine == 0 || $getRegistrationDeadLine == 1) {

		} else {
			$add_start_date = $getRegistrationDeadLine;
			$getRegistrationDeadLine = 0;
		} */


        if ($getRegistrationDeadLine != 1) {
            $add_start_date = $getRegistrationDeadLine;

            if (!empty($add_start_date) && $this->__isDate($add_start_date)) {
                $this->Flash->info(
                    'Course add will start on ' . (date(
                        'M d, Y',
                        strtotime($add_start_date)
                    )) . '  for ' . ($latestSemester == 'I' ? '1st' : ($latestSemester == 'II' ? '2nd' : '3rd')) . ' semester of ' . $latest_academic_year . ' academic year. Please come back and add courses on the date specified.'
                );
            } else {
                if (!$getRegistrationDeadLine) {
                    $this->Flash->default(
                        'Course add start and end date is not defined for ' . ($latestSemester == 'I' ? '1st' : ($latestSemester == 'II' ? '2nd' : '3rd')) . ' semester of ' . $latest_academic_year . ' academic year. You can <a href="' . BASE_URL_HTTPS . 'pages/academic_calender" target="_blank">check Academic Calendar here</a> and come back later when it is defined.',
                        array(
                            'params' => array('type' => 'Info', 'class' => 'info-box info-message'),
                            'escape' => false
                        )
                    );
                } else {
                    $deadlinepassed = true;
                    $this->set(compact('deadlinepassed'));
                    $this->Flash->warning(
                        'Course add deadline is passed for ' . ($latestSemester == 'I' ? '1st' : ($latestSemester == 'II' ? '2nd' : '3rd')) . ' semester of ' . $latest_academic_year . ' academic year. Please advise the registrar.'
                    );
                }
            }

            /* if (isset($add_start_date) && !empty($add_start_date)) {
				$this->Flash->info('Course add start date is on ' . (date('M d, Y', strtotime($add_start_date))) . '. You can not add courses now, please come back later on the date specified.');
			} else {
				$this->Flash->info('Course Add deadline passed. You can not add courses now, please consult the registrar.');
			} */

            // don't forget to uncomment this after checking, Neway
            $this->redirect(
                array(
                    'controller' => 'courseRegistrations',
                    'action' => 'index',
                    (str_replace('/', '-', $latest_academic_year)),
                    $latestSemester
                )
            );
        } else {
            $student_section = $this->CourseAdd->Student->student_academic_detail(
                $this->student_id,
                $current_academic_year
            );

            //debug($student_section);
            debug($studentDetails['Student']['department_id']);

            //exit();

            if (!is_null($studentDetails['Student']['department_id']) && $studentDetails['Student']['department_id']) {
                $published_detail = array(
                    'academic_year' => $current_academic_year,
                    'semester' => $semester,
                    'student_id' => $this->student_id,
                    'year_level_id' => $student_section_exam_status['Section']['year_level_id']
                );
            } else {
                $published_detail = array(
                    'academic_year' => $current_academic_year,
                    'semester' => $semester,
                    'student_id' => $this->student_id,
                    'OR' => array(
                        'year_level_id IS NULL',
                        'year_level_id = ""',
                        'year_level_id = 0',
                    )
                );
            }

            if (!empty($student_section_exam_status['Section'])) {
                $ownDepartmentPublishedForAdd = array();

                if (!is_null(
                        $studentDetails['Student']['department_id']
                    ) && $studentDetails['Student']['department_id']) {
                    $ownDepartmentPublishedForAdd = $this->CourseAdd->PublishedCourse->find('all', array(
                        'conditions' => array(
                            'PublishedCourse.semester' => $semester,
                            'PublishedCourse.department_id' => $this->department_id,
                            'PublishedCourse.section_id' => $student_section_exam_status['Section']['id'],
                            'PublishedCourse.academic_year LIKE ' => $current_academic_year . '%',
                            'PublishedCourse.add' => 1
                        ),
                        'contain' => array('Course')
                    ));
                } else {
                    $ownDepartmentPublishedForAdd = $this->CourseAdd->PublishedCourse->find('all', array(
                        'conditions' => array(
                            'PublishedCourse.semester' => $semester,
                            'PublishedCourse.college_id' => $this->college_id,
                            'PublishedCourse.section_id' => $student_section_exam_status['Section']['id'],
                            'PublishedCourse.academic_year LIKE ' => $current_academic_year . '%',
                            'PublishedCourse.add' => 1
                        ),
                        'contain' => array('Course')
                    ));
                }

                $pub_own_as_add_courses = array();
                $count = 0;

                if (!empty($ownDepartmentPublishedForAdd)) {
                    foreach ($ownDepartmentPublishedForAdd as $ownIndex => $ownValue) {
                        $already_added = $this->CourseAdd->find('count', array(
                            'conditions' => array(
                                'CourseAdd.student_id' => $this->student_id,
                                'CourseAdd.published_course_id' => $ownValue['PublishedCourse']['id']
                            )
                        ));

                        if ($already_added > 0) {
                            $pub_own_as_add_courses[$count] = $ownValue;
                            $pub_own_as_add_courses[$count]['already_added'] = 1;
                        } else {
                            $pub_own_as_add_courses[$count] = $ownValue;
                            $pub_own_as_add_courses[$count]['already_added'] = 0;
                        }
                    }

                    $ownDepartmentPublishedForAdd = $pub_own_as_add_courses;

                    $this->set(compact('ownDepartmentPublishedForAdd'));
                }

                //$ownDepartmentPublishedForAdd = $pub_own_as_add_courses;
                //$this->set(compact('ownDepartmentPublishedForAdd'));

            } else {
                $this->Flash->warning(
                    'You are not assiged to any section for ' . (isset($latest_academic_year) && !empty($latest_academic_year) ? $latest_academic_year : $this->AcademicYear->current_academicyear(
                    )) . ' academic year. Communicate your department and make sure you have a proper section assignment in ' . (isset($latest_academic_year) && !empty($latest_academic_year) ? $latest_academic_year : $this->AcademicYear->current_academicyear(
                    )) . ' before trying to register or add courses.'
                );
                $this->redirect(
                    array('controller' => 'students', 'action' => 'student_academic_profile', $this->student_id)
                );
                //$this->redirect('/');
            }

            $this->set(compact('student_section', 'student_section_exam_status'));

            if (!empty($this->request->data)) {
                $selected = array_sum($this->request->data['CourseAdd']['add']);

                if ($selected > 0) {
                    $selected_courses_for_add = $this->request->data['CourseAdd']['add'];

                    unset($this->request->data['CourseAdd']['add']);
                    unset($this->request->data['Student']['department_id']);

                    $add_selected_to_registration = array();

                    if (isset($selected_courses_for_add) && !empty($selected_courses_for_add)) {
                        foreach ($selected_courses_for_add as $k => $v) {
                            if ($v == 1) {
                                $published_detail['published_course_id'] = $k;
                                $add_selected_to_registration['CourseAdd'][] = $published_detail['published_course_id'];
                            }
                        }
                    }

                    $this->request->data['CourseAdd'] = $add_selected_to_registration['CourseAdd'];
                    //debug($this->request->data);
                    //check for duplicate entry

                    $already_added_courses = array();
                    $selected_courses_add = array();
                    $count = 0;
                    $currentLoadToAdd = 0;

                    if (!empty($this->request->data['CourseAdd']) && isset($this->request->data['addSelected'])) {
                        foreach ($this->request->data['CourseAdd'] as $cdd => $cdv) {
                            $courseDetailCredit = $this->CourseAdd->find('first', array(
                                'conditions' => array(
                                    'CourseAdd.published_course_id' => $cdv,
                                ),
                                'contain' => array(
                                    'PublishedCourse' => array(
                                        'Course' => array('id', 'course_title', 'credit')
                                    )
                                )
                            ));

                            $check = $this->CourseAdd->find('count', array(
                                'conditions' => array(
                                    'CourseAdd.published_course_id' => $cdv,
                                    'CourseAdd.student_id' => $this->student_id
                                ),
                                'recursive' => -1
                            ));

                            // already added, unset it

                            if ($check > 0) {
                                $already_added_courses[] = $cdv;
                            } else {
                                debug($courseDetailCredit);

                                $currentLoadToAdd += $courseDetailCredit['PublishedCourse']['Course']['credit'];

                                $is_mass_add = $this->CourseAdd->PublishedCourse->field('add', array('id' => $cdv));

                                $selected_courses_add['CourseAdd'][$count]['published_course_id'] = $cdv;

                                if ($is_mass_add == 1) {
                                    $selected_courses_add['CourseAdd'][$count]['department_approval'] = 1;
                                    $selected_courses_add['CourseAdd'][$count]['registrar_confirmation'] = 1;
                                }

                                $selected_courses_add['CourseAdd'][$count]['student_id'] = $this->student_id;
                                $selected_courses_add['CourseAdd'][$count]['semester'] = $semester;
                                $selected_courses_add['CourseAdd'][$count]['academic_year'] = $current_academic_year;

                                if (empty($student_section['Section'][0]['year_level_id']) || $student_section['Section'][0]['year_level_id'] == 0) {
                                    $selected_courses_add['CourseAdd'][$count]['year_level_id'] = 0;
                                } else {
                                    $selected_courses_add['CourseAdd'][$count]['year_level_id'] = $student_section['Section'][0]['year_level_id'];
                                }
                            }
                            $count++;
                        }
                    }

                    $already_added_courses_for_the_semester = $this->CourseAdd->find('count', array(
                        'conditions' => array(
                            'CourseAdd.academic_year' => $current_academic_year,
                            'CourseAdd.semester' => $semester,
                            'CourseAdd.student_id' => $this->student_id
                        )
                    ));

                    debug($already_added_courses_for_the_semester);

                    if (count($already_added_courses) == count($this->request->data['CourseAdd'])) {
                        $this->Flash->info(
                            'All the selected courses were already added. You do not need to add it again.'
                        );
                        $this->redirect(array('action' => 'index'));
                    } else {
                        if (is_numeric(
                                MAXIMUM_COURSES_TO_ADD_PER_SEMESTER
                            ) && MAXIMUM_COURSES_TO_ADD_PER_SEMESTER > 0 && (($already_added_courses_for_the_semester > MAXIMUM_COURSES_TO_ADD_PER_SEMESTER) || (($already_added_courses_for_the_semester + count(
                                            $this->request->data['CourseAdd']
                                        )) > MAXIMUM_COURSES_TO_ADD_PER_SEMESTER))) {
                            $this->Flash->warning(
                                'You can\'t add more than ' . MAXIMUM_COURSES_TO_ADD_PER_SEMESTER . ' courses per semester, uncheck some from your selection and try again.'
                            );
                            $this->redirect(array('action' => 'student_add_courses'));
                        } else {
                            if (is_numeric(
                                    MAXIMUM_COURSES_TO_ADD_PER_SEMESTER
                                ) && MAXIMUM_COURSES_TO_ADD_PER_SEMESTER > 0 && count(
                                    $this->request->data['CourseAdd']
                                ) > MAXIMUM_COURSES_TO_ADD_PER_SEMESTER) {
                                $this->Flash->warning(
                                    'You can\'t add more than ' . MAXIMUM_COURSES_TO_ADD_PER_SEMESTER . ' courses per semester, uncheck one or more courses from your selection and try again.'
                                );
                                $this->redirect(array('action' => 'student_add_courses'));
                            } else {
                                // unset($this->request->data);
                                $this->request->data['CourseAdd'] = $selected_courses_add;
                            }
                        }
                    }

                    $this->request->data['CourseAdd'] = $this->request->data['CourseAdd']['CourseAdd'];

                    if (!empty($this->request->data['CourseAdd'])) {
                        //debug($this->request->data);
                        //check if the add request is more than the allowed course per semester
                        $maxLoad = ($currentLoadToAdd + $this->CourseAdd->Student->calculateStudentLoad(
                                $this->student_id,
                                $semester,
                                $current_academic_year
                            ));
                        $allowedMaximum = ClassRegistry::init('AcademicCalendar')->maximumCreditPerSemester(
                            $this->student_id
                        );

                        if ($maxLoad < $allowedMaximum) {
                            if ($this->CourseAdd->saveAll(
                                $this->request->data['CourseAdd'],
                                array('validate' => 'first')
                            )) {
                                $this->Flash->success(
                                    'The course add has been successfull and sent to department for approval.'
                                );
                                $this->redirect(array('action' => 'index'));
                            } else {
                                $this->Flash->error('The course add could not be saved. Please, try again.');
                            }
                        } else {
                            $this->Flash->error(
                                'The maximum course load allowed per semester is ' . $allowedMaximum . '. Please reduce the number of courses you would like to take and try again.'
                            );
                        }
                    }
                } else {
                    $this->Flash->error('Please select atleast one course you want to add.');
                }
                //debug($this->request->data);
            }
        }

        // $departments= $this->CourseAdd->PublishedCourse->Department->find('list', array('conditions' => array('Department.active' => 1)));
        $colleges = $this->CourseAdd->PublishedCourse->College->find(
            'list',
            array('conditions' => array('College.active' => 1))
        );
        $departments = array();

        if (isset($student_section_exam_status['College']['stream']) && $student_section_exam_status['College']['stream']) {
            $colleges = $this->CourseAdd->PublishedCourse->College->find(
                'list',
                array(
                    'conditions' => array(
                        'College.active' => 1,
                        'College.stream' => $student_section_exam_status['College']['stream'],
                        'OR' => array(
                            'College.campus_id' => $student_section_exam_status['College']['campus_id'],
                            'College.id' => Configure::read('only_stream_based_colleges_pre_social_natural')
                        )
                    )
                )
            );
        }

        if (!is_null($studentDetails['Student']['college_id']) && is_null(
                $studentDetails['Student']['department_id']
            ) && in_array(
                $studentDetails['Student']['college_id'],
                Configure::read('only_stream_based_colleges_pre_social_natural')
            )) {
            $colleges = $this->CourseAdd->PublishedCourse->College->find(
                'list',
                array(
                    'conditions' => array(
                        'College.id' => Configure::read('only_stream_based_colleges_pre_social_natural'),
                        'College.active' => 1
                    )
                )
            );
        }

        $this->set(compact('colleges', 'departments'));
    }

    public function getPublishedAddCourses($section_id = null, $student_id = null, $academicYearSemester = null)
    {

        $this->layout = 'ajax';

        if (!empty($academicYearSemester)) {
            // $current_academic_year = $this->AcademicYear->current_academicyear();
            $academicYearSemesterArray = explode(",", $academicYearSemester);
            $academicYear = str_replace("-", "/", $academicYearSemesterArray[0]);
            $current_academic_year = $academicYear;
            $section_semester = $academicYearSemesterArray[1];
        } else {
            //check the selected section for academic year
            $secDetail = $this->CourseAdd->PublishedCourse->Section->find(
                'first',
                array('conditions' => array('Section.id' => $section_id), 'recursive' => -1)
            );

            if (isset($secDetail['Section']['academicyear']) && !empty($secDetail['Section']['academicyear'])) {
                $current_academic_year = $secDetail['Section']['academicyear'];
            } else {
                $current_academic_year = $this->AcademicYear->current_academicyear();
            }

            debug($current_academic_year);

            if (!empty($student_id)) {
                $latestAcSemester = ClassRegistry::init('CourseRegistration')->getLastestStudentSemesterAndAcademicYear(
                    $student_id,
                    $current_academic_year
                );
            } else {
                $latestAcSemester = ClassRegistry::init('CourseRegistration')->getLastestStudentSemesterAndAcademicYear(
                    $this->student_id,
                    $current_academic_year
                );
            }
            debug($latestAcSemester);

            $section_semester = ClassRegistry::init('CourseRegistration')->latest_semester_of_section(
                $section_id,
                $current_academic_year
            );
            debug($section_semester);

            if ($section_semester == 2 && !empty($secDetail['Section']['department_id'])) {
                $section_semester = $latestAcSemester['semester'];
            } else {
                if ($section_semester == 2 && empty($secDetail['Section']['department_id'])) {
                    $tmp = $this->CourseAdd->PublishedCourse->find('first', array(
                        'conditions' => array(
                            'PublishedCourse.academic_year' => $current_academic_year,
                            'PublishedCourse.section_id' => $section_id
                        ),
                        'order' => array('PublishedCourse.semester' => 'DESC'),
                        'recursive' => -1
                    ));

                    if (!empty($tmp)) {
                        $section_semester = $tmp['PublishedCourse']['semester'];
                    } else {
                        $section_semester = '';
                    }
                }
            }
        }

        if (!empty($student_id)) {
            $student_section_id = $this->CourseAdd->Student->StudentsSection->field(
                'section_id',
                array('student_id' => $student_id, 'archive' => 0)
            );
        } else {
            $student_section_id = $this->CourseAdd->Student->StudentsSection->field(
                'section_id',
                array('student_id' => $this->student_id, 'archive' => 0)
            );
        }

        if ($student_section_id == $section_id) {
            debug($section_id);
            // exclude mass add
            $otherpublished = $this->CourseAdd->PublishedCourse->find('all', array(
                'conditions' => array(
                    'PublishedCourse.academic_year LIKE ' => $current_academic_year . '%',
                    'PublishedCourse.semester ' => $section_semester,
                    'PublishedCourse.drop = 0',
                    'PublishedCourse.add = 0',
                    'PublishedCourse.section_id' => $section_id
                ),
                'contain' => array(
                    'Course' => array(
                        'fields' => array(
                            'course_code',
                            'credit',
                            'id',
                            'course_title',
                            'active',
                            'course_detail_hours'
                        ),
                        'Curriculum' => array('id', 'name', 'type_credit', 'year_introduced', 'active')
                    )
                )
            ));
        } else {
            $otherpublished = $this->CourseAdd->PublishedCourse->find('all', array(
                'conditions' => array(
                    'PublishedCourse.academic_year LIKE ' => $current_academic_year . '%',
                    'PublishedCourse.semester ' => $section_semester,
                    'PublishedCourse.drop = 0',
                    'PublishedCourse.add = 0',
                    'PublishedCourse.section_id' => $section_id
                ),
                'contain' => array(
                    'Course' => array(
                        'fields' => array(
                            'course_code',
                            'credit',
                            'id',
                            'course_title',
                            'active',
                            'course_detail_hours'
                        ),
                        'Curriculum' => array('id', 'name', 'type_credit', 'year_introduced', 'active')
                    )
                )
            ));
            debug($current_academic_year);
            debug($section_semester);
        }

        //debug($otherpublished);

        if (!empty($student_id)) {
            $otherAdds = $this->__exclude_already_added($otherpublished, $student_id);
        } else {
            $otherAdds = $this->__exclude_already_added($otherpublished, $this->student_id);
        }

        $this->set(compact('otherAdds'));

        if (!empty($student_id)) {
            $this->set('student_id', $student_id);
        } else {
            $this->set('student_id', $this->student_id);
        }
    }

    public function __exclude_already_added($otherAdds, $student_id = null)
    {

        $pub_own_as_add_courses = array();
        $count = 0;

        if (!empty($otherAdds)) {
            foreach ($otherAdds as $ownIndex => $ownValue) {
                if (isset($student_id) && !empty($student_id)) {
                    $already_added = $this->CourseAdd->find('count', array(
                        'conditions' => array(
                            'CourseAdd.student_id' => $student_id,
                            'CourseAdd.registrar_confirmation' => 1,
                            'CourseAdd.department_approval' => 1,
                            'CourseAdd.published_course_id' => $ownValue['PublishedCourse']['id']
                        )
                    ));
                } else {
                    $already_added = $this->CourseAdd->find('count', array(
                        'conditions' => array(
                            'CourseAdd.student_id' => $this->student_id,
                            'CourseAdd.registrar_confirmation' => 1,
                            'CourseAdd.department_approval' => 1,
                            'CourseAdd.published_course_id' => $ownValue['PublishedCourse']['id']
                        )
                    ));
                }

                if (!empty($ownValue['Course']['id'])) {
                    $already_taken_course = ClassRegistry::init('CourseDrop')->course_taken(
                        $student_id,
                        $ownValue['Course']['id']
                    );
                }

                debug($already_taken_course);
                /**
                 *1 -exclude from add
                 *2 -exclude from add
                 *3 -allow add
                 *4 - prerequist failed.
                 */

                if ($already_taken_course == 1 || $already_taken_course == 4 || $already_taken_course == 2) {
                    if ($already_added > 0) {
                        $pub_own_as_add_courses[$count] = $ownValue;
                        $pub_own_as_add_courses[$count]['already_added'] = 0;
                    } else {
                        $pub_own_as_add_courses[$count] = $ownValue;
                        $pub_own_as_add_courses[$count]['already_added'] = 1;
                    }

                    if ($already_taken_course == 4) {
                        $pub_own_as_add_courses[$count]['prerequiste_failed'] = 1;
                    }
                } else {
                    $pub_own_as_add_courses[$count] = $ownValue;
                    $pub_own_as_add_courses[$count]['already_added'] = 0;
                }
                $count++;
            }
        }
        return $pub_own_as_add_courses;
    }

    public function cancelMassAdd()
    {

        // Function to load/save search criteria.
        // $this->Session->delete('search_data');

        if ($this->Session->read('search_data') && !isset($this->request->data['getsection'])) {
            $this->request->data['getsection'] = true;
            $this->request->data['Student'] = $this->Session->read('search_data');
            $this->set('hide_search', true);
        }

        if (!empty($this->request->data) && isset($this->request->data['cancelmassadd'])) {
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
                    //foreach publish course, check if grade is not submitted then allow cancellation.
                    $grade_submitted_pub_count = 0;
                    $add_for_delete['add'] = array();

                    foreach ($selected_published_courses as $key => $pid) {
                        $is_grade_submitted = $this->CourseAdd->ExamGrade->is_grade_submitted($pid);
                        if (!$is_grade_submitted) {
                            $add_for_delete['add'][] = $pid;
                        } else {
                            $grade_submitted_pub_count++;
                        }
                    }

                    if (count($selected_published_courses) != $grade_submitted_pub_count) {
                        if (!empty($add_for_delete['add'])) {
                            if ($this->CourseAdd->deleteAll(
                                array('CourseAdd.published_course_id' => $add_for_delete['add']),
                                false
                            )) {
                                $this->Flash->success('Mass Add is cancelled for selected course(s).');
                            } else {
                                $this->Flash->error(
                                    'Mass Add is not cancelled for selected course(s). please try again'
                                );
                            }
                        }
                    } else {
                        $this->Flash->warning(
                            'You can not cancel the Mass Add, Grade is already submitted for the selected course(s).'
                        );
                    }
                }
            } else {
                $this->Flash->error('Please select at least one course or courses you want to cancel the Mass Add.');
            }
        }

        if (!empty($this->request->data) && isset($this->request->data['getsection'])) {
            $this->Session->delete('search_data');

            $everythingfine = false;
            switch ($this->request->data) {
                case empty($this->request->data['Student']['academic_year']):
                    $this->Flash->error('Please select the academic year you want to cancel the Mass Add.');
                    break;
                case empty($this->request->data['Student']['semester']):
                    $this->Flash->error('Please select the semester you want to cancel the Mass Add.');
                    break;
                case empty($this->request->data['Student']['department_id']):
                    $this->Flash->error('Please select the department you want to cancel the Mass Add');
                    break;
                case empty($this->request->data['Student']['year_level_id']):
                    $this->Flash->error('Please select the year level you want cancel the Mass Add.');
                    break;
                case empty($this->request->data['Student']['program_id']):
                    $this->Flash->error('Please select the program you want to cancel the Mass Add.');
                    break;
                case empty($this->request->data['Student']['program_type_id']):
                    $this->Flash->error('Please select the program type you want to cancel the Mass Add.');
                    break;
                default:
                    $everythingfine = true;
            }

            if ($everythingfine) {
                // yearlevel map for the selected department
                // debug($this->request->data['Student']['year_level_id']);

                $this->__init_search();

                $yearLevelId = $this->CourseAdd->PublishedCourse->YearLevel->field('id', array(
                    'YearLevel.department_id' => $this->request->data['Student']['department_id'],
                    'YearLevel.name' => $this->request->data['Student']['year_level_id']
                ));

                $sections = $this->CourseAdd->PublishedCourse->Section->find('list', array(
                    'conditions' => array(
                        'Section.department_id' => $this->request->data['Student']['department_id'],
                        'Section.year_level_id' => $yearLevelId,
                        'Section.program_id' => $this->request->data['Student']['program_id'],
                        'Section.program_type_id' => $this->request->data['Student']['program_type_id']
                    )
                ));

                $listOfPublishedCourses = $this->CourseAdd->PublishedCourse->find('all', array(
                    'conditions' => array(
                        'PublishedCourse.department_id' => $this->request->data['Student']['department_id'],
                        'PublishedCourse.year_level_id' => $yearLevelId,
                        'PublishedCourse.add' => 1,
                        'PublishedCourse.program_id' => $this->request->data['Student']['program_id'],
                        'PublishedCourse.program_type_id' => $this->request->data['Student']['program_type_id'],
                        'PublishedCourse.semester' => $this->request->data['Student']['semester'],
                        "OR" => array(
                            'PublishedCourse.id IN (select published_course_id from course_adds where academic_year = "' . $this->request->data['Student']['academic_year'] . '" AND semester = "' . $this->request->data['Student']['semester'] . '" GROUP BY published_course_id)'
                        ),
                    ),
                    'fields' => array('id', 'section_id'),
                    'contain' => array(
                        'Section' => array('fields' => array('id', 'name')),
                        'Course' => array(
                            'fields' => array(
                                'id',
                                'course_title',
                                'course_code',
                                'lecture_hours',
                                'tutorial_hours',
                                'laboratory_hours',
                                'credit'
                            ),
                            'Curriculum' => array(
                                'fields' => array(
                                    'id',
                                    'type_credit',
                                    'english_degree_nomenclature'
                                )
                            )
                        )
                    )
                ));

                //debug($listOfPublishedCourses);

                $organized_published_course_by_section = array();
                $publish_courses_list_ids = array();

                $publish_counter = 0;
                $grade_submitted_counter = 0;

                if (!empty($listOfPublishedCourses)) {
                    foreach ($listOfPublishedCourses as $lp => $lv) {
                        if (isset($lv['PublishedCourse']['section_id']) && !empty($lv['PublishedCourse']['section_id'])) {
                            $is_grade_submitted = $this->CourseAdd->ExamGrade->is_grade_submitted(
                                $lv['PublishedCourse']['id']
                            );

                            if ($is_grade_submitted) {
                                //put a flag and disable the check box for selection
                                $organized_published_course_by_section[$lv['PublishedCourse']['section_id']][$publish_counter] = $lv;
                                $organized_published_course_by_section[$lv['PublishedCourse']['section_id']][$publish_counter]['grade_submitted'] = 1;
                                $publish_courses_list_ids[$publish_counter] = $lv['PublishedCourse']['id'];
                                $grade_submitted_counter++;
                            } else {
                                $organized_published_course_by_section[$lv['PublishedCourse']['section_id']][$publish_counter] = $lv;
                                $publish_courses_list_ids[] = $lv['PublishedCourse']['id'];
                                $organized_published_course_by_section[$lv['PublishedCourse']['section_id']][$publish_counter]['grade_submitted'] = 0;
                            }

                            if (trim($lv['Course']['Curriculum']['type_credit']) == 'Credit') {
                                $organized_published_course_by_section[$lv['PublishedCourse']['section_id']][$publish_counter]['type_credit'] = 'Credit';
                            } else {
                                $organized_published_course_by_section[$lv['PublishedCourse']['section_id']][$publish_counter]['type_credit'] = 'ECTS';
                            }
                        }
                        $publish_counter++;
                    }
                }

                $publishedCourseAdd = ClassRegistry::init('CourseAdd')->find('all', array(
                    'conditions' => array(
                        'CourseAdd.published_course_id' => $publish_courses_list_ids,
                        'CourseAdd.published_course_id IN (select published_course_id from course_adds where academic_year = "' . $this->request->data['Student']['academic_year'] . '" AND semester = "' . $this->request->data['Student']['semester'] . '" GROUP BY published_course_id)',
                        'CourseAdd.id NOT IN (select course_add_id from exam_grades where course_add_id is not null)'
                    ),
                    'contain' => array(
                        'ExamGrade',
                        'PublishedCourse' => array(
                            'Course',
                            'Section'
                        )
                    )
                ));

                if (empty($publishedCourseAdd)) {
                    $this->Flash->info(
                        'No result is found. Either Grade is submitted or there is no Mass Added Course(s) found to cancel in the selected criteria.'
                    );
                } else {
                    $published_course_ids = array();
                    $this->set('hide_search', true);
                    $this->set(compact('sections', 'listOfPublishedCourses', 'organized_published_course_by_section'));
                }

                $year_level_id = $this->request->data['Student']['year_level_id'];
                $program_name = $this->CourseAdd->PublishedCourse->Program->field(
                    'Program.name',
                    array('Program.id' => $this->request->data['Student']['program_id'])
                );
                $program_type_name = $this->CourseAdd->PublishedCourse->ProgramType->field(
                    'ProgramType.name',
                    array('ProgramType.id' => $this->request->data['Student']['program_type_id'])
                );
                $academic_year = $this->request->data['Student']['academic_year'];
                $semester = $this->request->data['Student']['semester'];
                $department_name = $this->CourseAdd->PublishedCourse->Department->field(
                    'Department.name',
                    array('Department.id' => $this->request->data['Student']['department_id'])
                );

                $this->set(
                    compact(
                        'sections',
                        'year_level_id',
                        'program_name',
                        'program_type_name',
                        'academic_year',
                        'semester',
                        'department_name',
                        'publish_counter',
                        'grade_submitted_counter'
                    )
                );
            }
        }

        if ($this->role_id == ROLE_REGISTRAR) {
            $yearLevels = $this->CourseAdd->YearLevel->distinct_year_level();
            $programs = $this->CourseAdd->Student->Program->find(
                'list',
                array('conditions' => array('Program.id' => $this->program_id))
            );
            $programTypes = $this->CourseAdd->Student->ProgramType->find(
                'list',
                array('conditions' => array('ProgramType.id' => $this->program_type_id))
            );
            $departments = $this->CourseAdd->Student->Department->find(
                'list',
                array('conditions' => array('Department.id' => $this->department_ids, 'Department.active' => 1))
            );
        } else {
            if ($this->role_id == ROLE_COLLEGE) {
                $yearLevels = $this->CourseAdd->YearLevel->distinct_year_level();
                $programs = $this->CourseAdd->Student->Program->find('list');
                $programTypes = $this->CourseAdd->Student->ProgramType->find('list');
                $departments = $this->CourseAdd->Student->Department->find(
                    'list',
                    array('conditions' => array('Department.college_id' => $this->college_id, 'Department.active' => 1))
                );
            } else {
                if ($this->role_id == ROLE_DEPARTMENT) {
                    $departments = $this->CourseAdd->Department->find(
                        'list',
                        array('conditions' => array('Department.id' => $this->department_id,))
                    );
                    $yearLevels = $this->CourseAdd->YearLevel->find(
                        'list',
                        array('conditions' => array('YearLevel.department_id' => $this->department_id))
                    );
                    $programs = $this->CourseAdd->Student->Program->find('list');
                    $programTypes = $this->CourseAdd->Student->ProgramType->find('list');
                } else {
                    $departments = array();
                    $yearLevels = array();
                    $programs = array();
                    $programTypes = array();
                }
            }
        }

        $this->set(compact('departments', 'yearLevels', 'programs', 'programTypes'));
    }

    public function massAdd()
    {

        // Function to load/save search criteria.
        // $this->Session->delete('search_data');

        if ($this->Session->read('search_data') && !isset($this->request->data['getsection'])) {
            $this->request->data['getsection'] = true;
            $this->request->data['Student'] = $this->Session->read('search_data');
            $this->set('hide_search', true);
        }

        if (!empty($this->request->data) && isset($this->request->data['massadd'])) {
            $one_is_selected = false;
            $selected_published_courses = array();
            $section_allowed_mass_add = array();

            //debug($this->request->data['PublishedCourse']);

            if (!empty($this->request->data['PublishedCourse'])) {
                foreach ($this->request->data['PublishedCourse'] as $section_id => $publishedcourse) {
                    foreach ($publishedcourse as $p_id => $selected) {
                        if ($selected == 1) {
                            $one_is_selected = true;
                            // break 2;
                            $selected_published_courses[$section_id][] = $p_id;
                        }
                    }
                }
            }
            debug($selected_published_courses);
            //check if checked.
            //exit();

            if ($one_is_selected) {
                if (!empty($selected_published_courses)) {
                    $count = 0;
                    $selected_courses_add = array();
                    $totalStudentAlreadyAdded = 0;

                    $student_counts = array();
                    $selected_courses_count = 0;
                    $selected_sections_count = 0;

                    foreach ($selected_published_courses as $section_id => $pid) {
                        //dont forget to turn on archive to 0
                        $list_of_students_in_active_section = $this->CourseAdd->PublishedCourse->Section->StudentsSection->find(
                            'all',
                            array(
                                'conditions' => array(
                                    'StudentsSection.section_id' => $section_id,
                                    'StudentsSection.archive' => 0
                                ),
                                'group' => array(
                                    'StudentsSection.section_id',
                                    'StudentsSection.student_id'
                                ),
                                'recursive' => -1
                            )
                        );

                        $selected_sections_count++;
                        $selected_courses_count = $selected_courses_count + count($pid);

                        if (!empty($list_of_students_in_active_section)) {
                            $criteria = $this->Session->read('search_data');

                            $year_level_id = null;

                            if (!empty($criteria['department_id']) && !empty($criteria['year_level_id'])) {
                                $year_level_id = $this->CourseAdd->PublishedCourse->YearLevel->field(
                                    'id',
                                    array(
                                        'YearLevel.department_id' => $criteria['department_id'],
                                        'YearLevel.name' => $criteria['year_level_id']
                                    )
                                );
                            }

                            //debug($year_level_id);
                            debug(count($list_of_students_in_active_section));

                            if (!empty($list_of_students_in_active_section)) {
                                foreach ($list_of_students_in_active_section as $k => $value) {
                                    $pp = 0;
                                    foreach ($pid as $pk => $pvalue) {
                                        //debug($criteria);
                                        //debug($year_level_id);
                                        //debug($pvalue);

                                        $check = $this->CourseAdd->find('count', array(
                                            'conditions' => array(
                                                'CourseAdd.student_id' => $value['StudentsSection']['student_id'],
                                                'CourseAdd.academic_year' => $criteria['academic_year'],
                                                'CourseAdd.semester' => $criteria['semester'],
                                                'CourseAdd.year_level_id' => (!empty($year_level_id) ? $year_level_id : array(
                                                    null,
                                                    0,
                                                    ''
                                                )),
                                                'CourseAdd.published_course_id' => $pvalue,
                                            ),
                                            'recursive' => -1
                                        ));

                                        $graduated = $this->CourseAdd->Student->field(
                                            'Student.graduated',
                                            array('Student.id' => $value['StudentsSection']['student_id'])
                                        );

                                        //debug($graduated);

                                        $listThePrerequistCourse = $this->CourseAdd->PublishedCourse->find(
                                            'first',
                                            array(
                                                'conditions' => array(
                                                    'PublishedCourse.id' => $pvalue
                                                ),
                                                'contain' => array(
                                                    'Course' => array(
                                                        'Prerequisite' => array(
                                                            'id',
                                                            'prerequisite_course_id',
                                                            'co_requisite'
                                                        ),
                                                        'fields' => array(
                                                            'Course.id',
                                                            'Course.course_code',
                                                            'Course.course_title',
                                                            'Course.lecture_hours',
                                                            'Course.tutorial_hours',
                                                            'Course.credit'
                                                        )
                                                    )
                                                )
                                            )
                                        );

                                        $checkForPrerequiste = $this->CourseAdd->courseHasPrerequistAndFullFilled(
                                            $listThePrerequistCourse,
                                            $value['StudentsSection']['student_id']
                                        );

                                        //course taken as registred courses in given semester and academic year

                                        $checkForRegistration = ClassRegistry::init(
                                            'CourseRegistration'
                                        )->courseRegistered(
                                            $criteria['semester'],
                                            $criteria['academic_year'],
                                            $value['StudentsSection']['student_id']
                                        );

                                        //4 dismissed
                                        $passed_or_failed = $this->CourseAdd->Student->StudentExamStatus->get_student_exam_status(
                                            $value['StudentsSection']['student_id'],
                                            $criteria['academic_year']
                                        );

                                        // first time and fullfilled prequisite, and course not registred

                                        if ($checkForPrerequiste == false) {
                                            debug($pp++);
                                            debug($listThePrerequistCourse);
                                        }

                                        // check current load and compare it to maximum allowed Credit/ECTS for each student per program and program type, if it exceedes skip those, Neway.

                                        //debug($checkForPrerequiste);
                                        //debug($checkForRegistration);
                                        //debug($check);

                                        if (!$graduated && $check == 0 && $checkForPrerequiste == true && $checkForRegistration == 0 && $passed_or_failed != 4) {
                                            //if ($passed_or_failed != 4) {
                                            $selected_courses_add['CourseAdd'][$count]['semester'] = $criteria['semester'];
                                            $selected_courses_add['CourseAdd'][$count]['academic_year'] = $criteria['academic_year'];
                                            $selected_courses_add['CourseAdd'][$count]['year_level_id'] = $year_level_id;
                                            $selected_courses_add['CourseAdd'][$count]['published_course_id'] = $pvalue;
                                            $selected_courses_add['CourseAdd'][$count]['student_id'] = $value['StudentsSection']['student_id'];
                                            $selected_courses_add['CourseAdd'][$count]['department_approval'] = 1;
                                            $selected_courses_add['CourseAdd'][$count]['reason'] = 'Published as add';
                                            $selected_courses_add['CourseAdd'][$count]['department_approved_by'] = $this->Auth->user(
                                                'id'
                                            );
                                            $selected_courses_add['CourseAdd'][$count]['registrar_confirmed_by'] = $this->Auth->user(
                                                'id'
                                            );
                                            $selected_courses_add['CourseAdd'][$count]['registrar_confirmation'] = 1;
                                            $count++;

                                            if (!in_array($value['StudentsSection']['student_id'], $student_counts)) {
                                                $student_counts[] = $value['StudentsSection']['student_id'];
                                            }
                                            //}
                                        } else {
                                            //nothing
                                            $totalStudentAlreadyAdded++;
                                        }
                                    }
                                    //publishe course
                                }
                            }
                        }
                    }

                    //debug($count);
                    //debug($pp);
                    //check and add course as mass add

                    if (isset($selected_courses_add['CourseAdd']) && count($selected_courses_add['CourseAdd']) > 0) {
                        $successfull_adds = count($student_counts);
                        if ($this->CourseAdd->saveAll($selected_courses_add['CourseAdd'], array('validate' => 'first')
                        )) {
                            $this->Flash->success(
                                'Mass Add sucessful for ' . $successfull_adds . ' ' . ($successfull_adds == 1 ? 'student' : 'students which fullfil prequiesete courses, if any, ') . ' for the selected ' . ($selected_courses_count . ' ' . ($selected_courses_count == 1 ? 'course' : 'course')) . ($selected_sections_count != 1 ? ' in the selected ' . $selected_sections_count . ' sections.' : ' in the selected section.')
                            );
                            //$this->Session->delete('search_data');
                            unset($this->request->data['getsection']);
                            //$this->redirect(array('action' => 'mass_add'));
                        } else {
                            $this->Flash->error('The Course Add could not be saved. Please, try again.');
                        }
                    } else {
                        $this->Flash->warning(
                            'The mass add for ' . $totalStudentAlreadyAdded . ' student(s) has already approved earlier and currently the system could not find more students for approval.'
                        );
                    }
                }
            } else {
                $this->Flash->error('Please select courses you want to mass add to students.');
            }
        }

        if (!empty($this->request->data) && isset($this->request->data['getsection'])) {
            $this->Session->delete('search_data');

            $everythingfine = false;

            switch ($this->request->data) {
                case empty($this->request->data['Student']['academic_year']):
                    $this->Flash->error('Please select the academic year you want to approve the mass add.');
                    break;
                case empty($this->request->data['Student']['semester']):
                    $this->Flash->error('Please select the semester you want to approve the mass add.');
                    break;
                case empty($this->request->data['Student']['department_id']):
                    $this->Flash->error('Please select the department you want to approve the mass add.');
                    break;
                case empty($this->request->data['Student']['year_level_id']):
                    $this->Flash->error('Please select the year level you want to approve the mass add.');
                    break;
                case empty($this->request->data['Student']['program_id']):
                    $this->Flash->error('Please select the program you want to approve the mass add.');
                    break;
                case empty($this->request->data['Student']['program_type_id']):
                    $this->Flash->error('Please select the program type you want to approve the mass add.');
                    break;
                default:
                    $everythingfine = true;
            }

            if ($everythingfine) {
                // yearlevel map for the selected department
                $this->__init_search();

                $program_type_id = $this->AcademicYear->equivalent_program_type(
                    $this->request->data['Student']['program_type_id']
                );

                $yearLevelId = null;

                if (!empty($this->request->data['Student']['department_id']) && !empty($this->request->data['Student']['year_level_id'])) {
                    $yearLevelId = $this->CourseAdd->PublishedCourse->YearLevel->field(
                        'id',
                        array(
                            'YearLevel.department_id' => $this->request->data['Student']['department_id'],
                            'YearLevel.name' => $this->request->data['Student']['year_level_id']
                        )
                    );
                }

                $sections = array();

                if (!empty($yearLevelId) && !empty($this->request->data['Student']['department_id'])) {
                    $sections = $this->CourseAdd->PublishedCourse->Section->find('list', array(
                        'conditions' => array(
                            'Section.department_id' => $this->request->data['Student']['department_id'],
                            'Section.year_level_id' => (!empty($yearLevelId) ? $yearLevelId : array(null, 0, '')),
                            'Section.program_id' => $this->request->data['Student']['program_id'],
                            'Section.program_type_id' => $program_type_id,
                            'Section.archive' => 0
                        )
                    ));
                }

                $listOfPublishedCourses = array();

                if (!empty($sections)) {
                    $listOfPublishedCourses = $this->CourseAdd->PublishedCourse->find('all', array(
                        'conditions' => array(
                            'PublishedCourse.department_id' => $this->request->data['Student']['department_id'],
                            'PublishedCourse.year_level_id' => $yearLevelId,
                            'PublishedCourse.add' => 1,
                            'PublishedCourse.section_id' => array_keys($sections),
                            'PublishedCourse.program_id' => $this->request->data['Student']['program_id'],
                            'PublishedCourse.program_type_id' => $this->request->data['Student']['program_type_id'],
                            'PublishedCourse.semester' => $this->request->data['Student']['semester'],
                            'PublishedCourse.academic_year LIKE ' => $this->request->data['Student']['academic_year'] . '%',
                            'PublishedCourse.id NOT IN (select published_course_id from course_adds where academic_year = "' . $this->request->data['Student']['academic_year'] . '" AND semester = "' . $this->request->data['Student']['semester'] . '" GROUP BY published_course_id)'
                        ),
                        'fields' => array('id', 'section_id'),
                        'contain' => array(
                            'Section' => array(
                                'fields' => array('id', 'name', 'academicyear'),
                                'YearLevel' => array('id', 'name'),
                                'Department' => array('id', 'name'),
                                'College' => array('id', 'name'),
                                'Program' => array('id', 'name'),
                                'ProgramType' => array('id', 'name'),
                                'Curriculum' => array(
                                    'fields' => array('id', 'name', 'type_credit', 'year_introduced')
                                )
                            ),
                            'Course' => array(
                                'fields' => array(
                                    'id',
                                    'course_title',
                                    'course_code',
                                    'lecture_hours',
                                    'tutorial_hours',
                                    'laboratory_hours',
                                    'credit',
                                    'curriculum_id',
                                    'thesis',
                                    'exit_exam',
                                    'elective'
                                ),
                                'Curriculum' => array(
                                    'fields' => array('id', 'name', 'type_credit', 'year_introduced')
                                )
                            ),
                            'Program' => array('id', 'name'),
                            'ProgramType' => array('id', 'name'),
                            'YearLevel' => array('id', 'name'),
                            'Department' => array('id', 'name'),
                            'College' => array('id', 'name'),
                        )
                    ));
                }

                if (empty($listOfPublishedCourses)) {
                    $this->Flash->info(
                        'No course is found which is published as mass add that need your mass course add approval with the selected search criteria.'
                    );
                }

                $organized_published_course_by_section = array();
                $publish_courses_list_ids = array();
                $counter = 0;
                $totalAddedCourse = 0;

                if (!empty($listOfPublishedCourses)) {
                    foreach ($listOfPublishedCourses as $lp => $lv) {
                        if (isset($lv['PublishedCourse']['section_id']) && !empty($lv['PublishedCourse']['section_id'])) {
                            $is_grade_submitted = $this->CourseAdd->ExamGrade->is_grade_submitted(
                                $lv['PublishedCourse']['id']
                            );

                            if ($is_grade_submitted) {
                                //put a flag and disabled the check box for selection
                                $organized_published_course_by_section[$lv['PublishedCourse']['section_id']][$counter] = $lv;
                                $organized_published_course_by_section[$lv['PublishedCourse']['section_id']][$counter]['grade_submitted'] = true;
                                $publish_courses_list_ids[$counter] = $lv['PublishedCourse']['id'];
                            } else {
                                $organized_published_course_by_section[$lv['PublishedCourse']['section_id']][$counter] = $lv;
                                $publish_courses_list_ids[] = $lv['PublishedCourse']['id'];
                                $organized_published_course_by_section[$lv['PublishedCourse']['section_id']][$counter]['grade_submitted'] = false;
                            }

                            if (trim($lv['Course']['Curriculum']['type_credit']) == 'Credit') {
                                $organized_published_course_by_section[$lv['PublishedCourse']['section_id']][$counter]['type_credit'] = 'Credit';
                            } else {
                                $organized_published_course_by_section[$lv['PublishedCourse']['section_id']][$counter]['type_credit'] = 'ECTS';
                            }
                        }
                        $counter++;
                    }
                }

                $this->set(compact('sections', 'listOfPublishedCourses', 'organized_published_course_by_section'));

                $year_level_id = $this->request->data['Student']['year_level_id'];
                $program_name = $this->CourseAdd->PublishedCourse->Program->field(
                    'Program.name',
                    array('Program.id' => $this->request->data['Student']['program_id'])
                );
                $program_type_name = $this->CourseAdd->PublishedCourse->ProgramType->field(
                    'ProgramType.name',
                    array('ProgramType.id' => $this->request->data['Student']['program_type_id'])
                );
                $academic_year = $this->request->data['Student']['academic_year'];
                $semester = $this->request->data['Student']['semester'];

                $department_name = $this->CourseAdd->PublishedCourse->Department->field(
                    'Department.name',
                    array('Department.id' => $this->request->data['Student']['department_id'])
                );

                $this->set(
                    compact(
                        'sections',
                        'year_level_id',
                        'program_name',
                        'program_type_name',
                        'academic_year',
                        'semester',
                        'department_name'
                    )
                );
            }
        }

        if ($this->role_id == ROLE_REGISTRAR) {
            $yearLevels = $this->CourseAdd->YearLevel->distinct_year_level();
            $programs = $this->CourseAdd->Student->Program->find(
                'list',
                array('conditions' => array('Program.id' => $this->program_id))
            );
            $programTypes = $this->CourseAdd->Student->ProgramType->find(
                'list',
                array('conditions' => array('ProgramType.id' => $this->program_type_id))
            );
            $departments = $this->CourseAdd->Student->Department->find(
                'list',
                array('conditions' => array('Department.id' => $this->department_ids, 'Department.active' => 1))
            );
        } else {
            if ($this->role_id == ROLE_COLLEGE) {
                $yearLevels = $this->CourseAdd->YearLevel->distinct_year_level();
                $programs = $this->CourseAdd->Student->Program->find('list');
                $programTypes = $this->CourseAdd->Student->ProgramType->find('list');
                $departments = $this->CourseAdd->Student->Department->find(
                    'list',
                    array('conditions' => array('Department.college_id' => $this->college_id, 'Department.active' => 1))
                );
            } else {
                if ($this->role_id == ROLE_DEPARTMENT) {
                    $departments = $this->CourseAdd->Department->find(
                        'list',
                        array('conditions' => array('Department.id' => $this->department_id,))
                    );
                    $yearLevels = $this->CourseAdd->YearLevel->find(
                        'list',
                        array('conditions' => array('YearLevel.department_id' => $this->department_id))
                    );
                    $programs = $this->CourseAdd->Student->Program->find('list');
                    $programTypes = $this->CourseAdd->Student->ProgramType->find('list');
                } else {
                    $departments = array();
                    $yearLevels = array();
                    $programs = array();
                    $programTypes = array();
                }
            }
        }

        $this->set(compact('departments', 'yearLevels', 'programs', 'programTypes'));
    }

    public function cancelCourseAdd()
    {

        if ($this->role_id == ROLE_REGISTRAR || ROLE_REGISTRAR == $this->Session->read(
                'Auth.User'
            )['Role']['parent_id']) {
            $this->__cancel_course_add();
        }
    }

    private function __cancel_course_add($selected = null)
    {

        /*
		1. Retrieve list of sections based on the given search criteria
		2. Display list of sections
		3. Up on the selection of section, display list of students with check-box
		4. Prepare student grade report in PDF for the selected students
		*/

        $programs = $this->CourseAdd->PublishedCourse->Section->Program->find('list');
        $program_types = $this->CourseAdd->PublishedCourse->Section->ProgramType->find('list');
        //$acyear_list = $this->AcademicYear->academicYearInArray(date('Y') - 6, date('Y'));

        $current_acy = $this->AcademicYear->current_academicyear();

        if (is_numeric(YEARS_BACK_FOR_NG_F_FX_W_DO_I_CANCELATION) && YEARS_BACK_FOR_NG_F_FX_W_DO_I_CANCELATION) {
            $acyear_list = $this->AcademicYear->academicYearInArray(
                ((explode('/', $current_acy)[0]) - YEARS_BACK_FOR_NG_F_FX_W_DO_I_CANCELATION),
                (explode('/', $current_acy)[0])
            );
        } else {
            if (is_numeric(ACY_BACK_FOR_ALL) && ACY_BACK_FOR_ALL) {
                $acyear_list = $this->AcademicYear->academicYearInArray(
                    ((explode('/', $current_acy)[0]) - ACY_BACK_FOR_ALL),
                    (explode('/', $current_acy)[0])
                );
            } else {
                $acyear_list[$current_acy] = $current_acy;
            }
        }

        debug($acyear_list);

        $this->set(compact('acyear_list'));

        //deleteGrade button is clicked

        debug($this->request->data);

        if (isset($this->request->data['deleteGrade']) && !empty($this->request->data['deleteGrade'])) {
            $publishedCoursesId = array();
            $student_ids = array();
            $studentId = null;
            $courseAddAndGrade = array();
            $courseAddAndGrade = array();
            $count = 0;
            $scaleNotFound['freq'] = 0;

            $student_ids_to_regenarate_status = array();
            $exam_grade_change_ids_to_delete = array();

            if (isset($this->request->data['CourseAdd']) && !empty($this->request->data['CourseAdd'])) {
                foreach ($this->request->data['CourseAdd'] as $key => $student) {
                    if (isset($student['gp']) && $student['gp'] == 1) {
                        $student_ids[] = $student['student_id'];
                        $studentId = $student['student_id'];
                        $courseAddAndGrade[$count]['CourseAdd'] = $student;
                        $publishedCoursesId = $student['published_course_id'];

                        if (isset($student['grade_id']) && !empty($student['grade_id'])) {
                            debug($student['grade_id']);
                            $courseAddAndGrade[$count]['ExamGrade'][$count]['id'] = $student['grade_id'];
                        }

                        if (!empty($student['id'])) {
                            //$courseAddAndGrade[$count]['ExamGrade'][$count]['course_add_id'] = $student['id'];

                            // Get all exam exam_grade_ids to delete
                            $tmp = $this->CourseAdd->ExamGrade->find('all', array(
                                'conditions' => array('ExamGrade.course_add_id' => $student['id']),
                                'contain' => array('ExamGradeChange')
                            ));

                            debug($tmp);
                            if (!empty($tmp)) {
                                foreach ($tmp as $key => $exGrde) {
                                    //debug($exGrde);
                                    $courseAddAndGrade[$count]['ExamGrade'][$key]['id'] = $exGrde['ExamGrade']['id'];
                                    $courseAddAndGrade[$count]['ExamGrade'][$key]['course_add_id'] = $student['id'];

                                    if (!empty($exGrde['ExamGradeChange'])) {
                                        //debug($exGrde['ExamGradeChange']);
                                        foreach ($exGrde['ExamGradeChange'] as $key2 => $exGrChange) {
                                            //debug($exGrChange['id']);
                                            //debug($exGrChange['exam_grade_id']);
                                            $exam_grade_change_ids_to_delete[] = $exGrChange['id'];
                                        }
                                    }
                                }
                            } else {
                                $courseAddAndGrade[$count]['ExamGrade'][$count]['course_add_id'] = $student['id'];
                            }
                        }

                        if (isset($student['student_id']) && !empty($student['student_id'])) {
                            if (!empty($student_ids_to_regenarate_status) && !in_array(
                                    $student['student_id'],
                                    $student_ids_to_regenarate_status
                                )) {
                                $student_ids_to_regenarate_status[] = $student['student_id'];
                            } else {
                                if (empty($student_ids_to_regenarate_status)) {
                                    $student_ids_to_regenarate_status[] = $student['student_id'];
                                }
                            }
                        }

                        $count++;
                    }
                    //$count++;
                }
            }


            if (!empty($courseAddAndGrade)) {
                $courseAddandRegistrationExamGradeIds = array();

                foreach ($courseAddAndGrade as $data) {
                    foreach ($data['ExamGrade'] as $k => $v) {
                        //$courseAddandRegistrationExamGradeIds['ExamGrade'][] = $v['id'];
                        if (isset($v['id']) && !empty($v['id'])) {
                            $courseAddandRegistrationExamGradeIds['ExamGrade'][] = $v['id'];
                        }

                        if (isset($v['course_add_id']) && !empty($v['course_add_id'])) {
                            $courseAddandRegistrationExamGradeIds['CourseAdd'][] = $v['course_add_id'];
                        }
                    }
                }

                debug($courseAddandRegistrationExamGradeIds);
                //exit();

                if (!empty($courseAddandRegistrationExamGradeIds['CourseAdd'])) {
                    if ($this->CourseAdd->deleteAll(
                        array('CourseAdd.id' => $courseAddandRegistrationExamGradeIds['CourseAdd']),
                        false
                    )) {
                        if (isset($courseAddandRegistrationExamGradeIds['ExamGrade']) && !empty($courseAddandRegistrationExamGradeIds['ExamGrade'])) {
                            $this->CourseAdd->ExamGrade->deleteAll(
                                array('ExamGrade.id' => $courseAddandRegistrationExamGradeIds['ExamGrade']),
                                false
                            );
                        }

                        $cadd_count = count($courseAddandRegistrationExamGradeIds['CourseAdd']);
                        $this->Flash->success(
                            'You have successfully cancelled ' . ($cadd_count == 1 ? '1 course add and associated grades' : $cadd_count . ' course adds and associated grades') . ', if any.'
                        );

                        // Delete Exam Grade changes associated to the given Exam Grade ID
                        if (!empty($exam_grade_change_ids_to_delete)) {
                            $this->CourseAdd->ExamGrade->ExamGradeChange->deleteAll(
                                array('ExamGradeChange.id' => $exam_grade_change_ids_to_delete),
                                false
                            );
                        }

                        // regenerate all students status
                        if (!empty($student_ids_to_regenarate_status)) {
                            foreach ($student_ids_to_regenarate_status as $key => $stdnt_id) {
                                // regenarate all status regardless if it when it is regenerated
                                $status_status = $this->CourseAdd->Student->StudentExamStatus->regenerate_all_status_of_student_by_student_id(
                                    $stdnt_id,
                                    0
                                );

                                if ($status_status == 3) {
                                    // status is regenerated in last 1 week, so check if there is any changes are possible after that
                                }
                            }
                        }

                        unset($this->request->data['CourseAdd']);
                        unset($this->request->data['select-all']);
                    }
                }
            } else {
                if (empty($student_ids)) {
                    $this->request->data['listAddedCourses'] = true;
                    $this->Flash->error('You are required to select at least one course.');
                }
            }
        }

        //Get published course for the selected student
        if (isset($this->request->data['listAddedCourses']) && !empty($this->request->data['listAddedCourses'])) {
            $department_ids = array();
            $college_ids = array();
            $everyThingOk = false;
            $selectedStudent = array();

            if ($this->Session->read('Auth.User')['role_id'] == ROLE_REGISTRAR && !empty($this->department_ids)) {
                $department_ids = $this->department_ids;
            } else {
                if ($this->Session->read('Auth.User')['role_id'] == ROLE_REGISTRAR && !empty($this->college_ids)) {
                    $college_ids = $this->college_ids;
                }
            }


            $password = $this->CourseAdd->Student->User->field(
                'User.password',
                array('User.id' => $this->Auth->user('id'))
            );
            $hashedPasswordGiven = Security::hash($this->request->data['CourseAdd']['password'], null, true);

            if ($hashedPasswordGiven == $password) {
                if (!empty($department_ids)) {
                    $selectedStudent = $this->CourseAdd->Student->find('first', array(
                        'conditions' => array(
                            'Student.studentnumber' => trim($this->request->data['CourseAdd']['studentnumber']),
                        ),
                        'contain' => array('StudentsSection')
                    ));

                    //debug($selectedStudent);

                    if (empty($selectedStudent)) {
                        $this->Flash->error(
                            $this->request->data['CourseAdd']['studentnumber'] . ' is not a valid student number.'
                        );
                    } else {
                        if (!in_array($selectedStudent['Student']['department_id'], $department_ids) || !in_array(
                                $selectedStudent['Student']['program_id'],
                                $this->program_id
                            ) || !in_array($selectedStudent['Student']['program_type_id'], $this->program_type_id)) {
                            $this->Flash->error(
                                'You don\'t have the privilage to cancel course add for ' . $selectedStudent['Student']['full_name'] . ' (' . $selectedStudent['Student']['studentnumber'] . ').'
                            );
                            $selectedStudent = array();
                        } else {
                            if ($selectedStudent['Student']['graduated']) {
                                $this->Flash->error(
                                    $selectedStudent['Student']['full_name'] . ' (' . $selectedStudent['Student']['studentnumber'] . ') is graduated Student. You don\'t have the privilage to cancel the course add.'
                                );
                                $everyThingOk = false;
                                $selectedStudent = array();
                            } else {
                                $everyThingOk = true;
                            }
                        }
                    }
                } else {
                    if (!empty($college_ids)) {
                        $selectedStudent = $this->CourseAdd->Student->find('first', array(
                            'conditions' => array(
                                'Student.studentnumber' => trim($this->request->data['CourseAdd']['studentnumber']),
                            ),
                            'contain' => array('StudentsSection')
                        ));

                        if (empty($selectedStudent)) {
                            $this->Flash->error(
                                $this->request->data['CourseAdd']['studentnumber'] . ' is not a valid student number.'
                            );
                        } else {
                            if (!in_array($selectedStudent['Student']['college_id'], $college_ids) || !in_array(
                                    $selectedStudent['Student']['program_id'],
                                    $this->program_id
                                ) || !in_array(
                                    $selectedStudent['Student']['program_type_id'],
                                    $this->program_type_id
                                )) {
                                $this->Flash->error(
                                    'You don\'t have the privilage to cancel course add for ' . $selectedStudent['Student']['full_name'] . ' (' . $selectedStudent['Student']['studentnumber'] . ').'
                                );
                                $selectedStudent = array();
                            } else {
                                if ($selectedStudent['Student']['graduated']) {
                                    $this->Flash->error(
                                        $selectedStudent['Student']['full_name'] . ' (' . $selectedStudent['Student']['studentnumber'] . ') is graduated Student. You don\'t have the privilage to cancel the course add.'
                                    );
                                    $everyThingOk = false;
                                    $selectedStudent = array();
                                } else {
                                    $everyThingOk = true;
                                }
                            }
                        }
                    }
                }
            } else {
                $everyThingOk = false;
                $this->Flash->error('Wrong password!');
            }

            debug($everyThingOk);
            debug($selectedStudent);

            if ($everyThingOk && !empty($selectedStudent)) {
                /* find the published course in that semester and academic year does that published course has registration, grade submitted, then disable in the interface data entry */

                $yearLevelAndSemesterOfStudent = $this->CourseAdd->Student->StudentExamStatus->studentYearAndSemesterLevel(
                    $selectedStudent['Student']['id'],
                    $this->request->data['CourseAdd']['acadamic_year'],
                    $this->request->data['CourseAdd']['semester']
                );

                $student_academic_profile = $this->CourseAdd->Student->getStudentRegisteredAddDropCurriculumResult(
                    $selectedStudent['Student']['id'],
                    $this->AcademicYear->current_academicyear()
                );

                $this->set(compact('student_academic_profile'));

                $selectedStudentDetails = $this->CourseAdd->ExamGrade->getStudentCopy(
                    $selectedStudent['Student']['id'],
                    $this->request->data['CourseAdd']['acadamic_year'],
                    $this->request->data['CourseAdd']['semester']
                );

                $admission_explode = explode('-', $selectedStudentDetails['Student']['admissionyear']);

                $studentAdmissionYear = $this->AcademicYear->get_academicyear(
                    $admission_explode[1],
                    $admission_explode[0]
                );

                $publishedCourses = $this->CourseAdd->find('all', array(
                    'conditions' => array(
                        'CourseAdd.academic_year' => $this->request->data['CourseAdd']['acadamic_year'],
                        'CourseAdd.semester' => $this->request->data['CourseAdd']['semester'],
                        'CourseAdd.student_id' => $selectedStudentDetails['Student']['id'],
                        //'CourseAdd.registrar_confirmation' => 1
                    ),
                    'contain' => array(
                        'PublishedCourse' => array('Course' => array('Prerequisite')),
                        'ExamGrade' => array(
                            'order' => array('ExamGrade.id' => 'DESC', 'ExamGrade.registrar_approval' => 'DESC'),
                            'limit' => 1,
                            'ExamGradeChange' => array(
                                'order' => array(
                                    'ExamGradeChange.id' => 'DESC',
                                    'ExamGradeChange.registrar_approval' => 'DESC'
                                ),
                                'limit' => 1
                            )
                        ),
                        'ExamResult.course_add = 1'
                    )
                ));

                debug($publishedCourses);

                $studentbasic = $selectedStudentDetails;

                $this->set(compact('publishedCourses', 'studentbasic'));
            }
        }

        if (!empty($this->department_ids)) {
            $departments = $this->CourseAdd->Student->Department->find(
                'list',
                array('conditions' => array('Department.id' => $this->department_ids, 'Department.active' => 1))
            );
        } else {
            if (!empty($this->college_ids)) {
                $colleges = $this->CourseAdd->Student->College->find(
                    'list',
                    array('conditions' => array('College.id' => $this->college_ids, 'College.active' => 1))
                );
            }
        }

        $this->set(
            compact(
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
                'colleges',
                'department_id',
                'college_id'
            )
        );

        $this->render('cancel_course_add');
    }

    public function __getEquivalentProgramTypes($program_type_id = 0)
    {

        $program_types_to_look = array();

        $equivalentProgramType = unserialize(
            $this->CourseAdd->Student->ProgramType->field(
                'ProgramType.equivalent_to_id',
                array('ProgramType.id' => $program_type_id)
            )
        );

        if (!empty($equivalentProgramType)) {
            $selected_program_type_array = array();
            $selected_program_type_array[] = $program_type_id;
            $program_types_to_look = array_merge($selected_program_type_array, $equivalentProgramType);
        } else {
            $program_types_to_look[] = $program_type_id;
        }

        debug($program_types_to_look);

        return $program_types_to_look;
    }

    public function approveAutoRejectedCourseAdd($id = null)
    {

        $this->CourseAdd->id = $id;

        if (!$this->CourseAdd->exists()) {
            throw new NotFoundException(__('Invalid Course Add'));
        } else {
            $courseAdd = $this->CourseAdd->find('first', array(
                'conditions' => array('CourseAdd.id' => $id),
                'contain' => array(
                    'PublishedCourse' => array(
                        'Course' => array(
                            'Curriculum' => array('id', 'name', 'year_introduced', 'type_credit', 'active'),
                        ),

                    ),
                    'Student' => array(
                        'fields' => array(
                            'id',
                            'full_name',
                            'gender',
                            'studentnumber',
                            'college_id',
                            'department_id',
                            'program_id',
                            'program_type_id',
                            'graduated',
                            'academicyear',
                            'admissionyear'
                        ),
                        //'Curriculum' => array('id', 'name', 'year_introduced' ,'type_credit', 'active'),
                    ),
                    'YearLevel' => array('id', 'name'),
                ),
                'recursive' => -1
            ));

            //debug($courseAdd);

            $courseReformatted = array();

            if ($this->role_id == ROLE_DEPARTMENT || $this->role_id == ROLE_COLLEGE) {
                $courseAddReformatted['CourseAdd'] = $courseAdd['CourseAdd'];
                $courseAddReformatted['CourseAdd']['department_approval'] = 1;
                $courseAddReformatted['CourseAdd']['reason'] = 'Department Cancelled Auto Rejection. ' . $courseAdd['CourseAdd']['reason'];
                $courseAddReformatted['CourseAdd']['department_approved_by'] = $this->Auth->user('id');
                $courseAddReformatted['CourseAdd']['registrar_confirmation'] = null;
                $courseAddReformatted['CourseAdd']['registrar_confirmed_by'] = null;
                $courseAddReformatted['CourseAdd']['auto_rejected'] = 0;
                $courseAddReformatted['CourseAdd']['modified'] = date('Y-m-d H:i:s');
            } else {
                if ($this->role_id == ROLE_REGISTRAR) {
                    $courseAddReformatted['CourseAdd'] = $courseAdd['CourseAdd'];
                    $courseAddReformatted['CourseAdd']['department_approval'] = 1;
                    $courseAddReformatted['CourseAdd']['reason'] = 'Registrar Cancelled Auto Rejection. ' . $courseAdd['CourseAdd']['reason'];
                    $courseAddReformatted['CourseAdd']['department_approved_by'] = $this->Auth->user('id');
                    $courseAddReformatted['CourseAdd']['registrar_confirmation'] = 1;
                    $courseAddReformatted['CourseAdd']['registrar_confirmed_by'] = $this->Auth->user('id');
                    $courseAddReformatted['CourseAdd']['auto_rejected'] = 0;
                    $courseAddReformatted['CourseAdd']['modified'] = date('Y-m-d H:i:s');
                }
            }

            //debug($courseReformatted);

            //$this->request->allowMethod('post', 'approve_auto_rejected_course_add');

            if (isset($courseAddReformatted['CourseAdd']['id']) && !empty($courseAddReformatted['CourseAdd']['id'])) {
                if ($this->CourseAdd->saveAll($courseAddReformatted['CourseAdd'], array('validate' => 'first'))) {
                    $this->Flash->success(
                        'You have ' . ($this->Session->read(
                            'Auth.User'
                        )['role_id'] == ROLE_DEPARTMENT ? ' approved' : ' comfirmed') . '  course add by cancelling auto rejected course add of ' . $courseAdd['Student']['full_name'] . ' (' . $courseAdd['Student']['studentnumber'] . ')' . ' for ' . $courseAdd['PublishedCourse']['Course']['course_title'] . ' (' . $courseAdd['PublishedCourse']['Course']['course_code'] . ') course, ' . $courseAdd['PublishedCourse']['Course']['credit'] . ' ' . (count(
                            explode('ECTS', $courseAdd['PublishedCourse']['Course']['Curriculum']['type_credit'])
                        ) >= 2 ? 'ECTS' : 'Credit') . '.'
                    );
                } else {
                    $this->Flash->error(
                        'Couldn\'t cancel the auto rejected course add of ' . $courseAdd['Student']['full_name'] . ' (' . $courseAdd['Student']['studentnumber'] . ')' . ' for ' . $courseAdd['PublishedCourse']['Course']['course_title'] . ' (' . $courseAdd['PublishedCourse']['Course']['course_code'] . ') course,  ' . $courseAdd['PublishedCourse']['Course']['credit'] . ' ' . (count(
                            explode('ECTS', $courseAdd['PublishedCourse']['Course']['Curriculum']['type_credit'])
                        ) >= 2 ? 'ECTS' : 'Credit') . '.'
                    );
                }
            }
        }

        $this->redirect(Router::url($this->referer(), true));
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
