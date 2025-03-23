<?php

namespace App\Controller;

use Cake\Core\Configure;
use Cake\Event\Event;

class SenateListsController extends AppController
{

    public $name = 'SenateLists';

    public $menuOptions = array(
        'parent' => 'graduation',
        'exclude' => array('search', 'delete', 'generate_pdf'),
        'weight' => 1,
        'alias' => array(
            'index' => 'View Senate List',
            'add' => 'Prepare Senate List'
        )
    );


    public $paginate = array();

    public function initialize()
    {

        parent::initialize();
        $this->loadComponent('AcademicYear');
        $this->loadComponent('EthiopicDateTime');
        $this->loadComponent('Paginator');
    }

    public function beforeFilter(Event $event)
    {

        parent::beforeFilter($event);
        $this->Auth->allow(
            'search',
            'generate_pdf'
        );
    }


    public function __init_search_senate_list()
    {

        if (!empty($this->request->data['Search'])) {
            $this->Session->write('search_data_senate_list', $this->request->data['Search']);
        } else {
            if ($this->Session->check('search_data_senate_list')) {
                $this->request->data['Search'] = $this->Session->read('search_data_senate_list');
            }
        }
    }

    public function __init_clear_session_filters($data = null)
    {

        if ($this->Session->check('search_data_senate_list')) {
            $this->Session->delete('search_data_senate_list');
        }
        //return $this->redirect(array('action' => 'index', $data));
    }

    public function search()
    {

        $this->__init_search_senate_list();

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

    public function index($data = null)
    {

        $defaultacademicyear = $current_academicyear = $this->AcademicYear->current_academicyear();

        if (is_numeric(ACY_BACK_FOR_ALL) && ACY_BACK_FOR_ALL) {
            $acyear_array_data = $this->AcademicYear->academicYearInArray(
                ((explode('/', $current_academicyear)[0]) - ACY_BACK_FOR_ALL),
                (explode('/', $current_academicyear)[0])
            );
        } else {
            $acyear_array_data[$current_academicyear] = $current_academicyear;
        }

        //debug($acyear_array_data);

        //$this->set('defaultacademicyear', $defaultacademicyear);

        $programs = ClassRegistry::init('Program')->find(
            'list',
            array('conditions' => array('Program.id' => $this->program_ids, 'Program.active' => 1))
        );
        $program_types = $programTypes = ClassRegistry::init('ProgramType')->find(
            'list',
            array('conditions' => array('ProgramType.id' => $this->program_type_ids, 'ProgramType.active' => 1))
        );


        $departments = array();

        if ($this->Session->read('Auth.User')['role_id'] == ROLE_DEPARTMENT) {
            $departments = ClassRegistry::init('Department')->find(
                'list',
                array('conditions' => array('Department.id' => $this->department_id))
            );

            if (!empty($programs)) {
                $programs = [0 => 'All Programs'] + $programs;
            }

            if (!empty($program_types)) {
                $program_types = $programTypes = [0 => 'All Program Types'] + $program_types;
            }
        } elseif ($this->Session->read('Auth.User')['role_id'] == ROLE_COLLEGE && $this->Session->read(
                'Auth.User'
            )['is_admin'] == 1) {
            $departments = ClassRegistry::init('Department')->allDepartmentsByCollege2(
                1,
                array(),
                $this->college_id,
                1
            );

            if (!empty($programs)) {
                $programs = [0 => 'All Programs'] + $programs;
            }

            if (!empty($program_types)) {
                $program_types = $programTypes = [0 => 'All Program Types'] + $program_types;
            }
        } elseif (($this->Session->read('Auth.User')['role_id'] == ROLE_REGISTRAR || $this->Session->read(
                    'Auth.User'
                )['role_id'] == ROLE_COLLEGE) && $this->Session->read('Auth.User')['is_admin'] == 0) {
            if (isset($this->program_ids) && !empty($this->program_ids)) {
                $programs = ClassRegistry::init('Program')->find(
                    'list',
                    array('conditions' => array('Program.id' => $this->program_ids, 'Program.active' => 1))
                );
            } else {
                $programs = array();
            }

            if (isset($this->program_type_ids) && !empty($this->program_type_ids)) {
                $program_types = $programTypes = ClassRegistry::init('ProgramType')->find(
                    'list',
                    array('conditions' => array('ProgramType.id' => $this->program_type_ids, 'ProgramType.active' => 1))
                );
            } else {
                $program_types = $programTypes = array();
            }

            if (!empty($programs)) {
                $programs = [0 => 'Assigned Programs'] + $programs;
            }

            if (!empty($program_types)) {
                $program_types = $programTypes = [0 => 'Assigned Program Types'] + $program_types;
            }


            if (isset($this->department_ids) && !empty($this->department_ids)) {
                $departments = ClassRegistry::init('Department')->find(
                    'list',
                    array('conditions' => array('Department.id' => $this->department_ids))
                );

                if (!empty($departments)) {
                    $departments = [0 => 'All Assigned Departments'] + $departments;
                }
            } else {
                if ($this->onlyPre == 0 && ((isset($this->college_ids) && !empty($this->college_ids)) || (isset($this->college_id) && !empty($this->college_id)))) {
                    if ($this->Session->read('Auth.User')['role_id'] == ROLE_REGISTRAR) {
                        $departments = ClassRegistry::init('Department')->find(
                            'list',
                            array('conditions' => array('Department.college_id' => $this->college_ids))
                        );

                        if (!empty($departments)) {
                            $departments = [0 => 'All Assigned Departments'] + $departments;
                        }
                    } else {
                        $departments = ['0' => 'College Freshman'];
                    }
                } else {
                    $departments = array();
                }
            }
        } else {
            $departments = ClassRegistry::init('Department')->allDepartmentsByCollege2(
                1,
                $this->department_ids,
                $this->college_ids,
                1
            );

            if (!empty($departments)) {
                $departments = array(0 => 'All ' . Configure::read('CompanyName') . ' Students') + $departments;
            }

            if (!empty($programs)) {
                $programs = [0 => 'All Programs'] + $programs;
            }

            if (!empty($program_types)) {
                $program_types = $programTypes = [0 => 'All Program Types'] + $program_types;
            }
        }

        $this->set(
            compact(
                'acyear_array_data',
                'defaultacademicyear',
                'program_types',
                'programs',
                'programTypes',
                'departments'
            )
        );

        $limit = 100;
        $minute_number = '';
        $excludeMajor = 1;
        $sort_by = array('full_name' => 'ASC');
        $page = 1;

        $senate_date_from = Configure::read('Calendar.senateListStartYear');
        $senate_date_to = date('Y');

        $options = array();

        if (isset($this->passedArgs) && !empty($this->passedArgs)) {
            //debug($this->passedArgs);

            if (!empty($this->passedArgs['Search.limit'])) {
                $limit = $this->request->data['Search']['limit'] = $this->passedArgs['Search.limit'];
            }

            if (!empty($this->passedArgs['Search.minute_number'])) {
                $minute_number = $this->request->data['Search']['minute_number'] = $this->passedArgs['Search.minute_number'];
            }

            if (!empty($this->passedArgs['Search.department_id']) || $this->passedArgs['Search.department_id'] == 0) {
                $this->request->data['Search']['department_id'] = $this->passedArgs['Search.department_id'];
            }

            if (!empty($this->passedArgs['Search.program_id']) || $this->passedArgs['Search.program_id'] == 0) {
                $this->request->data['Search']['program_id'] = $this->passedArgs['Search.program_id'];
            }

            if (!empty($this->passedArgs['Search.program_type_id']) || $this->passedArgs['Search.program_type_id'] == 0) {
                $this->request->data['Search']['program_type_id'] = $this->passedArgs['Search.program_type_id'];
            }

            if (isset($this->passedArgs['Search.minute_number'])) {
                $minute_number = str_replace('-', '/', trim($this->passedArgs['Search.minute_number']));
                $this->request->data['Search']['minute_number'] = $this->passedArgs['Search.minute_number'];
            }

            if (isset($this->passedArgs['Search.sort_by'])) {
                $sort_by_order = explode('~', $this->passedArgs['Search.sort_by']);
                if (count($sort_by_order) > 1) {
                    $sort_by = array($sort_by_order[0] => $sort_by_order[1]);
                }
                $this->request->data['Search']['sort_by'] = $this->passedArgs['Search.sort_by'];
            }

            if (isset($this->passedArgs['Search.senate_date_from.year'])) {
                $senate_date_from = $this->passedArgs['Search.senate_date_from.year'] . '-' . $this->passedArgs['Search.senate_date_from.month'] . '-' . $this->passedArgs['Search.senate_date_from.day'];

                $this->request->data['Search']['senate_date_from']['year'] = $this->passedArgs['Search.senate_date_from.year'];
                $this->request->data['Search']['senate_date_from']['month'] = $this->passedArgs['Search.senate_date_from.month'];
                $this->request->data['Search']['senate_date_from']['day'] = $this->passedArgs['Search.senate_date_from.day'];

                $senate_date_to = $this->passedArgs['Search.senate_date_to.year'] . '-' . $this->passedArgs['Search.senate_date_to.month'] . '-' . $this->passedArgs['Search.senate_date_to.day'];

                $this->request->data['Search']['senate_date_to']['year'] = $this->passedArgs['Search.senate_date_to.year'];
                $this->request->data['Search']['senate_date_to']['month'] = $this->passedArgs['Search.senate_date_to.month'];
                $this->request->data['Search']['senate_date_to']['day'] = $this->passedArgs['Search.senate_date_to.day'];
            }

            if (isset($this->passedArgs['Search.page'])) {
                $page = $this->request->data['Search']['page'] = $this->passedArgs['Search.page'];
            }

            if (isset($this->passedArgs['Search.exclude_major'])) {
                $excludeMajor = $this->request->data['Search']['exclude_major'] = $this->passedArgs['Search.exclude_major'];
            }

            ////////////////////

            if (isset($this->passedArgs['page'])) {
                $page = $this->request->data['Search']['page'] = $this->passedArgs['page'];
            }

            if (isset($this->passedArgs['sort'])) {
                $this->request->data['Search']['sort'] = $this->passedArgs['sort'];
            }

            if (isset($this->passedArgs['direction'])) {
                $this->request->data['Search']['direction'] = $this->passedArgs['direction'];
            }

            ////////////////////

            $this->__init_search_senate_list();
        }

        //debug($sort_by);

        if (isset($data) && !empty($data['Search'])) {
            $this->request->data = $data;
            debug($this->request->data);
            $this->__init_search_senate_list();
        }

        if (isset($this->request->data['listStudentsForSenateList'])) {
            unset($this->passedArgs);
            $this->__init_clear_session_filters();
            $this->__init_search_senate_list();
        }

        debug($this->request->data);

        if (isset($this->request->data) && !empty($this->request->data)) {
            debug($this->request->data);

            $college_id = explode('~', $this->request->data['Search']['department_id']);

            count($college_id) > 1 ? debug($college_id[1]) : '';

            if (count($college_id) > 1) {
                if (!$this->onlyPre) {
                    if ($this->Session->read('Auth.User')['is_admin'] == 0 && $this->Session->read(
                            'Auth.User'
                        )['role_id'] == ROLE_REGISTRAR) {
                        $options['conditions'][] = array(
                            'Student.college_id' => $college_id[1],
                            'Student.department_id' => $this->department_ids
                        );
                    } else {
                        $options['conditions'][] = array('Student.college_id' => $college_id[1]);
                    }
                } else {
                    $options['conditions'][] = array(
                        'Student.department_id is null',
                        'Student.college_id' => $college_id[1]
                    );
                }
            } else {
                if ($this->request->data['Search']['department_id']) {
                    if ($this->Session->read('Auth.User')['is_admin'] == 0 && $this->Session->read(
                            'Auth.User'
                        )['role_id'] == ROLE_REGISTRAR && in_array(
                            $this->request->data['Search']['department_id'],
                            $this->department_ids
                        )) {
                        $options['conditions'][] = array('Student.department_id' => $this->request->data['Search']['department_id']);
                    } else {
                        if ($this->Session->read('Auth.User')['is_admin'] == 1 && $this->Session->read(
                                'Auth.User'
                            )['role_id'] == ROLE_REGISTRAR) {
                            $options['conditions'][] = array('Student.department_id' => $this->request->data['Search']['department_id']);
                        } else {
                            if ($this->Session->read('Auth.User')['role_id'] != ROLE_REGISTRAR) {
                                $options['conditions'][] = array('Student.department_id' => $this->request->data['Search']['department_id']);
                            } else {
                                // not authorized registrar user
                                $options['conditions'][] = array('Student.department_id' => 0);
                            }
                        }
                    }
                } else {
                    if (!empty($this->department_ids)) {
                        $options['conditions'][] = array('Student.department_id' => $this->department_ids);
                    } else {
                        if (!empty($this->college_ids) && !$this->onlyPre) {
                            $options['conditions'][] = array('Student.college_id' => $this->college_ids);
                        }
                    }
                }
            }

            if (!empty($this->request->data['Search']['program_id'])) {
                $options['conditions'][] = array('Student.program_id' => $this->request->data['Search']['program_id']);
            } else {
                if ($this->request->data['Search']['program_id'] == 0) {
                    //all assigned programs
                    if (!empty($this->program_ids)) {
                        $options['conditions'][] = array('Student.program_id' => $this->program_ids);
                    } else {
                        // do not find anything
                        $options['conditions'][] = array('Student.program_id' => 0);
                    }
                } else {
                    // do not find anything
                    $options['conditions'][] = array('Student.program_id' => 0);
                }
            }

            if (!empty($this->request->data['Search']['program_type_id']) && $this->request->data['Search']['program_type_id'] != 0) {
                $options['conditions'][] = array('Student.program_type_id' => $this->request->data['Search']['program_type_id']);
            } else {
                if ($this->request->data['Search']['program_type_id'] == 0) {
                    //all assigned program types
                    if (!empty($this->program_type_ids)) {
                        $options['conditions'][] = array('Student.program_type_id' => $this->program_type_ids);
                    } else {
                        // do not find anything
                        $options['conditions'][] = array('Student.program_type_id' => 0);
                    }
                } else {
                    // do not find anything
                    $options['conditions'][] = array('Student.program_type_id' => 0);
                }
            }

            if (!empty($minute_number)) {
                $options['conditions'][] = array('SenateList.minute_number' => $minute_number);
            }


            $options['conditions'][] = array('SenateList.approved_date >=' => $senate_date_from);
            $options['conditions'][] = array('SenateList.approved_date <=' => $senate_date_to);
        }

        debug($options);

        $senateLists = array();

        if (!empty($options['conditions'])) {
            $this->Paginator->settings = array(
                'conditions' => $options['conditions'],
                'contain' => array(
                    'Student' => array(
                        'order' => array(
                            'Student.college_id' => 'ASC',
                            'Student.department_id' => 'ASC',
                            'Student.program_id' => 'ASC',
                            'Student.program_type_id' => 'ASC',
                            'Student.first_name' => 'ASC',
                            'Student.middle_name' => 'ASC',
                            'Student.last_name' => 'ASC'
                        ),
                        'Department',
                        'Curriculum',
                        'ProgramType',
                        'CourseDrop' => array(
                            'CourseRegistration' => array(
                                'PublishedCourse' => array('Course')
                            ),
                            'order' => array(
                                'CourseDrop.academic_year' => 'ASC',
                                'CourseDrop.semester' => 'ASC',
                                'CourseDrop.id' => 'ASC'
                            )
                        ),
                        'CourseAdd' => array(
                            'PublishedCourse' => array(
                                'Course' => array('GradeType')
                            ),
                            'order' => array(
                                'CourseAdd.academic_year' => 'ASC',
                                'CourseAdd.semester' => 'ASC',
                                'CourseAdd.id' => 'ASC'
                            )
                        ),
                        'CourseRegistration' => array(
                            'PublishedCourse' => array(
                                'Course' => array('GradeType')
                            ),
                            'order' => array(
                                'CourseRegistration.academic_year' => 'ASC',
                                'CourseRegistration.semester' => 'ASC',
                                'CourseRegistration.id' => 'ASC'
                            )
                        ),
                        'Program',
                        'StudentExamStatus' => array(
                            //'order' => array('StudentExamStatus.created' => 'DESC')
                            'order' => array(
                                'StudentExamStatus.academic_year' => 'DESC',
                                'StudentExamStatus.semester' => 'DESC',
                                'StudentExamStatus.created' => 'DESC'
                            ),
                        ),
                        //'order' => $sort_by
                    ),
                ),
                'order' => array('SenateList.approved_date' => 'DESC'),
                'limit' => (!empty($limit) ? $limit : 100),
                'maxLimit' => (!empty($limit) ? $limit : 100),
                'page' => (isset($page) && $page > 0 ? $page : 1)
            );

            try {
                $senateLists = $this->Paginator->paginate($this->modelClass);
                $this->set(compact('senateLists'));
            } catch (NotFoundException $e) {
                if (!empty($this->request->data['Search'])) {
                    unset($this->request->data['Search']['page']);
                    unset($this->request->data['Search']['sort']);
                    unset($this->request->data['Search']['direction']);
                }
                if (!empty($this->request->data['SenateList'])) {
                    unset($this->request->data['SenateList']['page']);
                    unset($this->request->data['SenateList']['sort']);
                    unset($this->request->data['SenateList']['direction']);
                }
                unset($this->passedArgs);
                $this->Session->write('search_data_senate_list', $this->request->data['Search']);
                return $this->redirect(array('action' => 'index'));
            } catch (Exception $e) {
                if (!empty($this->request->data['Search'])) {
                    unset($this->request->data['Search']['page']);
                    unset($this->request->data['Search']['sort']);
                    unset($this->request->data['Search']['direction']);
                }
                if (!empty($this->request->data['SenateList'])) {
                    unset($this->request->data['SenateList']['page']);
                    unset($this->request->data['SenateList']['sort']);
                    unset($this->request->data['SenateList']['direction']);
                }
                unset($this->passedArgs);
                $this->Session->write('search_data_senate_list', $this->request->data['Search']);
                return $this->redirect(array('action' => 'index'));
            }

            if (!empty($senateLists)) {
                if ($this->Session->check('generate_pdf_options')) {
                    $this->Session->delete('generate_pdf_options');
                    $this->Session->delete('generate_pdf_sort_by');
                    $this->Session->delete('generate_pdf_exclude_major');
                }

                $this->Session->write('generate_pdf_options', $options);
                $this->Session->write('generate_pdf_sort_by', $sort_by);
                $this->Session->write('generate_pdf_exclude_major', $excludeMajor);
                $this->Session->write('generate_pdf_limit', $limit);
            }
        }

        //debug(count($senateLists));

        if (empty($senateLists) && isset($this->request->data) && !empty($options['conditions'])) {
            $this->Flash->info('There is no student in the senate list based on the given criteria.');

            if ($this->Session->check('generate_pdf_options')) {
                $this->Session->delete('generate_pdf_options');
                $this->Session->delete('generate_pdf_sort_by');
                $this->Session->delete('generate_pdf_exclude_major');
            }
        }

        $this->set(
            compact(
                'programs',
                'program_types',
                'departments',
                'senateLists',
                'excludeMajor',
                'limit',
                'minute_number',
                'senate_date_from',
                'senate_date_to',
                'page'
            )
        );

        /* //$this->paginate = array('limit' => 100);
        $this->paginate = array(
            'contain' => array(
                'Student' => array(
                    'Department',
                    'Curriculum',
                    'ProgramType',
                    'CourseDrop' => array(
                        'CourseRegistration' => array(
                            'PublishedCourse' => array('Course')
                        )
                    ),
                    'CourseAdd' => array(
                        'PublishedCourse' => array(
                            'Course' => array('GradeType')
                        )
                    ),
                    'CourseRegistration' => array(
                        'PublishedCourse' => array(
                            'Course' => array('GradeType')
                        )
                    ),
                    'Program',
                    'StudentExamStatus' => array(
                        'order' => array('StudentExamStatus.created DESC')
                    ),
                    'order' => array('Student.full_name ASC')
                ),

            ),
            'limit' => 100,
            'maxLimit'=> 5000
        );

        $selectedLimit = 100;


        if (isset($this->request->data)) {
            $this->passedArgs = $this->request->data;
        }

        debug($this->passedArgs);

        if (isset($this->passedArgs)) {
            if (isset($this->passedArgs['page'])) {
                $this->__init_search_senate_list();
                $this->request->data['SenateList']['page'] = $this->passedArgs['page'];
                $this->__init_search_senate_list();
            }
        }

        if ((isset($this->request->data['SenateList']) && isset($this->request->data['listStudentsForSenateList']))) {
            $this->__init_search_senate_list();
        }

        // filter by department or college
        if (isset($this->request->data['SenateList']['limit']) && !empty($this->request->data['SenateList']['limit'])) {
            $this->paginate['limit'] = $this->request->data['SenateList']['limit'];
            $selectedLimit = $this->request->data['SenateList']['limit'];
        }

        // filter by department or college
        if (isset($this->request->data['SenateList']['department_id'])) {
            if ($this->Session->read('Auth.User')['role_id'] == ROLE_REGISTRAR && $this->Session->read('Auth.User')['is_admin'] != 1 && $this->request->data['SenateList']['department_id'] == 0) {
                if (!empty($this->college_ids)) {
                    $this->paginate['conditions'][]['Student.college_id'] =  $this->college_ids;
                }
                if (!empty($this->department_ids)) {
                    $this->paginate['conditions'][]['Student.department_id'] = $this->department_ids;
                }
            } else if(!empty($this->request->data['SenateList']['department_id'])) {
                $department_id = $this->request->data['SenateList']['department_id'];
                $college_id = explode('~', $department_id);
                if (count($college_id) > 1) {
                    $this->paginate['conditions'][]['Student.college_id'] = $college_id[1];
                } else {
                    $this->paginate['conditions'][]['Student.department_id'] = $department_id;
                }
            }
        }

        // filter by program
        if (isset($this->request->data['SenateList']['program_id'])) {
            if ($this->Session->read('Auth.User')['role_id'] == ROLE_REGISTRAR && $this->Session->read('Auth.User')['is_admin'] != 1 && $this->request->data['SenateList']['program_id'] == 0) {
                if (!empty($this->program_id)) {
                    $this->paginate['conditions'][]['Student.program_id'] = $this->program_id;
                }
            } else if(!empty($this->request->data['SenateList']['program_id'])) {
                $this->paginate['conditions'][]['Student.program_id'] = $this->request->data['SenateList']['program_id'];
            }
        }

        // filter by program type
        if (isset($this->request->data['SenateList']['program_type_id'])) {
            if ($this->Session->read('Auth.User')['role_id'] == ROLE_REGISTRAR && $this->Session->read('Auth.User')['is_admin'] != 1 && $this->request->data['SenateList']['program_type_id'] == 0) {
                if (!empty($this->program_type_id)) {
                    $this->paginate['conditions'][]['Student.program_type_id'] = $this->program_type_id;
                }
            } else if(!empty($this->request->data['SenateList']['program_type_id'])){
                $this->paginate['conditions'][]['Student.program_type_id'] = $this->request->data['SenateList']['program_type_id'];
            }
        }

        // filter by minute number
        if (isset($this->request->data['SenateList.minute_number']) && !empty($this->request->data['SenateList']['minute_number'])) {
            unset($this->paginate);
            $this->paginate['conditions'][]['SenateList.minute_number'] = $this->request->data['SenateList']['minute_number'];
        }

        // filter by period
        if (isset($this->request->data['SenateList']['senate_date_from']['year'])) {
            $this->paginate['conditions'][] = array('SenateList.approved_date >= \'' . $this->request->data['SenateList']['senate_date_from']['year'] . '-' . $this->request->data['SenateList']['senate_date_from']['month'] . '-' . $this->request->data['SenateList']['senate_date_from']['day'] . '\'');
            $this->paginate['conditions'][] = array('SenateList.approved_date <= \'' . $this->request->data['SenateList']['senate_date_to']['year'] . '-' . $this->request->data['SenateList']['senate_date_to']['month'] . '-' . $this->request->data['SenateList']['senate_date_to']['day'] . '\'');
        } */

        /* if (isset($this->request->data['SenateList']['page']) && !empty($this->request->data['SenateList']['page'])) {
            $this->paginate['page'] = $this->request->data['SenateList']['page'];
        } */

        // Sort
        /* if (isset($this->request->data['SenateList']['sort_by']) && !empty($this->request->data['SenateList']['sort_by'])) {
            if ($this->request->data['SenateList']['sort_by'] == "full_name ASC" || $this->request->data['SenateList']['sort_by'] == "full_name DESC") {
                $this->paginate['contain']['Student']['order'] = $this->request->data['SenateList']['sort_by'];
            } else {
                $this->paginate['order'] = $this->request->data['SenateList']['sort_by'];
            }
        }

        if ((isset($this->request->data['SenateList']) && isset($this->request->data['viewPDF']))) {
            $search_session = $this->Session->read('search_data_senate_list');
            $this->request->data['SenateList'] = $search_session;
        }

        $this->Paginator->settings = $this->paginate;

        if (isset($this->Paginator->settings['conditions'])) {
            $students_for_senate_list = $senateLists = $this->Paginator->paginate('SenateList');
        } else {
            $senateLists = array();
        }

        if (empty($senateLists) && isset($this->request->data) && !empty($this->request->data)) {
            $this->Flash->info('There is no student in the senate list based on the given criteria.');
        }

        $excludeMajor = 1;

        if(isset($this->request->data['SenateList']['exclude_major'])){
            $excludeMajor = $this->request->data['SenateList']['exclude_major'];
        }

        $programs = $this->SenateList->Student->Program->find('list');
        $program_types = $this->SenateList->Student->ProgramType->find('list');

        //$departments = $this->SenateList->Student->Department->allDepartmentsByCollege2(1, $this->department_ids, $this->college_ids);

        $programs = array(0 => 'All Programs') + $programs;
        $program_types = array(0 => 'All Program Types') + $program_types;
        //$departments = array(0 => 'All University Students') + $departments;

        $default_department_id = null;
        $default_program_id = null;
        $default_program_type_id = null;

        if ($this->Session->read('Auth.User')['role_id'] == ROLE_DEPARTMENT) {
            $departments = $this->SenateList->Student->Department->find('list', array('conditions' => array('Department.id' => $this->department_id)));
        } else if ($this->Session->read('Auth.User')['role_id'] == ROLE_COLLEGE) {
            $departments =  $this->SenateList->Student->Department->allDepartmentsByCollege2(1, array(), $this->college_id);
        } else if ($this->Session->read('Auth.User')['role_id'] == ROLE_REGISTRAR && $this->Session->read('Auth.User')['is_admin'] != 1) {

            if (isset($this->program_id) && !empty($this->program_id)) {
                $programs = $this->SenateList->Student->Program->find('list', array('conditions' => array('Program.id' => $this->program_id)));
                $programs = array(0 => 'All Assigned Programs') + $programs;
            } else {
                $programs = array();
            }

            if (isset($this->program_type_id) && !empty($this->program_type_id)) {
                $program_types = $this->SenateList->Student->ProgramType->find('list', array('conditions' => array('ProgramType.id' => $this->program_type_id)));
                $program_types = array(0 => 'All Assigned Program Types') + $program_types;
            } else {
                $program_types = array();
            }

            if (isset($this->college_ids) && !empty($this->college_ids)) {
                $departments =  $this->SenateList->Student->Department->find('list', array('conditions' => array('Department.college_id' => $this->college_ids)));
                $departments = array(0 => 'All Assigned Departments') + $departments;
            } else {
                $departments = array();
            }

            if (isset($this->department_ids) && !empty($this->department_ids)) {
                $departments = $this->SenateList->Student->Department->find('list', array('conditions' => array('Department.id' => $this->department_ids)));
                $departments = array(0 => 'All Assigned Departments') + $departments;
            } else {
                $departments = array();
            }

        } else {
            $departments = $this->SenateList->Student->Department->allDepartmentsByCollege2(1, $this->department_ids, $this->college_ids);
            $departments = array(0 => 'All University Students') + $departments;
        }

        if ((!empty($this->request->data['SenateList']) && !empty($this->request->data['viewPDF']))) {

            $student_ids = array();
            $certificate_template = array();
            $students_for_senate_list_pdf = array();
            $count = 1;

            foreach ($students_for_senate_list as $k => $v) {

                $v['Student']['ExemptedCredit'] = $this->SenateList->Student->CourseExemption->getStudentCourseExemptionCredit($v['Student']['id']);
                $v['Student']['TransferedCredit'] = $this->SenateList->Student->DepartmentTransfer->getTransferedCourseCredit($v['Student']['id']);
                $v['Student']['CourseDroppedCredit'] = $this->SenateList->Student->CourseDrop->droppedCreditSum($v['Student']['id']);

                if ($v['Student']['program_id'] == 2 ||  $v['Student']['program_id'] == 3) {
                    $v['Student']['ThesisResult'] = ClassRegistry::init('ExamGrade')->getApprovedThesisGrade($v['Student']['id']);
                }

                if ($v['Student']['program_id'] == 1) {
                    $v['Student']['ExitExamGrade'] = ClassRegistry::init('ExamGrade')->getApprovedExitExamGrade($v['Student']['id']);
                }

                $students_for_senate_list_pdf[$v['Student']['Program']['name'] . '~' . $v['Student']['ProgramType']['name'] . '~' . $v['Student']['Department']['name'] . '~' . $v['Student']['Curriculum']['name'] . '~' . $v['Student']['Curriculum']['minimum_credit_points'] . '~' . $v['Student']['Curriculum']['amharic_degree_nomenclature'] . '~' . $v['Student']['Curriculum']['specialization_amharic_degree_nomenclature'] . '~' . $v['Student']['Curriculum']['english_degree_nomenclature'] . '~' . $v['Student']['Curriculum']['specialization_english_degree_nomenclature'] . '~' . $v['Student']['Curriculum']['type_credit']][] = $v;
                //debug($students_for_senate_list_pdf);
            }

            $defaultacademicyear = $this->AcademicYear->current_academicyear();
            $ethiopicYear = $e_year = $this->EthiopicDateTime->GetEthiopicYear(date('j'), date('n'), date('Y'));

            $this->set(compact('students_for_senate_list_pdf', 'defaultacademicyear', 'excludeMajor', 'ethiopicYear'));

            $this->response->type('application/pdf');
            $this->layout = '/pdf/default';
            $this->render('senate_list_masspage_pdf');
        } */
        //$this->set(compact('programs', 'program_types', 'departments', 'students_in_senate_list', 'default_department_id', 'default_program_id', 'default_program_type_id', 'senateLists', 'excludeMajor','limit', 'minute_number'));
    }

    /* function __init_search_senate_list()
    {
        if (!empty($this->request->data['SenateList'])) {
            $this->Session->write('search_data_senate_list', $this->request->data['SenateList']);
        } else if ($this->Session->check('search_data_senate_list')) {
            $this->request->data['SenateList'] = $this->Session->read('search_data_senate_list');
        }
    } */


    /***
     * 1. Display list of department, program, program type (optional)
     * 2. After "List Students" button, all students but note in the senate list and graduation list who take all courses will be displayed (non eligible students will be displayed in red with justification '+')
     * 3. A check-box to include students in the senate list
     ***/

    public function add($department_id = null, $program_id = null, $program_type_id = null)
    {

        $programs = $this->SenateList->Student->Program->find(
            'list',
            array('conditions' => array('Program.id' => $this->program_ids, 'Program.active' => 1))
        );
        $program_types = $this->SenateList->Student->ProgramType->find(
            'list',
            array('conditions' => array('ProgramType.id' => $this->program_type_ids, 'ProgramType.active' => 1))
        );
        $departments = $this->SenateList->Student->Department->allDepartmentsByCollege2(
            0,
            $this->department_ids,
            $this->college_ids,
            1
        );
        $department_combo_id = null;

        $program_types = array(0 => 'All Program Types') + $program_types;

        $default_department_id = null;
        $default_program_id = null;
        $default_program_type_id = null;

        if ($this->Session->read('Auth.User')['role_id'] == ROLE_REGISTRAR && $this->Session->read(
                'Auth.User'
            )['is_admin'] != 1) {
            if (isset($this->program_id) && !empty($this->program_id)) {
                $programs = $this->SenateList->Student->Program->find(
                    'list',
                    array('conditions' => array('Program.id' => $this->program_id))
                );
                //$programs = array(0 => 'All Assigned Programs') + $programs;
            } else {
                $programs = array();
            }

            if (isset($this->program_type_id) && !empty($this->program_type_id)) {
                $program_types = $this->SenateList->Student->ProgramType->find(
                    'list',
                    array('conditions' => array('ProgramType.id' => $this->program_type_id))
                );
                $program_types = array(0 => 'All Assigned Program Types') + $program_types;
            } else {
                $program_types = array();
            }

            if (isset($this->college_ids) && !empty($this->college_ids)) {
                $departments = $this->SenateList->Student->Department->find(
                    'list',
                    array('conditions' => array('Department.college_id' => $this->college_ids))
                );
                //$departments = array(0 => 'All Assigned Departments') + $departments;
            } else {
                $departments = array();
            }

            if (isset($this->department_ids) && !empty($this->department_ids)) {
                $departments = $this->SenateList->Student->Department->find(
                    'list',
                    array('conditions' => array('Department.id' => $this->department_ids))
                );
                //$departments = array(0 => 'All Assigned Departments') + $departments;
            } else {
                $departments = array();
            }
        }

        //When any of the button is clicked (List students or Add to Senate List)

        if (!empty($this->request->data) && !empty($this->request->data['listStudentsForSenateList'])) {
            //debug($this->request->data);

            $students_for_senate_list = $this->SenateList->getListOfStudentsForSenateList(
                $this->request->data['SenateList']['program_id'],
                $this->request->data['SenateList']['program_type_id'],
                $this->request->data['SenateList']['department_id'],
                $this->request->data['SenateList']['academicyear'],
                $this->request->data['SenateList']['percent_completed'],
                $this->request->data['SenateList']['studentnumber']
            );
            $default_department_id = $this->request->data['SenateList']['department_id'];
            $default_program_id = $this->request->data['SenateList']['program_id'];
            $default_program_type_id = $this->request->data['SenateList']['program_type_id'];

            if (empty($students_for_senate_list)) {
                if (isset($this->request->data['SenateList']['studentnumber']) && !empty($this->request->data['SenateList']['studentnumber'])) {
                    $studentId = $this->SenateList->Student->field(
                        'Student.id',
                        array('Student.studentnumber' => trim($this->request->data['SenateList']['studentnumber']))
                    );
                    debug($studentId);

                    if (isset($studentId) && is_numeric($studentId) && $studentId > 0) {
                        $studentDetails = $this->SenateList->Student->find('first', array(
                            'conditions' => array(
                                'Student.id' => $studentId
                            ),
                            'contain' => array(
                                'SenateList',
                                'GraduateList',
                                'Department' => array(
                                    'fields' => array('id', 'name', 'shortname', 'college_id'),
                                    'College' => array(
                                        'fields' => array('id', 'name', 'shortname', 'campus_id'),
                                        'Campus' => array('id', 'name')
                                    )
                                ),
                                'Program' => array('id', 'name'),
                                'ProgramType' => array('id', 'name'),
                                'Curriculum' => array(
                                    'id',
                                    'name',
                                    'year_introduced',
                                    'type_credit',
                                    'active',
                                    'minimum_credit_points'
                                ),
                            ),
                            'recursive' => -1
                        ));

                        //debug($studentDetails);

                        if (ClassRegistry::init('GraduateList')->isGraduated($studentId)) {
                            $this->Flash->info(
                                $studentDetails['Student']['full_name'] . ' (' . $studentDetails['Student']['studentnumber'] . ') is already included in Graduate List dated ' . (CakeTime::format(
                                    "F j, Y",
                                    $studentDetails['GraduateList']['graduate_date'],
                                    false,
                                    null
                                )) . ' with Minute Number: ' . $studentDetails['GraduateList']['minute_number'] . '. No need to add the student to senate list again.'
                            );
                        } else {
                            if (isset($studentDetails['SenateList'][0]) && !empty($studentDetails['SenateList'][0]['student_id'])) {
                                $this->Flash->info(
                                    $studentDetails['Student']['full_name'] . ' (' . $studentDetails['Student']['studentnumber'] . ') is already included in Sanate List dated ' . (CakeTime::format(
                                        "F j, Y",
                                        $studentDetails['SenateList'][0]['approved_date'],
                                        false,
                                        null
                                    )) . ' with Minute Number: ' . $studentDetails['SenateList'][0]['minute_number'] . '. No need to add the student to senate list again.'
                                );
                            } else {
                                if (!isset($studentDetails['Curriculum']['id'])) {
                                    $this->Flash->warning(
                                        $studentDetails['Student']['full_name'] . ' (' . $studentDetails['Student']['studentnumber'] . ') couldn\'t be included in senate list. The student is not attached to any curriculum.'
                                    );
                                } else {
                                    if (!empty($this->department_ids) && !empty($studentDetails['Student']['department_id']) && in_array(
                                            $studentDetails['Student']['department_id'],
                                            $this->department_ids
                                        )) {
                                        if ($this->request->data['SenateList']['program_id'] != $studentDetails['Student']['program_id'] || $this->request->data['SenateList']['program_type_id'] != $studentDetails['Student']['program_type_id'] || $this->request->data['SenateList']['department_id'] != $studentDetails['Student']['department_id']) {
                                            $this->Flash->info(
                                                'The system auto adjusted the search filters to ' . $studentDetails['Program']['name'] . ', ' . $studentDetails['ProgramType']['name'] . ', ' . $studentDetails['Department']['name'] . ' form ' . $studentDetails['Student']['full_name'] . ' (' . $studentDetails['Student']['studentnumber'] . ') admission details. You need click on List Eligible Students button to Search again.'
                                            );
                                        } else {
                                            $this->Flash->info(
                                                $studentDetails['Student']['full_name'] . ' (' . $studentDetails['Student']['studentnumber'] . ') couldn\'t be included in senate list. Either the student does\'t fulfill the requred minimum ' . $studentDetails['Curriculum']['minimum_credit_points'] . ' ' . $studentDetails['Curriculum']['type_credit'] . ' set on ' . $studentDetails['Curriculum']['name'] . ' curriculum for graduation or you don\'t have the proper permission to add the student to the senate list. If you have the permission to manage the selected student record and you are certain that the student took all the required credits, please check for WRONG Course Equivalency Mappings in the student attached curriculum.'
                                            );
                                        }
                                    } else {
                                        if ($this->Session->read(
                                                'Auth.User'
                                            )['role_id'] == ROLE_REGISTRAR && $this->Session->read(
                                                'Auth.User'
                                            )['is_admin'] != 1) {
                                            $this->Flash->warning(
                                                'You don\'t have the proper permission to add student with (' . $studentDetails['Student']['studentnumber'] . ') ID to the senate list.'
                                            );
                                        } else {
                                            $this->Flash->info(
                                                'Seems like you\'re on College role, change your role to department role, logout and login back to add ' . $studentDetails['Student']['full_name'] . ' (' . $studentDetails['Student']['studentnumber'] . ') to the senate list.'
                                            );
                                        }
                                    }
                                }
                            }
                        }
                    } else {
                        $this->Flash->info(
                            (trim(
                                $this->request->data['SenateList']['studentnumber']
                            )) . ' couldn\'t be included in senate list. Either you did\'t used the correct Search filters or you don\'t have the proper permission to add the student to the senate list.'
                        );
                    }
                } else {
                    $this->Flash->info(
                        'No Student is found to add to Senate List by the selected search criteria which qualify the requirement for graduation. If you are certain that there are students who took all the required credits for graduation, please check for WRONG Course Equivalency Mappings in the student attached curriculum.'
                    );
                }
            }

            if (isset($studentDetails) && !empty($studentDetails['Student']['id']) && !empty($this->department_ids) && in_array(
                    $studentDetails['Student']['department_id'],
                    $this->department_ids
                )) {
                $default_program_id = $this->request->data['SenateList']['program_id'] = $studentDetails['Student']['program_id'];
                $default_program_type_id = $this->request->data['SenateList']['program_type_id'] = $studentDetails['Student']['program_type_id'];
                $default_department_id = $this->request->data['SenateList']['department_id'] = $studentDetails['Student']['department_id'];
            }
        } else {
            if (!empty($department_id) && !empty($program_id)) {
                $students_for_senate_list = $this->SenateList->getListOfStudentsForSenateList(
                    $program_id,
                    $program_type_id,
                    $department_id
                );
                $default_department_id = $department_id;
                $default_program_id = $program_id;
                $default_program_type_id = $program_type_id;

                if (empty($students_for_senate_list)) {
                    $this->Flash->info(
                        'No Student is found to add to Senate List by the selected search criteria which qualify the requirement for graduation.'
                    );
                }
            }
        }

        //debug($students_for_senate_list);
        if (!empty($this->request->data) && !empty($this->request->data['addStudentToSenateList'])) {
            //debug($this->request->data);
            if (trim($this->request->data['SenateList']['minute_number']) == "") {
                $this->Flash->error('Please provide a Minute Number.');
            } else {
                $senate_list = array();
                debug($this->request->data);
                foreach ($this->request->data['Student'] as $key => $student) {
                    if ($student['include_senate'] == 1) {
                        $sl_count = $this->SenateList->find(
                            'count',
                            array('conditions' => array('SenateList.student_id' => $student['id']))
                        );
                        if ($sl_count == 0) {
                            $sl_index = count($senate_list);
                            $senate_list[$sl_index]['student_id'] = $student['id'];
                            $senate_list[$sl_index]['minute_number'] = trim(
                                $this->request->data['SenateList']['minute_number']
                            );
                            $senate_list[$sl_index]['approved_date'] = $this->request->data['SenateList']['approved_date']['year'] . '-' . $this->request->data['SenateList']['approved_date']['month'] . '-' . $this->request->data['SenateList']['approved_date']['day'];
                        }
                    }
                }

                if (empty($senate_list)) {
                    $this->Flash->error(
                        'You are required to select at least one student to be included in the senate list.'
                    );
                } else {
                    $x['SenateList'] = $senate_list;
                    if ($this->SenateList->saveAll($x['SenateList'])) {
                        $this->Flash->success(
                            count(
                                $senate_list
                            ) . ' students are included in the senate list. After senate approval, you can add those students to the graduate list.'
                        );
                        //return $this->redirect(array('action' => 'add', $this->request->data['SenateList']['department_id'], $this->request->data['SenateList']['program_id'], $this->request->data['SenateList']['program_type_id']));
                        $this->request->data = null;
                        return $this->redirect(array('action' => 'add'));
                    } else {
                        $this->Flash->error(
                            'The system is unable to include the selected students to the senate list. Please try again.'
                        );
                    }
                }
            }
        }

        if (isset($this->request->data['SenateList']) && isset($this->request->data['viewPDF'])) {
            //debug($this->request->data);
            $student_ids = array();
            $certificate_template = array();

            if (!empty($this->request->data['Student'])) {
                foreach ($this->request->data['Student'] as $key => $student) {
                    if (isset($student['include_senate']) && $student['include_senate'] == 1) {
                        $student_ids[] = $student['id'];
                    }
                }
            }

            $students_for_senate_list = array();

            if (!empty($student_ids)) {
                $students_for_senate_list = $this->SenateList->getListOfStudentsForSenateListGivenId($student_ids);

                $defaultacademicyear = $this->AcademicYear->current_academicyear();
                $ethiopicYear = $e_year = $this->EthiopicDateTime->GetEthiopicYear(date('j'), date('n'), date('Y'));
                $this->set(compact('students_for_senate_list', 'defaultacademicyear', 'ethiopicYear'));
                $this->layout = 'pdf';
                $this->render('senate_list_pdf');
            } else {
                $this->Flash->error(__('No Students are selected.'));
            }
        }

        $defaultacademicyear = $current_acy = $this->AcademicYear->current_academicyear();
        $admission_years = $this->AcademicYear->academicYearInArray(
            ((explode('/', $current_acy)[0]) - 10),
            (explode('/', $current_acy)[0])
        );

        $college_shortname = 'AMiT';

        if (!empty($department_id) || !empty($this->department_ids)) {
            if (isset($students_for_senate_list) && !empty($students_for_senate_list)) {
                $dptID = (!empty($default_department_id) ? $default_department_id : $department_id);
            } else {
                $dptID = array_rand(array_values($this->department_ids));
                if (empty($dptID)) {
                    $dptID = $department_id;
                }
            }

            $deptCollID = $this->SenateList->Student->Department->field('college_id', array('Department.id' => $dptID));

            if (!empty($deptCollID)) {
                $college_shortname = $this->SenateList->Student->College->field(
                    'shortname',
                    array('College.id' => $deptCollID)
                );
            }
        }

        //debug($admission_years);

        $this->set(
            compact(
                'programs',
                'program_types',
                'departments',
                'defaultacademicyear',
                'department_combo_id',
                'students_for_senate_list',
                'default_department_id',
                'default_program_id',
                'default_program_type_id',
                'admission_years',
                'college_shortname'
            )
        );
    }

    public function delete($id = null)
    {

        $this->SenateList->id = $id;

        if (!$id || !$this->SenateList->exists($id)) {
            $this->Flash->error(__('Invalid Senate List ID.'));
            return $this->redirect(array('action' => 'index'));
        }

        $senate_detail = $this->SenateList->find(
            'first',
            array('conditions' => array('SenateList.id' => $id), 'contain' => array('Student'))
        );

        if (!in_array($senate_detail['Student']['department_id'], $this->department_ids)) {
            $this->Flash->error('You do not have privilege to manage the selected student records.');
            return $this->redirect(array('action' => 'index'));
        }

        $graduate_count = $this->SenateList->Student->GraduateList->find(
            'count',
            array('conditions' => array('GraduateList.student_id' => $senate_detail['Student']['id']))
        );

        if ($graduate_count > 0) {
            $this->Flash->error(
                $senate_detail['Student']['full_name'] . ' is on graduate list and can not be deleted.'
            );
            return $this->redirect(array('action' => 'index'));
        } else {
            if ($this->SenateList->delete($id)) {
                $this->Flash->success(
                    $senate_detail['Student']['full_name'] . ' is successfully removed from the senate list'
                );
                return $this->redirect(array('action' => 'index'));
            }
        }

        $this->Flash->error(
            $senate_detail['Student']['full_name'] . ' is not removed from the senate list. Please try again.'
        );
        return $this->redirect(array('action' => 'index'));
    }


    public function generate_pdf()
    {

        $options = array();
        $sort_by = array('full_name' => 'ASC');
        $excludeMajor = 1;

        if ($this->Session->check('generate_pdf_options')) {
            $options = $this->Session->read('generate_pdf_options');
            $sort_by = $this->Session->read('generate_pdf_sort_by');
            $excludeMajor = $this->Session->read('generate_pdf_exclude_major');
        }

        // debug($options);
        // debug($sort_by);

        $students_for_senate_list_pdf = array();
        $senateLists = array();

        if (!empty($options)) {
            $senateLists = $students_for_senate_list = $this->SenateList->find('all', array(
                'conditions' => $options['conditions'],
                'contain' => array(
                    'Student' => array(
                        'order' => array(
                            'Student.college_id' => 'ASC',
                            'Student.department_id' => 'ASC',
                            'Student.program_id' => 'ASC',
                            'Student.program_type_id' => 'ASC',
                            'Student.first_name' => 'ASC',
                            'Student.middle_name' => 'ASC',
                            'Student.last_name' => 'ASC'
                        ),
                        'Department',
                        'Curriculum',
                        'ProgramType',
                        'CourseDrop' => array(
                            'CourseRegistration' => array(
                                'PublishedCourse' => array('Course')
                            ),
                            'order' => array(
                                'CourseDrop.academic_year' => 'ASC',
                                'CourseDrop.semester' => 'ASC',
                                'CourseDrop.id' => 'ASC'
                            )
                        ),
                        'CourseAdd' => array(
                            'PublishedCourse' => array(
                                'Course' => array('GradeType')
                            ),
                            'order' => array(
                                'CourseAdd.academic_year' => 'ASC',
                                'CourseAdd.semester' => 'ASC',
                                'CourseAdd.id' => 'ASC'
                            )
                        ),
                        'CourseRegistration' => array(
                            'PublishedCourse' => array(
                                'Course' => array('GradeType')
                            ),
                            'order' => array(
                                'CourseRegistration.academic_year' => 'ASC',
                                'CourseRegistration.semester' => 'ASC',
                                'CourseRegistration.id' => 'ASC'
                            )
                        ),
                        'Program',
                        'StudentExamStatus' => array(
                            //'order' => array('StudentExamStatus.created' => 'DESC')
                            'order' => array(
                                'StudentExamStatus.academic_year' => 'DESC',
                                'StudentExamStatus.semester' => 'DESC',
                                'StudentExamStatus.created' => 'DESC'
                            ),
                        ),
                        //'order' => $sort_by
                    )
                ),
                'order' => array('SenateList.approved_date' => 'DESC'),
            ));

            if (!empty($students_for_senate_list)) {
                foreach ($students_for_senate_list as $k => $v) {
                    // $v['Student']['ExemptedCredit'] = $this->SenateList->Student->CourseExemption->getStudentCourseExemptionCredit($v['Student']['id']);
                    // $v['Student']['TransferedCredit'] = $this->SenateList->Student->DepartmentTransfer->getTransferedCourseCredit($v['Student']['id']);

                    $v['Student']['ExemptedCredit'] = $this->SenateList->Student->DepartmentTransfer->getTransferedCourseCredit(
                        $v['Student']['id']
                    );
                    $v['Student']['TransferedCredit'] = $this->SenateList->Student->CourseExemption->getStudentCourseExemptionCredit(
                        $v['Student']['id']
                    );

                    $v['Student']['CourseDroppedCredit'] = $this->SenateList->Student->CourseDrop->droppedCreditSum(
                        $v['Student']['id']
                    );

                    if ($v['Student']['program_id'] == PROGRAM_POST_GRADUATE || $v['Student']['program_id'] == PROGRAM_PhD) {
                        $v['Student']['ThesisResult'] = ClassRegistry::init('ExamGrade')->getApprovedThesisGrade(
                            $v['Student']['id']
                        );
                    } else {
                        if ($v['Student']['program_id'] == PROGRAM_UNDEGRADUATE) {
                            $v['Student']['ExitExamGrade'] = ClassRegistry::init('ExamGrade')->getApprovedExitExamGrade(
                                $v['Student']['id']
                            );
                        }
                    }

                    $students_for_senate_list_pdf[$v['Student']['Program']['name'] . '~' . $v['Student']['ProgramType']['name'] . '~' . $v['Student']['Department']['name'] . '~' . $v['Student']['Curriculum']['name'] . '~' . $v['Student']['Curriculum']['minimum_credit_points'] . '~' . $v['Student']['Curriculum']['amharic_degree_nomenclature'] . '~' . $v['Student']['Curriculum']['specialization_amharic_degree_nomenclature'] . '~' . $v['Student']['Curriculum']['english_degree_nomenclature'] . '~' . $v['Student']['Curriculum']['specialization_english_degree_nomenclature'] . '~' . $v['Student']['Curriculum']['type_credit']][] = $v;
                }
            }


            //debug(count($students_for_senate_list_pdf));
            //isset($students_for_senate_list_pdf) ? debug($students_for_senate_list_pdf[0]) : '';

            $defaultacademicyear = $this->AcademicYear->current_academicyear();
            $ethiopicYear = $e_year = $this->EthiopicDateTime->GetEthiopicYear(date('j'), date('n'), date('Y'));

            $this->set(
                compact(
                    'students_for_senate_list_pdf',
                    'senateLists',
                    'defaultacademicyear',
                    'excludeMajor',
                    'ethiopicYear'
                )
            );
            $this->response->type('application/pdf');
            $this->layout = '/pdf/default';
            $this->render('senate_list_masspage_pdf');
        } else {
            $this->Flash->error('No Search Criteria is Set or no students found!');
            return $this->redirect(array('action' => 'index'));
        }
    }
}
