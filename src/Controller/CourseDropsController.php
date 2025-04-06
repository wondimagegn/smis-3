<?php

namespace App\Controller;

use Cake\Event\Event;
use Cake\ORM\TableRegistry;
use Cake\Core\Configure;

class CourseDropsController extends AppController
{

    public $name = 'CourseDrops';
    public $menuOptions = array(
        'parent' => 'registrations',
        'exclude' => array('listStudents', 'delete'),
        'alias' => array(
            'index' => 'List Course Drops',
            'add' => 'Drop course for a Student',
            'forcedDrop' => 'Forced Drop',
            'dropCourses' => 'Drop Courses',
            'massDrop' => 'Confirm Mass Drop Requests'
        )
    );

    public $components = array('AcademicYear');

    public function beforeRender(Event $event)
    {

        parent::beforeRender();

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
        $this->Auth->allow('list_students', 'search', 'delete');
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
            $this->Session->write('search_data_approve', $this->request->data['Student']);
        } else {
            if ($this->Session->check('search_data_approve')) {
                $this->request->data['Student'] = $this->Session->read('search_data_approve');
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

        if ($this->Session->check('search_data_index')) {
            $this->Session->delete('search_data_index');
        }

        if ($this->Session->check('search_data_approve')) {
            $this->Session->delete('search_data_approve');
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
        $sort = 'CourseDrop.created';

        $default_ac_year = $this->AcademicYear->currentAcademicYear();
        $allowed_academic_years_for_add_drop[$default_ac_year] = $default_ac_year;

        if (is_numeric(ACY_BACK_COURSE_ADD_DROP_APPROVAL) && ACY_BACK_COURSE_ADD_DROP_APPROVAL) {
            $allowed_academic_years_for_add_drop = $this->AcademicYear->academicYearInArray(
                ((explode('/', $default_ac_year)[0]) - ACY_BACK_COURSE_ADD_DROP_APPROVAL),
                (explode('/', $default_ac_year)[0])
            );
        }

        $options = array();

        if (!empty($this->passedArgs)) {
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

            if ($this->Session->read('Auth.User')['role_id'] == ROLE_DEPARTMENT) {
                $departments = $this->CourseDrop->Student->Department->find(
                    'list',
                    array('conditions' => array('Department.id' => $this->department_id, 'Department.active' => 1))
                );
                $options['conditions'][] = array('Student.department_id' => $this->department_id);
                $default_department_id = $this->request->data['Search']['department_id'] = $this->department_id;

                if ($this->request->data['Search']['status'] == 'notprocessed') {
                    $options['conditions'][] = array('CourseDrop.department_approval is null');
                } else {
                    if ($this->request->data['Search']['status'] == 'accepted') {
                        $options['conditions'][] = array(
                            'OR' => array(
                                'CourseDrop.department_approval' => 1,
                                'CourseDrop.registrar_confirmation' => 1
                            )
                        );
                    } else {
                        if ($this->request->data['Search']['status'] == 'rejected') {
                            $options['conditions'][] = array(
                                'OR' => array(
                                    'CourseDrop.department_approval' => 0,
                                    'CourseDrop.registrar_confirmation' => 0
                                ),
                            );
                        } else {
                            if ($this->request->data['Search']['status'] == 'forced') {
                                $options['conditions'][] = array('CourseDrop.forced' => 1);
                            }
                        }
                    }
                }
            } else {
                if ($this->Session->read('Auth.User')['role_id'] == ROLE_COLLEGE) {
                    $departments = $this->CourseDrop->Student->Department->find(
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
                        $options['conditions'][] = array('CourseDrop.department_approval is null');
                    } else {
                        if ($this->request->data['Search']['status'] == 'accepted') {
                            $options['conditions'][] = array(
                                'OR' => array(
                                    'CourseDrop.department_approval' => 1,
                                    'CourseDrop.registrar_confirmation' => 1
                                )
                            );
                        } else {
                            if ($this->request->data['Search']['status'] == 'rejected') {
                                $options['conditions'][] = array(
                                    'OR' => array(
                                        'CourseDrop.department_approval' => 0,
                                        'CourseDrop.registrar_confirmation' => 0
                                    ),
                                );
                            } else {
                                if ($this->request->data['Search']['status'] == 'forced') {
                                    $options['conditions'][] = array('CourseDrop.forced' => 1);
                                }
                            }
                        }
                    }
                } else {
                    if ($this->Session->read('Auth.User')['role_id'] == ROLE_REGISTRAR) {
                        if (!empty($this->department_ids)) {
                            $colleges = array();
                            $departments = $this->CourseDrop->Student->Department->find(
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
                                $colleges = $this->CourseDrop->Student->College->find(
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
                                'CourseDrop.department_approval' => 1,
                                'CourseDrop.registrar_confirmation is null'
                            );
                        } else {
                            if ($this->request->data['Search']['status'] == 'accepted') {
                                $options['conditions'][] = array(
                                    'CourseDrop.department_approval' => 1,
                                    'CourseDrop.registrar_confirmation' => 1
                                    /* 'OR' => array(
                                        'CourseDrop.department_approval' => 1,
                                        'CourseDrop.registrar_confirmation' => 1
                                    ) */
                                );
                            } else {
                                if ($this->request->data['Search']['status'] == 'rejected') {
                                    $options['conditions'][] = array(
                                        'CourseDrop.department_approval' => 1,
                                        'CourseDrop.registrar_confirmation' => 0
                                    );
                                } else {
                                    if ($this->request->data['Search']['status'] == 'forced') {
                                        $options['conditions'][] = array('CourseDrop.forced' => 1);
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
                            $departments = $this->CourseDrop->Student->Department->find(
                                'list',
                                array(
                                    'conditions' => array(
                                        'Department.id' => $this->department_ids,
                                        'Department.active' => 1
                                    )
                                )
                            );
                            $colleges = $this->CourseDrop->Student->College->find(
                                'list',
                                array('conditions' => array('College.id' => $this->college_ids, 'College.active' => 1))
                            );

                            if (!empty($this->request->data['Search']['department_id'])) {
                                $options['conditions'][] = array('Student.department_id' => $this->request->data['Search']['department_id']);
                            } else {
                                if (empty($this->request->data['Search']['department_id']) && !empty($this->request->data['Search']['college_id'])) {
                                    $departments = $this->CourseDrop->Student->Department->find(
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
                                $options['conditions'][] = array('CourseDrop.department_approval is null');
                            } else {
                                if ($this->request->data['Search']['status'] == 'accepted') {
                                    $options['conditions'][] = array(
                                        'OR' => array(
                                            'CourseDrop.department_approval' => 1,
                                            'CourseDrop.registrar_confirmation' => 1
                                        )
                                    );
                                } else {
                                    if ($this->request->data['Search']['status'] == 'rejected') {
                                        $options['conditions'][] = array(
                                            'OR' => array(
                                                'CourseDrop.department_approval' => 0,
                                                'CourseDrop.registrar_confirmation' => 0
                                            ),
                                        );
                                    } else {
                                        if ($this->request->data['Search']['status'] == 'forced') {
                                            $options['conditions'][] = array('CourseDrop.forced' => 1);
                                        }
                                    }
                                }
                            }
                        }
                    }
                }
            }

            if (!empty($selected_academic_year)) {
                $options['conditions'][] = array('CourseDrop.academic_year' => $selected_academic_year);
            } else {
                $options['conditions'][] = array('CourseDrop.academic_year' => $allowed_academic_years_for_add_drop);
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
                $departments = $this->CourseDrop->Student->Department->find(
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
                $departments = $this->CourseDrop->Student->Department->find(
                    'list',
                    array('conditions' => array('Department.college_id' => $this->college_id, 'Department.active' => 1))
                );

                $options['conditions'][] = array(
                    'CourseDrop.academic_year' => $allowed_academic_years_for_add_drop,
                    'CourseDrop.department_approval is null',
                    'OR' => array(
                        'Student.college_id' => $this->college_id,
                        'Student.department_id' => array_keys($departments)
                    )
                );

                $default_college_id = $this->college_id;
            } else {
                if ($this->Session->read('Auth.User')['role_id'] == ROLE_DEPARTMENT) {
                    $departments = $this->CourseDrop->Student->Department->find(
                        'list',
                        array('conditions' => array('Department.id' => $this->department_id))
                    );

                    $options['conditions'][] = array(
                        'CourseDrop.academic_year' => $allowed_academic_years_for_add_drop,
                        'CourseDrop.department_approval is null',
                        'Student.department_id' => $this->department_id
                    );

                    $default_department_id = $this->department_id;
                } else {
                    if ($this->Session->read('Auth.User')['role_id'] == ROLE_REGISTRAR) {
                        if (!empty($this->department_ids)) {
                            $colleges = array();
                            $departments = $this->CourseDrop->Student->Department->find(
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
                                $colleges = $this->CourseDrop->Student->College->find(
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
                                    '
						Student.program_type_id' => $this->program_type_ids
                                );
                            }
                        }

                        $options['conditions'][] = array(
                            'CourseDrop.academic_year' => $allowed_academic_years_for_add_drop,
                            'CourseDrop.department_approval' => 1,
                            'CourseDrop.registrar_confirmation is null'
                        );
                    } else {
                        if ($this->Session->read('Auth.User')['role_id'] == ROLE_STUDENT) {
                            $options['conditions'][] = array('Student.id' => $this->student_id);
                        } else {
                            $departments = $this->CourseDrop->Student->Department->find(
                                'list',
                                array(
                                    'conditions' => array(
                                        'Department.id' => $this->department_ids,
                                        'Department.active' => 1
                                    )
                                )
                            );
                            $colleges = $this->CourseDrop->Student->College->find(
                                'list',
                                array('conditions' => array('College.id' => $this->college_ids, 'College.active' => 1))
                            );

                            if (!empty($departments) && !empty($colleges)) {
                                $options['conditions'][] = array(
                                    'CourseDrop.academic_year' => $allowed_academic_years_for_add_drop,
                                    'CourseDrop.department_approval is null',
                                    'OR' => array(
                                        'Student.department_id' => $this->department_ids,
                                        'Student.college_id' => $this->college_ids
                                    )
                                );
                            } else {
                                if (!empty($departments)) {
                                    $options['conditions'][] = array(
                                        'CourseDrop.academic_year' => $allowed_academic_years_for_add_drop,
                                        'CourseDrop.department_approval is null',
                                        'Student.department_id' => $this->department_ids
                                    );
                                } else {
                                    if (!empty($colleges)) {
                                        $options['conditions'][] = array(
                                            'CourseDrop.academic_year' => $allowed_academic_years_for_add_drop,
                                            'CourseDrop.department_approval is null',
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

        $courseDrops = array();

        if (!empty($options['conditions'])) {
            $this->Paginator->settings = array(
                'conditions' => $options['conditions'],
                'contain' => array(
                    'CourseRegistration' => array(
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
                            'YearLevel' => array('fields' => array('id', 'name'))
                        ),
                        'ExamGrade',
                        'ExamResult.course_add = 0',
                        'YearLevel' => array('fields' => array('id', 'name'))
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

            //$courseDrops = $this->paginate($options);

            try {
                $courseDrops = $this->Paginator->paginate($this->modelClass);
                $this->set(compact('courseDrops'));
            } catch (NotFoundException $e) {
                if (!empty($this->request->data['Search'])) {
                    unset($this->request->data['Search']['page']);
                    unset($this->request->data['Search']['sort']);
                    unset($this->request->data['Search']['direction']);
                }
                unset($this->passedArgs);
                return $this->redirect(array('action' => 'index'/* , $this->request->data */));
            } catch (Exception $e) {
                if (!empty($this->request->data['Search'])) {
                    unset($this->request->data['Search']['page']);
                    unset($this->request->data['Search']['sort']);
                    unset($this->request->data['Search']['direction']);
                }
                unset($this->passedArgs);
                return $this->redirect(array('action' => 'index'/* , $this->request->data */));
            }
        } else {
            $this->set(compact('courseDrops'));
        }

        if (empty($courseDrops) && !empty($courseDrops)) {
            $this->Flash->info('There is no Course Drop found with the given criteria.');
            $turn_off_search = false;
        } else {
            //debug($courseDrops[0]);
            //debug($courseDrops);
            $turn_off_search = false;
        }

        $this->set(
            compact(
                'programs',
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

    public function add($student_id = null, $registration_id = null)
    {

        $studentnumber = '';
        $is_forced_drop = 0;

        if ($student_id) {
            if (!empty($this->department_ids)) {
                $elegible_registrar_responsibility = $this->CourseDrop->Student->find('count', array(
                    'conditions' => array(
                        'Student.id' => $student_id,
                        'Student.department_id' => $this->department_ids,
                        'Student.program_type_id' => $this->program_type_id,
                        'Student.program_id' => $this->program_id
                    )
                ));
            } elseif (!empty($this->college_ids)) {
                $elegible_registrar_responsibility = $this->CourseDrop->Student->find('count', array(
                    'conditions' => array(
                        'Student.id' => $student_id,
                        'Student.college_id' => $this->college_ids,
                        'Student.program_type_id' => $this->program_type_id,
                        'Student.program_id' => $this->program_id,
                        'Student.department_id is null'
                    )
                ));
            }

            if ($elegible_registrar_responsibility == 0) {
                $this->Flash->error('You do not have the privilage to drop the selected student courses.');
            } else {
                $studentnumber = $this->CourseDrop->CourseRegistration->Student->field(
                    'studentnumber',
                    array('Student.id' => $student_id)
                );

                $detail = $this->CourseDrop->drop_courses_list(
                    $student_id,
                    $this->AcademicYear->current_academicyear()
                );
                $coursesDrop = $detail['courseDrop'];
                $student_section_exam_status = $detail['student_basic'];
                $already_dropped = $detail['alreadyDropped'];

                if (empty($detail['courseDrop'])) {
                    $this->Flash->error(__('The student has not registred for the latest academic year and semester.'));
                } else {
                    $this->set(compact('coursesDrop', 'studentnumber'));
                    $this->set(compact('student_section_exam_status', 'already_dropped'));
                    $this->set('no_display', true);
                }
            }
        }

        if ($registration_id) {
            if (!empty($this->department_ids)) {
                $registrationDetail = $this->CourseDrop->CourseRegistration->find('first', array(
                    'conditions' => array(
                        'CourseRegistration.id' => $registration_id
                    ),
                    'contain' => array(
                        'Student' => array(
                            'conditions' => array(
                                'Student.department_id' => $this->department_ids
                            )
                        )
                    )
                ));
            } elseif (!empty($this->college_ids)) {
                $registrationDetail = $this->CourseDrop->CourseRegistration->find('first', array(
                    'conditions' => array(
                        'CourseRegistration.id' => $registration_id
                    ),
                    'contain' => array(
                        'Student' => array(
                            'conditions' => array(
                                'Student.college_id' => $this->college_ids,
                                'Student.department_id is null'
                            )
                        )
                    )
                ));
            }

            if (!empty($registrationDetail)) {
                $elegible_registrar_responsibility = 1;
            } else {
                $elegible_registrar_responsibility = 0;
            }

            if ($elegible_registrar_responsibility == 0) {
                $this->Flash->error('You do not have the privilage to drop the selected student courses.');
            } else {
                $studentnumber = $this->CourseDrop->CourseRegistration->Student->field(
                    'studentnumber',
                    array('Student.id' => $registrationDetail['CourseRegistration']['student_id'])
                );

                $detail = $this->CourseDrop->drop_courses_list(
                    $registrationDetail['CourseRegistration']['student_id'],
                    $registrationDetail['CourseRegistration']['academic_year']
                );
                $coursesDrop = $detail['courseDrop'];
                $student_section_exam_status = $detail['student_basic'];
                $already_dropped = $detail['alreadyDropped'];

                if (empty($detail['courseDrop'])) {
                    $this->Flash->error(
                        __('The student has not registred for  the latest academic year and semester.')
                    );
                } else {
                    $this->set(compact('coursesDrop', 'studentnumber'));
                    $this->set(compact('student_section_exam_status', 'already_dropped'));
                    $this->set('no_display', true);
                }
            }
        }


        if (!empty($this->request->data) && isset($this->request->data['drop'])) {
            $selected = array_sum($this->request->data['CourseRegistration']['drop']);
            //$student_id=$this->request->data['CourseRegistration']['student_id'];

            if ($selected > 0) {
                $selected_courses_for_drop = $this->request->data['CourseRegistration']['drop'];
                unset($this->request->data['CourseRegistration']['drop']);
                //unset($this->request->data['CourseRegistration']['student_id']);
                $delete_selected_from_registration = array();

                foreach ($selected_courses_for_drop as $k => $v) {
                    if ($v == 0) {
                        foreach ($this->request->data['CourseDrop'] as $cr => $cv) {
                            if ($cv['course_registration_id'] == $k) {
                                unset($this->request->data['CourseDrop'][$cr]);
                            }
                        }
                    }
                }
                /// if needed to delete from course registration table use deleted_selected_from_registration
                // array

                if (!empty($this->request->data['CourseDrop'])) {
                    foreach ($this->request->data['CourseDrop'] as $ds => &$dv) {
                        $delete_selected_from_registration[] = $dv['course_registration_id'];
                        // unset($dv['id']);
                    }
                }

                // $this->request->data['CourseDrop'] = $this->request->data['CourseRegistration'];
                //debug($this->request->data['CourseRegistration']);

                unset($this->request->data['CourseRegistration']);
                unset($this->request->data['Student']);

                $this->CourseDrop->create();
                $already_dropped_courses = array();
                $selected_courses_drop = array();

                if (!empty($this->request->data['CourseDrop'])) {
                    foreach ($this->request->data['CourseDrop'] as $cdd => $cdv) {
                        // debug($cdv);
                        $check = $this->CourseDrop->find('count', array('conditions' => $cdv, 'recursive' => -1));
                        // already dropped, unset it
                        if ($check) {
                            $already_dropped_courses[] = $cdv['course_id'];
                            // unset ($this->request->data['CourseDrop'][$cdd]);
                        } else {
                            $selected_courses_drop['CourseDrop'][] = $cdv;
                        }
                    }
                }

                debug($already_dropped_courses);

                //check duplicate dropping
                if (count($already_dropped_courses) == count($this->request->data['CourseDrop'])) {
                    $this->Flash->error(
                        __('All the selected courses are already dropped. You do not need to drop it again')
                    );
                    //$this->redirect(array('action'=>'index',$this->request->data['CourseDrop'][0]['student_id']));
                } else {
                    unset($this->request->data);
                    $this->request->data = $already_dropped_courses;
                }

                if (!empty($selected_courses_drop['CourseDrop'])) {
                    foreach ($selected_courses_drop['CourseDrop'] as $di => &$dv) {
                        $dv['registrar_confirmation'] = 1;
                        $dv['department_approval'] = 1;
                        $dv['registrar_confirmed_by'] = $this->Auth->user('id');
                        $dv['department_approved_by'] = $this->Auth->user('id');
                        $dv['reason'] = 'Drop by Registrar';
                        $dv['minute_number'] = 'Drop by Registrar';
                    }

                    debug($selected_courses_drop['CourseDrop']);
                    //exit();

                    $selected_courses_count = count($selected_courses_drop['CourseDrop']);

                    if ($this->CourseDrop->saveAll($selected_courses_drop['CourseDrop'], array('validate' => 'first')
                    )) {
                        $this->Flash->success(
                            __(
                                'The selected ' . ($selected_courses_count == 1 ? '1 course is' : $selected_courses_count . ' courses are') . ' dropped successfully.'
                            )
                        );
                        // do hard deletion from course registration
                        $this->redirect(array('action' => 'index'));
                    } else {
                        $this->Flash->error(__('The course drop could not be dropped. Please, try again.'));
                    }
                } else {
                    $this->Flash->error(__('The selected courses could not be dropped. '));
                    //$this->redirect(array('action'=>'index',$this->request->data['CourseDrop'][0]['student_id']));
                }
            } else {
                $this->Flash->error(__('The course drop could not be saved. Please, select one courses to drop.'));
                $this->redirect(array('action' => 'add', $student_id));
            }
        }

        if (!empty($this->request->data) && isset($this->request->data['continue'])) {
            if (isset($this->request->data['Student']['academicyear'])) {
                $current_academic_year = $this->request->data['Student']['academicyear'];
            } else {
                $current_academic_year = $this->AcademicYear->current_academicyear();
            }

            if (!empty($student_id) || !empty($registration_id)) {
                $this->redirect(array('action' => 'add'));
            }

            if (!empty($student_id)) {
                $is_forced_drop = 1;
            }

            $student_lists = array();

            $student_lists = $this->CourseDrop->student_list_registred_but_not_dropped(
                $this->request->data,
                $current_academic_year
            );

            /* if (isset($this->request->data['Student']['semseter']) && !empty($this->request->data['Student']['semseter'])) {
                $this->request->data['Student']['semester'] = $this->request->data['Student']['semseter'];
            }

            if (isset($this->request->data['Student']['department_id']) && !empty($this->request->data['Student']['department_id'])) {
                $this->request->data['Student']['department_id'] = $this->request->data['Student']['department_id'];
            }

            if (isset($this->request->data['Student']['studentnumber']) && !empty($this->request->data['Student']['studentnumber'])) {
                $this->request->data['Student']['studentnumber'] = $this->request->data['Student']['studentnumber'];
            } */

            if (empty($student_lists)) {
                $this->Flash->info(
                    __(
                        'There are no studens who have registered for ' . $current_academic_year . ' academic year who needs dropping of courses. Either you have already dropped courses for those students, or grade has been submitted. '
                    )
                );
            } else {
                $semester = $student_lists[0]['CourseRegistration']['semester'];
                $this->set(compact('semester'));
                $this->set('student_lists', $student_lists);
                $this->set(compact('current_academic_year'));
            }
        }

        if (empty($this->request->data)) {
            $current_academic_year = $this->AcademicYear->current_academicyear();
            $student_lists = array();
            $this->set('student_lists', $student_lists);
            $this->set(compact('current_academic_year'));
        }

        if (!empty($student_id)) {
            $is_forced_drop = 1;
        }

        $yearLevels = $this->CourseDrop->YearLevel->find('list');

        $departments = $this->CourseDrop->CourseRegistration->PublishedCourse->Department->find(
            'list',
            array('conditions' => array('Department.id' => $this->department_ids, 'Department.active' => 1))
        );

        $this->set(compact('yearLevels', 'departments', 'programs', 'studentnumber', 'is_forced_drop'));
    }

    public function massDrop()
    {

        //get list of students and registered courses

        if (!empty($this->request->data) && isset($this->request->data['continue'])) {
            $everythingfine = false;

            switch ($this->request->data) {
                case empty($this->request->data['Student']['department_id']):
                    $this->Flash->error(__('Please select department you want to drop courses for mass students.'));
                    break;
                case empty($this->request->data['Student']['academic_year']):
                    $this->Flash->error(__('Please select academic year you  want to drop courses for mass students.'));
                    break;
                case empty($this->request->data['Student']['program_id']):
                    $this->Flash->error(__('Please select the program you want to drop courses for mass students.'));
                    break;
                case empty($this->request->data['Student']['year_level_id']):
                    $this->Flash->error(__('Please select the year level you want to drop courses for mass students.'));
                    break;
                case empty($this->request->data['Student']['program_type_id']):
                    $this->Flash->error(
                        __('Please select the program type you want to drop courses for mass students.')
                    );
                    break;
                default:
                    $everythingfine = true;
            }

            // everthing is selected, reterive from the data list of published coures for the selected criteria
            if ($everythingfine) {
                $this->__init_search();

                $equivalent_program_type_id = $this->AcademicYear->equivalent_program_type(
                    $this->request->data['Student']['program_type_id']
                );

                $publishedCourses = $this->CourseDrop->CourseRegistration->find('all', array(
                    'conditions' => array(
                        'CourseRegistration.academic_year LIKE ' => $this->request->data['Student']['academic_year'] . '%',
                        'CourseRegistration.semester' => $this->request->data['Student']['semester'],
                        'CourseRegistration.published_course_id = PublishedCourse.id',
                        //'Student.id NOT IN (select student_id from graduate_lists)'
                    ),
                    'contain' => array(
                        'PublishedCourse' => array(
                            'conditions' => array(
                                'PublishedCourse.academic_year LIKE ' => $this->request->data['Student']['academic_year'] . '%',
                                'PublishedCourse.drop' => 1,
                                'PublishedCourse.department_id' => $this->request->data['Student']['department_id'],
                                'PublishedCourse.semester' => $this->request->data['Student']['semester'],
                                'PublishedCourse.program_id' => $this->request->data['Student']['program_id'],
                                'PublishedCourse.program_type_id' => $equivalent_program_type_id
                            ),
                            'fields' => array(
                                'id',
                                'year_level_id',
                                'semester',
                                'program_type_id',
                                'department_id',
                                'academic_year',
                                'section_id'
                            ),
                            'Course' => array(
                                'fields' => array(
                                    'id',
                                    'course_title',
                                    'course_code',
                                    'credit',
                                    'lecture_hours',
                                    'tutorial_hours',
                                    'course_code_title'
                                )
                            ),
                        ),
                        'Student' => array(
                            'conditions' => array(
                                'Student.graduated' => 0
                            ),
                            'fields' => array('id', 'full_name', 'studentnumber')
                        ),
                        'ExamGrade'
                    )
                ));

                $group_courses = array();

                if (!empty($publishedCourses)) {
                    foreach ($publishedCourses as $pk => $pv) {
                        if (!empty($pv['PublishedCourse']['Course'])) {
                            $group_courses[$pv['PublishedCourse']['Course']['id']] = $pv['PublishedCourse']['Course'];
                        }
                    }
                }

                if (!empty($publishedCourses)) {
                    $list_of_students_registered = array();
                    $list_of_students_registered_organized_by_section = array();

                    foreach ($publishedCourses as $k => $v) {
                        $studentsss = $this->CourseDrop->CourseRegistration->find('all', array(
                            'conditions' => array(
                                'CourseRegistration.year_level_id' => $v['PublishedCourse']['year_level_id'],
                                'CourseRegistration.semester' => $v['PublishedCourse']['semester'],
                                'CourseRegistration.academic_year LIKE ' => $v['PublishedCourse']['academic_year'] . '%',
                                'CourseRegistration.section_id' => $v['PublishedCourse']['section_id'],
                                'CourseRegistration.published_course_id' => $v['PublishedCourse']['id'],
                                'CourseRegistration.id NOT IN (select course_registration_id from course_drops)'
                            ),
                            'contain' => array(
                                'ExamGrade',
                                'PublishedCourse' => array(
                                    'Course' => array(
                                        'fields' => array('id', 'course_code_title', 'credit')
                                    )
                                ),
                                'Student' => array(
                                    'conditions' => array(
                                        'Student.graduated' => 0
                                    ),
                                    'Program',
                                    'ProgramType',
                                    'Department',
                                    'fields' => array('id', 'full_name', 'studentnumber', 'gender', 'graduated')
                                )
                            )
                        ));

                        if (!empty($studentsss)) {
                            // $list_of_students_registered=array_merge($list_of_students_registered,$studentsss);
                            $list_of_students_registered_organized_by_section[$v['PublishedCourse']['section_id']][$v['PublishedCourse']['Course']['course_code_title']] = $studentsss;
                            $list_of_students_registered[$v['PublishedCourse']['Course']['course_code_title']] = $studentsss;
                            $sections_list[] = $v['PublishedCourse']['section_id'];
                        }
                    }

                    //list of students registered for the published courses, unset those courses which
                    // has already grade submitted.

                    if (!empty($list_of_students_registered)) {
                        //$sections = $this->CourseDrop->Student->Section->find('list', array('conditions' => array('Section.id' => $sections_list)));
                        $sectionsFormated = $this->CourseDrop->Student->Section->find('all', array(
                            'conditions' => array('Section.id' => $sections_list),
                            'contain' => array(
                                'Program' => array('id', 'name'),
                                'ProgramType' => array('id', 'name'),
                                'YearLevel' => array('id', 'name'),
                                'Department' => array('id', 'name'),
                                'College' => array('id', 'name'),
                                'Curriculum' => array('id', 'name', 'year_introduced', 'type_credit', 'active')
                            )
                        ));

                        $sections = array();

                        foreach ($sectionsFormated as $seckey => $section) {
                            $sections[$section['Section']['id']]['Section'] = $section['Section'];
                            $sections[$section['Section']['id']]['Program'] = $section['Program'];
                            $sections[$section['Section']['id']]['ProgramType'] = $section['ProgramType'];
                            $sections[$section['Section']['id']]['YearLevel'] = $section['YearLevel'];
                            $sections[$section['Section']['id']]['Department'] = $section['Department'];
                            $sections[$section['Section']['id']]['College'] = $section['College'];
                            $sections[$section['Section']['id']]['Curriculum'] = $section['Curriculum'];
                        }

                        //debug($sections);

                        $list_of_students_registered_for_courses = array();

                        foreach ($list_of_students_registered as $k => &$v) {
                            foreach ($v as $kkk => $vvv) {
                                if (empty($vvv['ExamGrade']) && count($vvv['ExamGrade']) == 0) {
                                    // unset($list_of_students_registered[$k]);
                                    $list_of_students_registered_for_courses[$k][] = $v[$kkk];
                                }
                            }
                        }

                        $this->set('hide_search', true);
                        $this->set('list_of_students_registered_for_courses', $list_of_students_registered_for_courses);
                        $this->set(
                            compact(
                                'list_of_students_registered',
                                'list_of_students_registered_organized_by_section',
                                'sections'
                            )
                        );
                        $this->set(compact('publishedCourses', 'group_courses'));
                    } else {
                        $this->Flash->info(
                            __(
                                'There is no student who have been registred for the published courses that need mass drop in the given criteria. Only courses pusblished as Mass drop by the department appreare here for confirmation.'
                            )
                        );
                    }
                } else {
                    $this->Flash->info(
                        __('No course is found which is published as mass drop with the given search criteria.')
                    );
                    //$this->redirect(array('action'=>'index'));
                }
            }
        }

        // drop the selected courses
        if (!empty($this->request->data) && isset($this->request->data['massdrop'])) {
            //$selected_courses_for_drop=array_sum($this->request->data['CourseDrop']['drop']);

            if (!empty($this->request->data['CourseDrop']['minute_number'])) {
                $minute_number = $this->request->data['CourseDrop']['minute_number'];
                unset($this->request->data['CourseDrop']['minute_number']);
                unset($this->request->data['Student']);
                $forced = 1;
                //prepare for soft deletion
                $delete_selected_from_registration = array();

                $student_ids_to_regenarate_status = array();
                $regenerate_status_for_students = false;

                //prepare for saveAll
                if (count($this->request->data['CourseDrop']) > 0) {
                    $year_level_id = $this->request->data['CourseDrop'][0]['year_level_id'];
                    $academic_year = $this->request->data['CourseDrop'][0]['academic_year'];
                    $semester = $this->request->data['CourseDrop'][0]['semester'];

                    $current_acy_and_semester = $this->AcademicYear->current_acy_and_semester();
                    //debug($current_acy_and_semester);

                    if ($academic_year != $current_acy_and_semester['academic_year'] || $semester != $current_acy_and_semester['semester']) {
                        $regenerate_status_for_students = true;
                    }

                    if (!empty($this->request->data['CourseDrop'])) {
                        foreach ($this->request->data['CourseDrop'] as $cd => &$cv) {
                            $cv['academic_year'] = $academic_year;
                            $cv['semester'] = $semester;
                            $cv['year_level_id'] = $year_level_id;
                            $cv['minute_number'] = $minute_number;
                            $cv['forced'] = $forced;
                            $cv['registrar_confirmation'] = 1;
                            $cv['department_approval'] = 1;
                            //$cv['course_id']=$k;

                            $cv['reason'] = 'Published as Mass Drop';
                            $cv['department_approved_by'] = $this->Auth->user('id');
                            $cv['registrar_confirmed_by'] = $this->Auth->user('id');
                        }
                    }

                    //check for duplicate entry
                    $already_dropped_courses = array();
                    $selected_courses_drop = array();

                    if (!empty($this->request->data['CourseDrop'])) {
                        foreach ($this->request->data['CourseDrop'] as $cdd => $cdv) {
                            $major_field = $cdv;
                            unset($major_field['minute_number']);
                            unset($major_field['forced']);
                            $check = $this->CourseDrop->find(
                                'count',
                                array('conditions' => $major_field, 'recursive' => -1)
                            );
                            // debug($cdv);
                            // already dropped, unset it
                            if ($check > 0) {
                                $already_dropped_courses[] = $cdv['course_registration_id'];
                            } else {
                                $selected_courses_drop['CourseDrop'][] = $cdv;

                                if ($regenerate_status_for_students && isset($cdv['student_id'])) {
                                    if (!empty($student_ids_to_regenarate_status) && !in_array(
                                            $cdv['student_id'],
                                            $student_ids_to_regenarate_status
                                        )) {
                                        $student_ids_to_regenarate_status[] = $cdv['student_id'];
                                    } else {
                                        if (empty($student_ids_to_regenarate_status)) {
                                            $student_ids_to_regenarate_status[] = $cdv['student_id'];
                                        }
                                    }
                                }
                            }
                        }
                    }

                    if (count($already_dropped_courses) == count($this->request->data['CourseDrop'])) {
                        $this->Flash->error(
                            __(
                                'The selected courses for the selected sections are already dropped for all students registred. You do not need to drop it again.'
                            )
                        );
                        $this->redirect(array('action' => 'index'));
                    } else {
                        unset($this->request->data);
                        $this->request->data = $already_dropped_courses;
                    }

                    //saveAll
                    $this->set($this->request->data);

                    if ($this->CourseDrop->saveAll($selected_courses_drop['CourseDrop'], array('validate' => 'first')
                    )) {
                        $drops_count = count($selected_courses_drop['CourseDrop']);
                        $this->Flash->success(
                            'The course drop has been saved for ' . $drops_count . ' ' . ($drops_count > 1 ? ' students' : 'student') . (isset($regenerate_status_for_students) && $regenerate_status_for_students ? ' and status is also regenerated.' : '.')
                        );

                        if ($this->Session->delete('search_data_approve')) {
                            $this->Session->delete('search_data_approve');
                        }

                        if (!empty($student_ids_to_regenarate_status) && $regenerate_status_for_students) {
                            foreach ($student_ids_to_regenarate_status as $key => $stdnt_id) {
                                // regenarate all status regardless if it when it is regenerated
                                $status_status = $this->CourseDrop->Student->StudentExamStatus->regenerate_all_status_of_student_by_student_id(
                                    $stdnt_id,
                                    0
                                );
                            }
                        }
                        // do hard deletion from course registration
                        //$this->redirect(array('controller' => 'CourseDrops', 'action' => 'index'));
                    } else {
                        $this->Flash->error(__('The course drop could not be saved.'));
                        // $this->request->data['CourseDrop']['drop'] = $selected_courses_for_drop;
                    }
                }
            } else {
                $this->Flash->error(__('The mass drop could not be saved. You have to provide minute number.'));
            }
        }

        if ($this->role_id == ROLE_REGISTRAR) {
            $yearLevels = $this->CourseDrop->YearLevel->distinct_year_level();
            $this->set(compact('yearLevels'));
        } else {
            if ($this->role_id == ROLE_DEPARTMENT) {
                $yearLevels = $this->CourseDrop->YearLevel->find(
                    'list',
                    array('conditions' => array('YearLevel.department_id' => $this->department_id))
                );
                $this->set(compact('yearLevels'));
            } else {
                $yearLevels = $this->CourseDrop->YearLevel->find('list');
                $this->set(compact('yearLevel'));
            }
        }

        $departments = $this->CourseDrop->CourseRegistration->PublishedCourse->Department->find(
            'list',
            array('conditions' => array('Department.id' => $this->department_ids, 'Department.active' => 1))
        );
        $programTypes = $this->CourseDrop->CourseRegistration->PublishedCourse->ProgramType->find(
            'list',
            array('conditions' => array('ProgramType.id' => $this->program_type_ids, 'ProgramType.active' => 1))
        );
        $programs = $this->CourseDrop->CourseRegistration->PublishedCourse->Program->find(
            'list',
            array('conditions' => array('Program.id' => $this->program_ids, 'Program.active' => 1))
        );
        $this->set(compact('departments', 'programTypes', 'programs'));
    }

    public function approveDrops()
    {

        $flag = false;

        if (!empty($this->request->data) && isset($this->request->data['approverejectdrop'])) {
            $this->set($this->request->data);

            $accepted_course_drop_counts = 0;
            $rejected_course_drop_counts = 0;
            $available_course_drop_count = count($this->request->data['CourseDrop']);

            if (!empty($this->request->data['CourseDrop'])) {
                foreach ($this->request->data['CourseDrop'] as $k => &$v) {
                    if ($this->role_id == ROLE_DEPARTMENT || $this->role_id == ROLE_COLLEGE) {
                        if ($v['department_approval'] == '') {
                            unset($this->request->data['CourseDrop'][$k]);
                        } else {
                            $v['department_approved_by'] = $this->Auth->user('id');

                            if ($v['department_approval'] == 1) {
                                $accepted_course_drop_counts++;
                            } else {
                                if ($v['department_approval'] == 0) {
                                    $rejected_course_drop_counts++;
                                }
                            }
                        }
                    } elseif ($this->role_id == ROLE_REGISTRAR) {
                        if ($v['registrar_confirmation'] == '') {
                            unset($this->request->data['CourseDrop'][$k]);
                        } else {
                            $v['registrar_confirmed_by'] = $this->Auth->user('id');

                            if ($v['registrar_confirmation'] == 1) {
                                $accepted_course_drop_counts++;
                            } else {
                                if ($v['registrar_confirmation'] == 0) {
                                    $rejected_course_drop_counts++;
                                }
                            }
                        }
                    }
                }
            }

            if (!empty($this->request->data['CourseDrop'])) {
                if ($this->CourseDrop->saveAll($this->request->data['CourseDrop'], array('validate' => 'first'))) {
                    $flashMessage = 'Out of ' . $available_course_drop_count . ' available couse drop request' . ($available_course_drop_count > 1 ? 's, ' : ', ');

                    if ($accepted_course_drop_counts > 0) {
                        $flashMessage .= $accepted_course_drop_counts . ' ' . ($accepted_course_drop_counts > 1 ? 'course drops have been ' : 'course drop has been ') . ($this->role_id == ROLE_DEPARTMENT ? 'approved' : 'confirmed');
                    }

                    if ($this->role_id == ROLE_DEPARTMENT && $rejected_course_drop_counts == 0) {
                        $flashMessage .= ' and notification is sent to the registrar for confirmation';
                    }

                    if ($rejected_course_drop_counts > 0) {
                        if ($accepted_course_drop_counts > 0) {
                            $flashMessage .= ' and ';
                        }
                        $flashMessage .= $rejected_course_drop_counts . ' ' . ($rejected_course_drop_counts > 1 ? 'course drops have been ' : 'course drop has been ') . 'rejected';
                    }

                    if ($this->role_id == ROLE_DEPARTMENT && $rejected_course_drop_counts > 0 && $accepted_course_drop_counts > 0) {
                        $flashMessage .= ' and notification is sent to the registrar to confirm the accepted';
                    }

                    $this->Flash->success(__($flashMessage . '.'));

                    $flag = true;
                    unset($this->request->data['CourseDrop']);
                    //$this->redirect(array('action'=>'approve_drops'));
                } else {
                    $this->Flash->error(__('The course drop approve could not be saved. Please, try again.'));
                }
            } else {
                if ($this->role_id == ROLE_REGISTRAR) {
                    $this->Flash->error(
                        __(
                            'The course drop could not be saved. You have not confirmed/denay any of the listed requests.'
                        )
                    );
                } else {
                    if ($this->role_id == ROLE_DEPARTMENT) {
                        $this->Flash->error(
                            __('The course drop could not be saved. You have not approved any of the listed requests.')
                        );
                    }
                }
            }
        }

        //read from session
        // Function to load/save search criteria.

        if ($this->Session->read('search_data_approve')) {
            $this->request->data['getdroprequests'] = true;
            $this->request->data['Student'] = $this->Session->read('search_data_approve');
            $this->set('hide_search', true);
        }

        if (!empty($this->request->data) && isset($this->request->data['getdroprequests'])) {
            //$this->Session->delete('search_data_registration');
            $everythingfine = false;
            switch ($this->request->data) {
                case empty($this->request->data['Student']['semester']):
                    $this->Flash->error(__('Please select the semester you want to approve course drop.'));
                    break;
                case empty($this->request->data['Student']['year_level_id']):
                    $this->Flash->error(__('Please select the year level you want to approve course drop.'));
                    break;
                case empty($this->request->data['Student']['program_id']):
                    $this->Flash->error(__('Please select the program you want to approve course drop.'));
                    break;
                case empty($this->request->data['Student']['program_type_id']):
                    $this->Flash->error(__('Please select the program type you want to approve course drop.'));
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
                    $department_id = $this->department_id;
                }

                $program_type_id = $this->AcademicYear->equivalent_program_type(
                    $this->request->data['Student']['program_type_id']
                );

                $sections = $this->CourseDrop->Student->Section->find('list', array(
                    'conditions' => array(
                        'Section.department_id' => $department_id,
                        'Section.year_level_id' => $this->request->data['Student']['year_level_id'],
                        'Section.program_id' => $this->request->data['Student']['program_id'],
                        'Section.program_type_id' => $program_type_id,
                        'Section.archive' => 0,
                    )
                ));

                // query according their roles

                $this->CourseDrop->Student->bindModel(array('hasMany' => array('StudentsSection')));

                if ($this->role_id == ROLE_REGISTRAR) {
                    $courseDrops = $this->CourseDrop->find('all', array(
                        'conditions' => array(
                            'Student.department_id' => $department_id,
                            'CourseDrop.year_level_id' => $this->request->data['Student']['year_level_id'],
                            'Student.program_id' => $this->request->data['Student']['program_id'],
                            'Student.program_type_id' => $program_type_id,
                            'CourseDrop.semester' => $this->request->data['Student']['semester'],
                            'CourseDrop.academic_year' => $this->request->data['Student']['academic_year'],
                            'CourseDrop.department_approval=1',
                            'CourseDrop.registrar_confirmation is null',
                            'Student.id NOT IN (select student_id from graduate_lists)'
                        ),
                        'contain' => array(
                            'CourseRegistration',
                            'Student' => array(
                                'StudentsSection' => array(
                                    'conditions' => array('StudentsSection.archive = 0')
                                ),
                                'CourseRegistration' => array(
                                    'conditions' => array(
                                        'CourseRegistration.year_level_id' => $this->request->data['Student']['year_level_id'],
                                        'CourseRegistration.semester' => $this->request->data['Student']['semester'],
                                        'CourseRegistration.academic_year' => $this->request->data['Student']['academic_year']
                                    ),
                                    'PublishedCourse' => array(
                                        'Course' => array(
                                            'fields' => array(
                                                'course_code',
                                                'course_detail_hours',
                                                'credit',
                                                'course_title',
                                                'course_code'
                                            )
                                        ),
                                        'fields' => array('PublishedCourse.id')
                                    ),
                                    'fields' => array('id')
                                ),
                                'fields' => array('id', 'full_name')
                            )
                        )
                    ));
                } else {
                    $courseDrops = $this->CourseDrop->find('all', array(
                        'conditions' => array(
                            'Student.department_id' => $department_id,
                            'CourseDrop.year_level_id' => $this->request->data['Student']['year_level_id'],
                            'Student.program_id' => $this->request->data['Student']['program_id'],
                            'Student.program_type_id' => $program_type_id,
                            'CourseDrop.semester' => $this->request->data['Student']['semester'],
                            'CourseDrop.academic_year' => $this->request->data['Student']['academic_year'],
                            'CourseDrop.department_approval is null',
                            'CourseDrop.registrar_confirmation is null',
                            'Student.id NOT IN (select student_id from graduate_lists)'
                        ),
                        'contain' => array(
                            'CourseRegistration' => array(
                                'PublishedCourse' => array(
                                    'Course',
                                    '
									fields' => array('PublishedCourse.id')
                                ),
                                'fields' => array('id')
                            ),
                            'Student' => array(
                                'StudentsSection' => array(
                                    'conditions' => array('StudentsSection.archive = 0')
                                ),
                                'CourseRegistration' => array(
                                    'conditions' => array(
                                        'CourseRegistration.year_level_id' => $this->request->data['Student']['year_level_id'],
                                        'CourseRegistration.semester' => $this->request->data['Student']['semester'],
                                        'CourseRegistration.academic_year' => $this->request->data['Student']['academic_year']
                                    ),
                                    'PublishedCourse' => array(
                                        'Course' => array(
                                            'fields' => array(
                                                'course_code',
                                                'course_detail_hours',
                                                'credit',
                                                'course_title',
                                                'course_code'
                                            )
                                        ),
                                        'fields' => array('PublishedCourse.id')
                                    ),
                                    'fields' => array('id')
                                ),
                                'fields' => array('id', 'full_name')
                            )
                        )
                    ));
                    //debug($courseDrops);

                }

                if (empty($courseDrops)) {
                    if ($this->role_id == ROLE_DEPARTMENT) {
                        $this->Flash->info(__('No students drop request is found in the given criteria.'));
                    } else {
                        if ($this->role_id == ROLE_REGISTRAR) {
                            $this->Flash->info(
                                __(
                                    'No drop request is approved by deparment who needs registrar confirmation  in the given  criteria.'
                                )
                            );
                        } else {
                            $this->Flash->info(
                                __(
                                    'No drop request is approved by deparment who needs  confirmation  in the given  criteria.'
                                )
                            );
                        }
                    }
                } else {
                    $this->__init_search();

                    foreach ($courseDrops as $pk => &$pv) {
                        if (array_key_exists($pv['Student']['StudentsSection'][0]['section_id'], $sections)) {
                            $pv['Student']['max_load'] = $this->CourseDrop->Student->calculateStudentLoad(
                                $pv['Student']['id'],
                                $this->request->data['Student']['semester'],
                                $this->request->data['Student']['academic_year']
                            );
                            $section_organized_published_course[$pv['Student']['StudentsSection'][0]['section_id']][] = $pv;
                        }
                    }

                    $this->set('hide_search', true);
                    $this->set('coursesss', $section_organized_published_course);
                    $this->set(compact('sections'));
                }

                $year_level_id = $this->request->data['Student']['year_level_id'];
                $program_name = $this->CourseDrop->CourseRegistration->PublishedCourse->Program->field(
                    'Program.name',
                    array('Program.id' => $this->request->data['Student']['program_id'])
                );
                $program_type_name = $this->CourseDrop->CourseRegistration->PublishedCourse->ProgramType->field(
                    'ProgramType.name',
                    array('ProgramType.id' => $this->request->data['Student']['program_type_id'])
                );
                $academic_year = $this->request->data['Student']['academic_year'];
                $semester = $this->request->data['Student']['semester'];
                $department_name = $this->CourseDrop->CourseRegistration->PublishedCourse->Department->field(
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

        $current_ac_year = $this->AcademicYear->current_academicyear();

        if ($this->role_id == ROLE_REGISTRAR) {
            $department_ids = array();
            $college_ids = array();

            if (!empty($this->department_ids)) {
                $departments = $this->CourseDrop->CourseRegistration->PublishedCourse->Department->find(
                    'list',
                    array('conditions' => array('Department.id' => $this->department_ids, 'Department.active' => 1))
                );
            } else {
                if (!empty($this->college_ids)) {
                    $departments = $this->CourseDrop->CourseRegistration->PublishedCourse->Department->find(
                        'list',
                        array(
                            'conditions' => array(
                                'Department.college_id' => $this->college_ids,
                                'Department.active' => 1
                            )
                        )
                    );
                }
            }

            if (!empty($this->department_ids)) {
                if (!isset($section_organized_published_course)) {
                    $section_organized_published_course = $this->CourseDrop->list_course_drop_request(
                        $this->role_id,
                        $this->department_ids,
                        $current_ac_year
                    );

                    $sections = $this->CourseDrop->Student->Section->find('list', array(
                        'conditions' => array(
                            'Section.department_id' => $this->department_id,
                            'Section.academicyear' => $current_ac_year,
                            'Section.archive' => 0,
                        )
                    ));

                    $coursesss = $section_organized_published_course;

                    if (empty($coursesss) && !$flag) {
                        $this->Flash->info(
                            __('No students drop request has been approved by department and waits your confirmation.')
                        );
                    }

                    $this->set(compact('sections', 'coursesss'));
                }
            }

            if (!empty($this->college_ids) && $this->onlyPre) {
                if (!isset($section_organized_published_course)) {
                    $section_organized_published_course = $this->CourseDrop->list_course_drop_request(
                        $this->role_id,
                        null,
                        $current_ac_year,
                        $this->college_ids
                    );

                    $sections = $this->CourseDrop->Student->Section->find('list', array(
                        'conditions' => array(
                            'Section.department_id is null',
                            'Section.college_id' => $this->college_ids,
                            'Section.academicyear' => $current_ac_year,
                            'Section.archive' => 0,
                        )
                    ));

                    $coursesss = $section_organized_published_course;

                    if (empty($coursesss) && !$flag) {
                        $this->Flash->info(
                            __('No students drop request has been approved by department and waits your confirmation.')
                        );
                    }

                    $this->set(compact('sections', 'coursesss'));
                }
            }
        } else {
            if ($this->role_id == ROLE_DEPARTMENT) {
                if (!isset($section_organized_published_course)) {
                    $section_organized_published_course = $this->CourseDrop->list_course_drop_request(
                        $this->role_id,
                        $this->department_id,
                        $current_ac_year
                    );

                    $sections = $this->CourseDrop->Student->Section->find('list', array(
                        'conditions' => array(
                            'Section.department_id' => $this->department_id,
                            'Section.academicyear' => $current_ac_year,
                            'Section.archive' => 0,
                        )
                    ));

                    $coursesss = $section_organized_published_course;

                    if (empty($coursesss) && !$flag) {
                        $this->Flash->info(__('No students drop request that needs approval.'));
                    }

                    $this->set(compact('sections', 'coursesss'));
                }
            } else {
                if ($this->role_id == ROLE_COLLEGE) {
                    $departments = $this->CourseDrop->CourseRegistration->PublishedCourse->Department->find(
                        'list',
                        array(
                            'conditions' => array(
                                'Department.college_id' => $this->college_id,
                                'Department.active' => 1
                            )
                        )
                    );

                    if (!isset($section_organized_published_course)) {
                        $section_organized_published_course = $this->CourseDrop->list_course_drop_request(
                            $this->role_id,
                            null,
                            $current_ac_year,
                            $this->college_id
                        );

                        $sections = $this->CourseDrop->Student->Section->find('list', array(
                            'conditions' => array(
                                'Section.department_id is null',
                                'Section.college_id' => $this->college_id,
                                'Section.academicyear' => $current_ac_year,
                                'Section.archive' => 0,
                            )
                        ));

                        $coursesss = $section_organized_published_course;

                        if (empty($coursesss) && !$flag) {
                            $this->Flash->info(__('No students drop request that needs approval.'));
                        }
                        $this->set(compact('sections', 'coursesss'));
                    }
                }
            }
        }

        if ($this->role_id == ROLE_REGISTRAR) {
            $yearLevels = $this->CourseDrop->YearLevel->distinct_year_level();
            $this->set(compact('yearLevels'));
        } else {
            if ($this->role_id == ROLE_DEPARTMENT) {
                $yearLevels = $this->CourseDrop->YearLevel->find(
                    'list',
                    array('conditions' => array('YearLevel.department_id' => $this->department_id))
                );
                $this->set(compact('yearLevels'));
            } else {
                //$yearLevels = $this->CourseDrop->YearLevel->find('list');
                $yearLevels = $this->CourseDrop->YearLevel->distinct_year_level();
                $this->set(compact('yearLevel'));
            }
        }

        $programTypes = $this->CourseDrop->CourseRegistration->PublishedCourse->ProgramType->find('list');
        $programs = $this->CourseDrop->CourseRegistration->PublishedCourse->Program->find('list');

        if (!empty($this->program_ids)) {
            $programs = $this->CourseDrop->CourseRegistration->PublishedCourse->Program->find(
                'list',
                array('conditions' => array('Program.id' => $this->program_ids, 'Program.active' => 1))
            );
        }

        if (!empty($this->program_type_ids)) {
            $programTypes = $this->CourseDrop->CourseRegistration->PublishedCourse->ProgramType->find(
                'list',
                array('conditions' => array('ProgramType.id' => $this->program_type_ids, 'ProgramType.active' => 1))
            );
        }

        $this->set(compact('departments', 'programTypes', 'programs'));
    }

    public function listStudents($course_id = null)
    {

        $this->layout = 'ajax';
        //debug($this->request->data);

        $studentsss = ClassRegistry::init('CourseRegistration')->find('all', array(
            'conditions' => array(
                'CourseRegistration.year_level_id' => $this->request->data['CourseDrop'][0]['year_level_id'],
                'CourseRegistration.semester' => $this->request->data['CourseDrop'][0]['semester'],
                'CourseRegistration.academic_year LIKE' => $this->request->data['CourseDrop'][0]['academic_year'],
                'CourseRegistration.course_id' => $course_id,
                'CourseRegistration.student_id NOT IN (select student_id from graduate_lists)'
            ),
            'contain' => array(
                'ExamResult',
                'Course' => array('fields' => array('id', 'course_code_title', 'credit', '')),
                'Student' => array(
                    'Program',
                    'ProgramType',
                    'Department',
                    'fields' => array('id', 'full_name', 'studentnumber')
                )
            )
        ));

        $hide_search = false;
        $list_of_students_registered_for_courses = array();

        if (!empty($studentsss)) {
            foreach ($studentsss as $k => &$v) {
                //foreach ($v as $kkk=>$vvv) {
                if (empty($v['ExamResult']) && count($v['ExamResult']) == 0) {
                    // unset($list_of_students_registered[$k]);
                    $list_of_students_registered_for_courses[] = $v;
                }
                //}
            }
            $hide_search = true;
        }

        $this->set('hide_search', $hide_search);
        $this->set('studentsss', $list_of_students_registered_for_courses);
    }

    public function dropCourses()
    {

        $current_academic_year = $this->AcademicYear->current_academicyear();
        $studentDetails = $this->CourseDrop->Student->find(
            'first',
            array('conditions' => array('Student.id' => $this->student_id), 'recursive' => -1)
        );

        $student_section_exam_status = $this->CourseDrop->Student->get_student_section(
            $this->student_id,
            $current_academic_year
        );

        $getRegistrationDeadLine = false;

        $latestAcSemester = $this->CourseDrop->CourseRegistration->getLastestStudentSemesterAndAcademicYear(
            $this->student_id,
            $current_academic_year,
            1
        );

        $semester = $latestAcSemester['semester'];

        if (!empty($this->department_id)) {
            $year_level_id = $this->CourseDrop->CourseRegistration->PublishedCourse->YearLevel->field(
                'name',
                array('id' => $student_section_exam_status['Section']['year_level_id'])
            );

            $getRegistrationDeadLine = $this->CourseDrop->CourseRegistration->AcademicCalendar->check_add_drop_end(
                $current_academic_year,
                $semester,
                $this->department_id,
                $year_level_id,
                $studentDetails['Student']['program_id'],
                $studentDetails['Student']['program_type_id']
            );
        } else {
            if (!empty($this->college_id)) {
                $getRegistrationDeadLine = $this->CourseDrop->CourseRegistration->AcademicCalendar->check_add_drop_end(
                    $current_academic_year,
                    $semester,
                    $this->college_id,
                    0,
                    $studentDetails['Student']['program_id'],
                    $studentDetails['Student']['program_type_id']
                );
            }
        }

        if ($getRegistrationDeadLine == 0 || $getRegistrationDeadLine == 1) {
        } else {
            $drop_start_date = $getRegistrationDeadLine;
            $getRegistrationDeadLine = 0;
        }

        if (!$getRegistrationDeadLine) {
            if (isset($drop_start_date) && !empty($drop_start_date)) {
                $this->Flash->info(
                    __(
                        'Course drop starts on ' . (date(
                            'M d, Y',
                            strtotime($drop_start_date)
                        )) . '. You can not drop courses now, please come back on the specified date.'
                    )
                );
            } else {
                $this->Flash->info(
                    __(
                        'Course drop deadline passed. You can not drop courses at this time, Please consult the registrar.'
                    )
                );
            }
            $this->redirect(array('controller' => 'courseRegistrations', 'action' => 'index'));
        } else {
            $detail = $this->CourseDrop->drop_courses_list(
                $this->student_id,
                $this->AcademicYear->current_academicyear()
            );
            $coursesDrop = $detail['courseDrop'];
            $student_section_exam_status = $detail['student_basic'];
            $already_dropped = $detail['alreadyDropped'];
            $semester = $detail['semester'];

            if (empty($detail['courseDrop'])) {
                $this->Flash->error(
                    'You can not drop courses for semester ' . $semester . ' of ' . ($this->AcademicYear->current_academicyear(
                    )) . ' . You have to get registered for the courses before dropping.'
                );
            } else {
                $this->set(compact('coursesDrop'));
                $this->set(compact('student_section_exam_status', 'already_dropped'));
            }
        }

        if (!empty($this->request->data)) {
            $selected = array_sum($this->request->data['CourseRegistration']['drop']);

            if ($selected > 0) {
                $selected_courses_for_drop = $this->request->data['CourseRegistration']['drop'];
                unset($this->request->data['CourseRegistration']['drop']);
                //unset($this->request->data['CourseRegistration']['student_id']);
                $delete_selected_from_registration = array();

                if (!empty($selected_courses_for_drop)) {
                    foreach ($selected_courses_for_drop as $k => $v) {
                        if ($v == 0) {
                            foreach ($this->request->data['CourseDrop'] as $cr => $cv) {
                                if ($cv['course_registration_id'] == $k) {
                                    unset($this->request->data['CourseDrop'][$cr]);
                                }
                            }
                        }
                    }
                }
                // if needed to delete from course registration table use deleted_selected_from_registration  array

                if (!empty($this->request->data['CourseDrop'])) {
                    foreach ($this->request->data['CourseDrop'] as $ds => &$dv) {
                        $delete_selected_from_registration[] = $dv['course_registration_id'];
                        // unset($dv['id']);
                    }
                }

                unset($this->request->data['CourseRegistration']);
                unset($this->request->data['Student']);

                $this->CourseDrop->create();
                $already_dropped_courses = array();
                $selected_courses_drop = array();

                if (!empty($this->request->data['CourseDrop'])) {
                    foreach ($this->request->data['CourseDrop'] as $cdd => $cdv) {
                        debug($cdv);
                        $check = $this->CourseDrop->find('count', array('conditions' => $cdv, 'recursive' => -1));
                        // already dropped, unset it
                        if ($check) {
                            $already_dropped_courses[] = $cdv['course_registration_id'];
                            // unset ($this->request->data['CourseDrop'][$cdd]);
                        } else {
                            $selected_courses_drop['CourseDrop'][] = $cdv;
                        }
                    }
                }

                //check duplicate dropping
                if (count($already_dropped_courses) == count($this->request->data['CourseDrop'])) {
                    $this->Flash->warning(
                        __('All the selected courses were already dropped. You do not need to drop it again')
                    );
                    //$this->redirect(array('action'=>'index',$this->request->data['CourseDrop'][0]['student_id']));
                } else {
                    unset($this->request->data);
                    $this->request->data = $already_dropped_courses;
                }

                if (!empty($selected_courses_drop['CourseDrop'])) {
                    if ($this->CourseDrop->saveAll($selected_courses_drop['CourseDrop'], array('validate' => 'first')
                    )) {
                        $this->Flash->success(__('The course drop request has been sent to department for approval.'));
                        // do hard deletion from course registration
                        $this->redirect(array('action' => 'index'));
                    } else {
                        $this->Flash->error(__('The course drop could not be dropped. Please, try again.'));
                    }
                } else {
                    $this->Flash->error(__('The selected courses could not be dropped.'));
                    //$this->redirect(array('action'=>'index',$this->request->data['CourseDrop'][0]['student_id']));
                }
            } else {
                $this->Flash->error(__('The course drop could not be saved. Please, select one courses to drop.'));
            }
        }
    }

    public function forcedDrop()
    {

        $current_academic_year = $this->AcademicYear->currentAcademicYear();
        $semester = '';
        $studentnumber = '';

        if (!empty($this->request->data) && isset($this->request->data['continue'])) {
            if (isset($this->request->data['Student']['academicyear'])) {
                $current_academic_year = $this->request->data['Student']['academicyear'];
            } /* else {
				$current_academic_year = '';
			} */

            if (isset($this->request->data['Student']['semseter']) && !empty($this->request->data['Student']['semseter'])) {
                $semester = $this->request->data['Student']['semseter'];
            } else {
                $semester = '';
            }

            if (isset($this->request->data['Student']['department_id']) && !empty($this->request->data['Student']['department_id'])) {
                $department_id = $this->request->data['Student']['department_id'];
            } else {
                $department_id = null;
            }

            if (isset($this->request->data['Student']['college_id']) && !empty($this->request->data['Student']['college_id'])) {
                $college_id = $this->request->data['Student']['college_id'];
            } else {
                $college_id = null;
            }

            if (isset($this->request->data['Student']['program_id']) && !empty($this->request->data['Student']['program_id'])) {
                $program_id = $this->request->data['Student']['program_id'];
                $program_ids = $this->request->data['Student']['program_id'];
            } else {
                $program_id = array_values($this->program_ids)[0];
                $program_ids = $this->program_ids;
            }

            if (isset($this->request->data['Student']['program_type_id']) && !empty($this->request->data['Student']['program_type_id'])) {
                $program_type_id = $this->request->data['Student']['program_type_id'];
                $program_type_ids = $this->request->data['Student']['program_type_id'];
            } else {
                $program_type_id = array_values($this->program_type_ids)[0];
                $program_type_ids = $this->program_type_ids;
            }

            if (isset($this->request->data['Student']['studentnumber']) && !empty($this->request->data['Student']['studentnumber'])) {
                $studentnumber = $this->request->data['Student']['studentnumber'];
            }

            $student_lists = array();

            if (!empty($this->department_ids)) {
                if (!empty($this->request->data['Student']['department_id'])) {
                    $department_ids = $this->request->data['Student']['department_id'];
                } else {
                    $department_ids = $this->department_ids;
                }

                $student_lists = $this->CourseDrop->list_of_students_need_force_drop(
                    $department_ids,
                    null,
                    $program_ids,
                    $program_type_ids,
                    $current_academic_year,
                    $semester
                );
            } else {
                if (!empty($this->college_ids)) {
                    if (!empty($this->request->data['Student']['college_id'])) {
                        $college_ids = $this->request->data['Student']['college_id'];
                    } else {
                        $college_ids = $this->college_ids;
                    }

                    $student_lists = $this->CourseDrop->list_of_students_need_force_drop(
                        null,
                        $college_ids,
                        $program_ids,
                        $program_type_ids,
                        $current_academic_year,
                        $semester,
                        1
                    );
                } else {
                    $student_lists = array();
                }
            }


            if (empty($student_lists['list'])) {
                $this->Flash->info(
                    'There are no studens who have registered for ' . $current_academic_year . ' academic year who needs dropping of courses. Either you have already dropped courses for those students, or grade has been submitted.'
                );
            } else {
                $this->set(compact('semester'));
                $latest_academic_year = $current_academic_year;
                $this->set('student_lists', $student_lists['list']);
                $this->set(
                    compact(
                        'current_academic_year',
                        'latest_academic_year',
                        'semester',
                        'program',
                        'program_type',
                        'studentnumber',
                        'college',
                        'department'
                    )
                );
            }
        } else {
            if (!empty($this->department_ids)) {
                $student_lists = $this->CourseDrop->list_of_students_need_force_drop(
                    $this->department_ids,
                    null,
                    $this->program_ids,
                    $this->program_type_ids,
                    $current_academic_year
                );
            } else {
                if (!empty($this->college_ids)) {
                    $student_lists = $this->CourseDrop->list_of_students_need_force_drop(
                        null,
                        $this->college_ids,
                        $this->program_ids,
                        $this->program_type_ids,
                        $current_academic_year,
                        null,
                        1
                    );
                } else {
                    $student_lists = array();
                }
            }

            $this->set('student_lists', $student_lists['list']);
        }

        $colleges = array();
        $departments = array();

        if (!empty($this->department_ids)) {
            $departments = $this->CourseDrop->CourseRegistration->PublishedCourse->Department->find(
                'list',
                array('conditions' => array('Department.id' => $this->department_ids, 'Department.active' => 1))
            );
        } else {
            if (!empty($this->college_ids)) {
                $colleges = $this->CourseDrop->CourseRegistration->PublishedCourse->College->find(
                    'list',
                    array('conditions' => array('College.id' => $this->college_ids, 'College.active' => 1))
                );
            }
        }

        $programs = $this->CourseDrop->Student->Program->find(
            'list',
            array('conditions' => array('Program.id' => $this->program_ids, 'Program.active' => 1))
        );
        $programTypes = $this->CourseDrop->Student->ProgramType->find(
            'list',
            array('conditions' => array('ProgramType.id' => $this->program_type_ids, 'ProgramType.active' => 1))
        );

        $this->set(compact(/* 'yearLevels', */ 'departments', 'programs', 'programTypes', 'colleges', 'studentnumber'));
    }

    public function delete($id = null)
    {

        if (!$id) {
            $this->Flash->error('Invalid Course Drop ID');
            return $this->redirect(array('action' => 'index'));
        }

        $this->CourseDrop->id = $id;

        if (!$this->CourseDrop->exists()) {
            $this->Flash->error('Invalid Course Drop ID');
            return $this->redirect(array('action' => 'index'));
        }

        $courseDropDetails = $this->CourseDrop->find('first', array(
            'conditions' => array('CourseDrop.id' => $id),
            'contain' => array(
                'CourseRegistration' => array(
                    'PublishedCourse' => array(
                        'Course' => array(
                            'Curriculum' => array('id', 'name', 'year_introduced', 'type_credit', 'active'),
                        ),
                    ),
                    'ExamGrade',
                    'ExamResult.course_add = 0',
                    'YearLevel' => array('fields' => array('id', 'name'))
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
            'recursive' => -1
        ));

        $deletion_allowed = false;
        $error_message = '';

        $course_name = (isset($courseDropDetails['CourseRegistration']['PublishedCourse']['Course']['id']) && !empty($courseDropDetails['CourseRegistration']['PublishedCourse']['Course']['course_code_title']) ? $courseDropDetails['CourseRegistration']['PublishedCourse']['Course']['course_code_title'] : '');
        $student_full_name = (isset($courseDropDetails['Student']['id']) && !empty($courseDropDetails['Student']['id']) ? $courseDropDetails['Student']['full_name'] . ' (' . $courseDropDetails['Student']['studentnumber'] . ')' : '');

        if ((isset($courseDropDetails['CourseRegistration']['ExamResult']) && !empty($courseDropDetails['CourseRegistration']['ExamResult'])) || (isset($courseDropDetails['CourseRegistration']['ExamGrade']) && !empty($courseDropDetails['CourseRegistration']['ExamGrade']))) {
            debug('Course Registration associated with the Course Drop have Exam Result or Exam Grade.');
            $error_message .= ' Course registration associated with this course drop have exam result or exam grade.';
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

            if ((isset($courseDropDetails['CourseDrop']['registrar_confirmation']) && !empty($courseDropDetails['CourseDrop']['registrar_confirmation'])) || (isset($courseDropDetails['CourseDrop']['department_approval']) && !empty($courseDropDetails['CourseDrop']['department_approval']))) {
                debug('Course Drop is approved by either Registrar or Departmnt.');
                $error_message .= ' This course drop is approved by either Registrar or Departmnt.';
                $deletion_allowed = false;

                if ($this->Session->read('Auth.User')['role_id'] == ROLE_REGISTRAR) {
                    $deletion_allowed = true;
                }
            }

            if (!isset($courseDropDetails['CourseRegistration']) || (isset($courseDropDetails['CourseRegistration']) && empty($courseDropDetails['CourseRegistration']))) {
                debug('Course Drop is no longer associated to Course Registration (Course Registration ID not found)');
                $error_message .= ' This course drop no longer associated to course registration (Course Registration ID not found).';
                $deletion_allowed = true;
            }

            if (!isset($courseDropDetails['CourseRegistration']['PublishedCourse']) || (isset($courseDropDetails['CourseRegistration']['PublishedCourse']) && empty($courseDropDetails['CourseRegistration']['PublishedCourse']))) {
                debug('Course Drop have is no longer associated to PublishedCourse (PublishedCourse ID not found)');
                $error_message .= ' This course drop no longer associated to published course (Published Course ID not found).';
                $deletion_allowed = true;
            }

            if (isset($courseDropDetails['CourseRegistration']['PublishedCourse']['id']) && !isset($courseDropDetails['CourseRegistration']['PublishedCourse']['Course']['id'])) {
                debug('Course Drop is no longer associated to Published Course (Course ID not found)');
                $error_message .= ' This course drop no longer associated to published course (Course ID not found).';
                $deletion_allowed = true;
            }


            if (isset($courseDropDetails['CourseDrop']['forced']) && !empty($courseDropDetails['CourseDrop']['forced'])) {
                debug('Course Drop is a Forced Drop.');
                $error_message .= ' This course drop is a forced drop.';
                $deletion_allowed = true;
            }

            if (isset($courseDropDetails['Student']['graduated']) && $courseDropDetails['Student']['graduated']) {
                debug(
                    (!empty($student_full_name) ? $student_full_name : 'The selected student') . ' is Graduated Student.'
                );
                $error_message .= (!empty($student_full_name) ? $student_full_name : 'The selected student') . ' is Graduated Student.';
                $deletion_allowed = false;
            }
        }

        if ($this->Session->read(
                'Auth.User'
            )['role_id'] == ROLE_REGISTRAR && !empty($courseDropDetails['Student']['id'])) {
            if (!empty($this->department_ids) && !empty($courseDropDetails['Student']['department_id']) && !in_array(
                    $courseDropDetails['Student']['department_id'],
                    $this->department_ids
                )) {
                //$error_message .= 'You are not authorized to drop courses for '. (!empty($student_full_name) ? $student_full_name : 'the selected student');
                $error_message .= 'You are not authorized to drop courses for the selected student.';
                $deletion_allowed = false;
            }
            if (!empty($this->college_ids) && !empty($courseDropDetails['Student']['college_id']) && !in_array(
                    $courseDropDetails['Student']['college_id'],
                    $this->college_ids
                )) {
                //$error_message .= 'You are not authorized to drop courses for '. (!empty($student_full_name) ? $student_full_name : 'the selected student');
                $error_message .= 'You are not authorized to drop courses for the selected student.';
                $deletion_allowed = false;
            }
        }


        if ($this->Session->read(
                'Auth.User'
            )['role_id'] == ROLE_STUDENT && !empty($courseDropDetails['Student']['id']) && $this->student_id !== $courseDropDetails['Student']['id']) {
            $error_message .= 'You are not authorized to drop courses for other student. Don\'t try this again, your action is logged and reported.';
        }

        //debug($courseDropDetails);

        if ($this->Session->read('Auth.User')['role_id'] == ROLE_STUDENT && empty($error_message)) {
            if ($this->CourseDrop->delete($id)) {
                $this->Flash->success(
                    'Course drop cancellation successfull' . (!empty($course_name) ? ' for ' . $course_name . '.' : '.')
                );
                //return $this->redirect(array('action' => 'index'));
                $this->redirect(Router::url($this->referer(), true));
            }
        } else {
            if ($this->Session->read('Auth.User')['role_id'] == ROLE_REGISTRAR && $deletion_allowed) {
                if ($this->CourseDrop->delete($id)) {
                    $this->Flash->success(
                        'Course drop cancellation is successfull for ' . (!empty($student_full_name) ? $student_full_name : 'the selected student') . (!empty($course_name) ? ' for ' . $course_name . '.' : '.') . (!empty($error_message) ? ' With the following additional alerts: ' . $error_message : '')
                    );
                    //return $this->redirect(array('action' => 'index'));

                    if ($this->Session->read('Auth.User')['role_id'] == ROLE_REGISTRAR && $this->Session->read(
                            'Auth.User'
                        )['is_admin'] == 1 && isset($courseDropDetails['Student']['id']) && !$courseDropDetails['Student']['graduated']) {
                        // regenerate student status
                        $this->CourseDrop->Student->StudentExamStatus->regenerate_all_status_of_student_by_student_id(
                            $courseDropDetails['Student']['id'],
                            0
                        );
                    }
                    $this->redirect(Router::url($this->referer(), true));
                }
            }
        }

        $this->Flash->error(
            'Course drop was not cancelled.' . (!empty($error_message) ? $error_message : ' It is associated to Exam Grades.')
        );
        //return $this->redirect(array('action' => 'index'));
        $this->redirect(Router::url($this->referer(), true));
    }

}
