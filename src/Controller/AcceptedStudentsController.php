<?php

namespace App\Controller;


use Cake\Core\Configure;
use Cake\Event\Event;


class AcceptedStudentsController extends AppController
{

    public $name = 'AcceptedStudents';
    public $paginate = array();
    public $menuOptions = array(
        'parent' => 'placement',
        'exclude' => array(
            'search',
            'print_autoplaced_pdf',
            'export_autoplaced_xls',
            'download',
            'print_students_number_pdf',
            'export_students_number_xls',
            'download_csv',
            'download',
            'count_result'
        ),
        'alias' => array(
            'index' => 'List Accepted Students',
            'add' => 'Add Accepted Student',
            'generate' => 'Generate Student ID Number',
            'import_newly_students' => 'Import Accepted Students',
            'direct_placement' => 'Direct Department Placement',
            'auto_placement' => 'Auto Department Placement',
            'cancel_auto_placement' => 'Cancel Auto Placement',
            'auto_placement_approve_college' => 'Approve Auto Placement/View',
            'export_print_students_number' => 'Export Student IDs',
            'approve_auto_placement' => 'Approve Auto Placed Students',
            'print_student_identification' => 'Print Student IDs',
            'deattach_curriculum' => 'Detach Curriculum',
            'place_to_campus' => 'Place Students to Campus',
            'view_campus_assignment' => 'View Campus Assignment',
            'place_student_to_college_for_section' => 'Place Students to College For Section',
            'transfer_campus' => 'Transfer Student Campus',
            'move_readmitted_to_freshman' => 'Move Readmitted to Freshman'
        )
    );


    public function initialize()
    {

        parent::initialize();
        $this->loadComponent('EthiopicDateTime');

        $this->loadComponent('Paginator');
        $this->loadComponent('AcademicYear');
        $this->viewBuilder()->setHelpers(['Xls', 'Csv']);
    }

    public function beforeFilter(Event $event)
    {

        parent::beforeFilter($event);
        $this->Auth->allow(
            'download',
            'print_students_number_pdf',
            'export_students_number_xls',
            'count_result',
            'search',
            'view_campus_assignment',
            'download_csv'
        );
    }

    public function beforeRender(Event $event)
    {

        parent::beforeRender($event);
        //$acyear_array_data = $this->AcademicYear->acyear_array();

        $current_academicyear = $defaultacademicyear = $this->AcademicYear->currentAcademicYear();

        $acyear_array_data = $this->AcademicYear->academicYearInArray(
            APPLICATION_START_YEAR,
            (explode('/', $current_academicyear)[0]) + 1
        );
        $acYearMinuSeparated = $this->AcademicYear->acYearMinuSeparated(
            APPLICATION_START_YEAR,
            (explode('/', $current_academicyear)[0]) + 1
        );

        $defaultacademicyearMinusSeparted = str_replace('/', '-', $defaultacademicyear);

        // $this->set('defaultacademicyear', $defaultacademicyear);
        // $this->set('defaultacademicyearMinusSeparted', $defaultacademicyearMinusSeparted);

        $programs = $this->AcceptedStudent->Program->find(
            'list',
            array('conditions' => array('Program.id' => $this->program_ids, 'Program.active' => 1))
        );
        $program_types = $programTypes = $this->AcceptedStudent->ProgramType->find(
            'list',
            array('conditions' => array('ProgramType.id' => $this->program_type_ids, 'ProgramType.active' => 1))
        );


        $this->set(
            compact(
                'acyear_array_data',
                'acYearMinuSeparated',
                'defaultacademicyear',
                'program_types',
                'programs',
                'programTypes',
                'defaultacademicyearMinusSeparted',
                'yearLevels'
            )
        );

        if (isset($this->request->data['User']['password'])) {
            unset($this->request->data['User']['password']);
        }
    }

    public function search()
    {

        $this->__init_search_index();

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

    public function __init_clear_session_filters()
    {

        if ($this->Session->check('search_data_index')) {
            $this->Session->delete('search_data_index');
        }
    }


    public function index()
    {

        $limit = 100;
        $name = '';
        $selected_academic_year = '';
        $page = 1;
        $sort = 'AcceptedStudent.created';
        $direction = 'desc';

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
                    $this->passedArgs['Search.name']
                );
            }

            if (!empty($this->passedArgs['Search.department_id'])) {
                $this->request->data['Search']['department_id'] = $this->passedArgs['Search.department_id'];
            }

            if (!empty($this->passedArgs['Search.college_id'])) {
                $this->request->data['Search']['college_id'] = $this->passedArgs['Search.college_id'];
            }

            if (!empty($this->passedArgs['Search.academicyear'])) {
                $this->request->data['Search']['academicyear'] = $selected_academic_year = str_replace(
                    '-',
                    '/',
                    $this->passedArgs['Search.academicyear']
                );
            }

            if (isset($this->passedArgs['Search.admitted'])) {
                $this->request->data['Search']['admitted'] = $this->passedArgs['Search.admitted'];
            }

            if (isset($this->passedArgs['Search.program_id'])) {
                $this->request->data['Search']['program_id'] = $this->passedArgs['Search.program_id'];
            }

            if (isset($this->passedArgs['Search.program_type_id'])) {
                $this->request->data['Search']['program_type_id'] = $this->passedArgs['Search.program_type_id'];
            }


            ////////////////////

            if (isset($this->passedArgs['page'])) {
                $page = $this->request->data['Search']['page'] = $this->passedArgs['page'];
            }

            if (isset($this->passedArgs['sort'])) {
                $sort = $this->request->data['Search']['sort'] = $this->passedArgs['sort'];
            }

            if (isset($this->passedArgs['direction'])) {
                $direction = $this->request->data['Search']['direction'] = $this->passedArgs['direction'];
            }

            ////////////////////

            $this->__init_search_index();
            //$this->request->data['search'] = true;
        }

        if (isset($data) && !empty($data['Search'])) {
            $this->request->data['Search'] = $data['Search'];
            $this->__init_search_index();
        }

        if (isset($this->request->data['search'])) {
            unset($this->passedArgs);
            $this->__init_search_index();
            $this->__init_clear_session_filters();
        }

        //$this->__init_search_index();

        if (/* isset($this->request->data) &&  */ !empty($this->request->data)) {
            debug($this->request->data);

            if (!empty($page) && !isset($this->request->data['search'])) {
                $this->request->data['Search']['page'] = $page;
            }

            if ($this->Session->read('Auth.User')['role_id'] == ROLE_DEPARTMENT) {
                $departments = $this->AcceptedStudent->Department->find(
                    'list',
                    array('conditions' => array('Department.id' => $this->department_id, 'Department.active' => 1))
                );
                $options['conditions'][] = array('AcceptedStudent.department_id' => $this->department_id);
                $this->request->data['Search']['department_id'] = $this->department_id;
            } else {
                if ($this->Session->read('Auth.User')['role_id'] == ROLE_COLLEGE) {
                    $departments = array();

                    if (!$this->onlyPre) {
                        $departments = $this->AcceptedStudent->Department->find(
                            'list',
                            array(
                                'conditions' => array(
                                    'Department.college_id' => $this->college_id,
                                    'Department.active' => 1
                                )
                            )
                        );
                    }

                    if (!empty($this->request->data['Search']['department_id'])) {
                        $options['conditions'][] = array('AcceptedStudent.department_id' => $this->request->data['Search']['department_id']);
                    } else {
                        $options['conditions'][] = array('AcceptedStudent.college_id' => $this->college_id);
                    }

                    $this->request->data['Search']['college_id'] = $this->college_id;
                } else {
                    if ($this->Session->read('Auth.User')['role_id'] == ROLE_REGISTRAR) {
                        if (!empty($this->department_ids)) {
                            $colleges = array();
                            $departments = $this->AcceptedStudent->Department->find(
                                'list',
                                array(
                                    'conditions' => array(
                                        'Department.id' => $this->department_ids,
                                        'Department.active' => 1
                                    )
                                )
                            );

                            if (!empty($this->request->data['Search']['department_id'])) {
                                $options['conditions'][] = array('AcceptedStudent.department_id' => $this->request->data['Search']['department_id']);
                            } else {
                                $options['conditions'][] = array("AcceptedStudent.department_id" => $this->department_ids);
                            }
                        } else {
                            if (!empty($this->college_ids)) {
                                $departments = array();
                                $colleges = $this->AcceptedStudent->College->find(
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
                                        'AcceptedStudent.college_id' => $this->request->data['Search']['college_id'],
                                        'AcceptedStudent.department_id IS NULL'
                                    );
                                } else {
                                    $options['conditions'][] = array(
                                        "AcceptedStudent.college_id" => $this->college_ids,
                                        'AcceptedStudent.department_id IS NULL'
                                    );
                                }
                            }
                        }
                    } else {
                        if ($this->Session->read('Auth.User')['role_id'] == ROLE_STUDENT) {
                            unset($this->passedArgs);
                            unset($this->request->data);
                            return $this->redirect(array('action' => 'index'));
                        } else {
                            $departments = $this->AcceptedStudent->Department->find(
                                'list',
                                array('conditions' => array('Department.active' => 1))
                            );
                            $colleges = $this->AcceptedStudent->College->find(
                                'list',
                                array('conditions' => array('College.active' => 1))
                            );
                            if (!empty($this->request->data['Search']['department_id'])) {
                                $options['conditions'][] = array("AcceptedStudent.department_id" => $this->request->data['Search']['department_id']);
                            } else {
                                if (empty($this->request->data['Search']['department_id']) && !empty($this->request->data['Search']['college_id'])) {
                                    $departments = $this->AcceptedStudent->Department->find(
                                        'list',
                                        array(
                                            'conditions' => array(
                                                'Department.college_id' => $this->request->data['Search']['college_id'],
                                                'Department.active' => 1
                                            )
                                        )
                                    );
                                    $options['conditions'][] = array('AcceptedStudent.college_id' => $this->request->data['Search']['college_id']);
                                } else {
                                    if (!empty($colleges) && !empty($departments)) {
                                        $options['conditions'][] = array(
                                            'OR' => array(
                                                'AcceptedStudent.college_id' => array_keys($colleges),
                                                'AcceptedStudent.department_id' => array_keys($departments)
                                            )
                                        );
                                    } else {
                                        if (!empty($departments)) {
                                            $options['conditions'][] = array(
                                                'AcceptedStudent.department_id' => array_keys(
                                                    $departments
                                                )
                                            );
                                        } else {
                                            if (!empty($colleges)) {
                                                $options['conditions'][] = array(
                                                    'AcceptedStudent.college_id' => array_keys(
                                                        $colleges
                                                    )
                                                );
                                            }
                                        }
                                    }
                                }
                            }
                        }
                    }
                }
            }

            if (!empty($selected_academic_year)) {
                //debug($selected_academic_year);
                $options['conditions'][] = array('AcceptedStudent.academicyear' => $selected_academic_year);
            }

            if (!empty($this->request->data['Search']['program_id'])) {
                $options['conditions'][] = array('AcceptedStudent.program_id' => $this->request->data['Search']['program_id']);
            } else {
                if (empty($this->request->data['Search']['program_id']) && $this->Session->read(
                        'Auth.User'
                    )['role_id'] == ROLE_REGISTRAR) {
                    $options['conditions'][] = array('AcceptedStudent.program_id' => $this->program_ids);
                }
            }

            if (!empty($this->request->data['Search']['program_type_id'])) {
                $options['conditions'][] = array('AcceptedStudent.program_type_id' => $this->request->data['Search']['program_type_id']);
            } else {
                if (empty($this->request->data['Search']['program_type_id']) && $this->Session->read(
                        'Auth.User'
                    )['role_id'] == ROLE_REGISTRAR) {
                    $options['conditions'][] = array('AcceptedStudent.program_type_id' => $this->program_type_ids);
                }
            }

            if (isset($name) && !empty($name)) {
                $options['conditions'][] = array(
                    'OR' => array(
                        'AcceptedStudent.first_name LIKE ' => '%' . $name . '%',
                        'AcceptedStudent.middle_name LIKE ' => '%' . $name . '%',
                        'AcceptedStudent.last_name LIKE ' => '%' . $name . '%',
                        'AcceptedStudent.studentnumber LIKE ' => '%' . $name . '%',
                    )
                );
            }

            if (isset($this->request->data['Search']['college_id']) && !empty($this->request->data['Search']['college_id']) && $this->Session->read(
                    'Auth.User'
                )['role_id'] != ROLE_REGISTRAR) {
                $departments = $this->AcceptedStudent->Department->find(
                    'list',
                    array(
                        'conditions' => array(
                            'Department.college_id' => $this->request->data['Search']['college_id'],
                            'Department.active' => 1
                        )
                    )
                );
            }

            if (isset($this->request->data['Search']['admitted']) && ($this->request->data['Search']['admitted'] == '1' || $this->request->data['Search']['admitted'] == 1)) {
                $options['conditions'][] = array('Student.id IS NULL');
            } else {
                if (isset($this->request->data['Search']['admitted']) && ($this->request->data['Search']['admitted'] == '2' || $this->request->data['Search']['admitted'] == 2)) {
                    $options['conditions'][] = array('Student.id IS NOT NULL');
                }
            }
        } else {
            if ($this->Session->read('Auth.User')['role_id'] == ROLE_COLLEGE) {
                $departments = array();
                if (!$this->onlyPre) {
                    $departments = $this->AcceptedStudent->Department->find(
                        'list',
                        array(
                            'conditions' => array(
                                'Department.college_id' => $this->college_id,
                                'Department.active' => 1
                            )
                        )
                    );
                }
                $options['conditions'][] = array('AcceptedStudent.college_id' => $this->college_id);
                $this->request->data['Search']['college_id'] = $this->college_id;
            } elseif ($this->Session->read('Auth.User')['role_id'] == ROLE_DEPARTMENT) {
                $departments = $this->AcceptedStudent->Department->find(
                    'list',
                    array('conditions' => array('Department.id' => $this->department_id))
                );
                $options['conditions'][] = array('AcceptedStudent.department_id' => $this->department_id);
                $this->request->data['Search']['department_id'] = $this->department_id;
            } elseif ($this->Session->read('Auth.User')['role_id'] == ROLE_REGISTRAR) {
                if (!empty($this->department_ids)) {
                    $colleges = array();
                    $departments = $this->AcceptedStudent->Department->find(
                        'list',
                        array('conditions' => array('Department.id' => $this->department_ids, 'Department.active' => 1))
                    );
                    if (!empty($departments)) {
                        $options['conditions'][] = array(
                            'AcceptedStudent.department_id' => $this->department_ids,
                            'AcceptedStudent.program_id' => $this->program_ids,
                            'AcceptedStudent.program_type_id' => $this->program_type_ids
                        );
                    }
                } elseif (!empty($this->college_ids)) {
                    $departments = array();
                    $colleges = $this->AcceptedStudent->College->find(
                        'list',
                        array('conditions' => array('College.id' => $this->college_ids, 'College.active' => 1))
                    );

                    if (!empty($colleges)) {
                        $options['conditions'][] = array(
                            'AcceptedStudent.college_id' => array_keys($colleges),
                            'AcceptedStudent.department_id IS NULL',
                            'AcceptedStudent.program_id' => $this->program_ids,
                            'AcceptedStudent.program_type_id' => $this->program_type_ids
                        );
                    }
                }
            } else {
                if ($this->Session->read('Auth.User')['role_id'] == ROLE_STUDENT) {
                    $options['conditions'][] = 'AcceptedStudent.id in (select accepted_student_id from students where id=' . $this->student_id . ')';
                } else {
                    $departments = $this->AcceptedStudent->Department->find(
                        'list',
                        array('conditions' => array('Department.active' => 1))
                    );
                    $colleges = $this->AcceptedStudent->College->find(
                        'list',
                        array('conditions' => array('College.active' => 1))
                    );

                    if (!empty($colleges) && !empty($departments)) {
                        $options['conditions'][] = array(
                            'OR' => array(
                                'AcceptedStudent.department_id' => array_keys($departments),
                                'AcceptedStudent.college_id' => array_keys($colleges)
                            )
                        );
                    } else {
                        if (!empty($departments)) {
                            $options['conditions'][] = array(
                                'AcceptedStudent.department_id' => array_keys(
                                    $departments
                                )
                            );
                        } else {
                            if (!empty($colleges)) {
                                $options['conditions'][] = array('AcceptedStudent.college_id' => array_keys($colleges));
                            }
                        }
                    }
                }
            }

            if (!empty($options['conditions'])) {
                $options['conditions'][] = array('Student.id IS NOT NULL');
            }
        }

        //debug($options['conditions']);

        if (!empty($options['conditions'])) {
            $this->Paginator->settings = array(
                'conditions' => $options['conditions'],
                'contain' => array(
                    'Department' => array(
                        'fields' => array(
                            'Department.id',
                            'Department.name',
                            'Department.shortname',
                            'Department.college_id',
                            'Department.institution_code'
                        )
                    ),
                    'College' => array(
                        'fields' => array(
                            'College.id',
                            'College.name',
                            'College.shortname',
                            'College.institution_code',
                            'College.campus_id',
                        ),
                        'Campus' => array(
                            'id',
                            'name',
                            'campus_code'
                        )
                    ),
                    'Program' => array(
                        'fields' => array(
                            'Program.id',
                            'Program.name',
                            'Program.shortname',
                        )
                    ),
                    'Student' => array(
                        'fields' => array(
                            'Student.id'
                        )
                    ),
                    'ProgramType' => array(
                        'fields' => array(
                            'ProgramType.id',
                            'ProgramType.name',
                            'ProgramType.shortname',
                        )
                    ),
                ),
                'order' => array($sort => $direction),
                'limit' => (!empty($limit) ? $limit : 100),
                'maxLimit' => 5000,
                'recursive' => -1,
                'page' => $page
            );

            //$acceptedStudents = $this->paginate($options['conditions']);

            try {
                $acceptedStudents = $this->Paginator->paginate($this->modelClass);
                $this->set(compact('acceptedStudents'));
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
            $acceptedStudents = array();
            $this->set(compact('acceptedStudents'));
        }

        if (empty($acceptedStudents) && !empty($options['conditions'])) {
            $this->Flash->info('No Accepted Student is found with the given search criteria.');
            $turn_off_search = false;
        } else {
            $turn_off_search = false;
            //debug($acceptedStudents[0]);
        }

        $this->__init_search_index();

        if ($this->Session->read('student_not_deleted')) {
            $this->set('student_not_deleted', $this->Session->read('student_not_deleted'));
            $this->Session->delete('student_not_deleted');
        }

        $this->set(
            compact(
                'colleges',
                'departments',
                /*  'acceptedStudents', */
                'turn_off_search',
                'limit',
                'name',
                'selected_academic_year'
            )
        );
    }

    public function view($id = null)
    {

        if (!$id) {
            $this->Flash->error('Invalid Accepted Student.');
            return $this->redirect(array('action' => 'index'));
        }

        $this->AcceptedStudent->id = $id;
        $this->AcceptedStudent->resursive = 0;

        if (!$this->AcceptedStudent->exists()) {
            $this->Flash->error('Invalid Accepted Student.');
            return $this->redirect(array('action' => 'index'));
        }


        $this->request->data = $this->AcceptedStudent->read(null, $id);

        //debug($this->request->data);

        $this->request->data['AcceptedStudent']['sex'] = strtolower(
            trim($this->request->data['AcceptedStudent']['sex'])
        );

        $selected_department = $this->request->data['AcceptedStudent']['department_id'];
        $colleges = $this->AcceptedStudent->College->find('list', array('conditions' => array('College.active' => 1)));
        $departments = $this->AcceptedStudent->Department->find(
            'list',
            array(
                'conditions' => array(
                    'Department.college_id' => $this->request->data['AcceptedStudent']['college_id'],
                    'Department.active' => 1
                )
            )
        );

        $disabilities = $this->AcceptedStudent->Disability->find('list');
        $foreignPrograms = $this->AcceptedStudent->ForeignProgram->find('list');
        $placementTypes = $this->AcceptedStudent->PlacementType->find('list');
        $campuses = $this->AcceptedStudent->College->Campus->find('list');

        $countries = $this->AcceptedStudent->Student->Country->find('list');
        $regions = $this->AcceptedStudent->Region->find('list');
        $zones = $this->AcceptedStudent->Zone->find('list');
        $woredas = $this->AcceptedStudent->Woreda->find('list');
        $cities = $this->AcceptedStudent->Student->City->find('list');

        //$this->request->data['AcceptedStudent']['country_id'] = $this->AcceptedStudent->Region->field('country_id', array('Region.id' => $this->request->data['AcceptedStudent']['region_id']));
        $this->request->data['AcceptedStudent']['country_id'] = (!empty($this->request->data['Student']['country_id']) ? $this->request->data['Student']['country_id'] : null);
        $this->request->data['AcceptedStudent']['zone_id'] = (!empty($this->request->data['Student']['zone_id']) ? $this->request->data['Student']['zone_id'] : null);
        $this->request->data['AcceptedStudent']['woreda_id'] = (!empty($this->request->data['Student']['woreda_id']) ? $this->request->data['Student']['woreda_id'] : null);
        $this->request->data['AcceptedStudent']['city_id'] = (!empty($this->request->data['Student']['city_id']) ? $this->request->data['Student']['city_id'] : null);
        $this->request->data['AcceptedStudent']['student_national_id'] = (!empty($this->request->data['Student']['student_national_id']) ? $this->request->data['Student']['student_national_id'] : null);

        $currentacyeardata = $this->request->data['AcceptedStudent']['academicyear'];

        $this->set(
            compact(
                'departments',
                'programTypes',
                'colleges',
                'programs',
                'currentacyeardata',
                'selected_department',
                'disabilities',
                'foreignPrograms',
                'placementTypes',
                'campuses',
                'countries',
                'regions',
                'zones',
                'woredas',
                'cities'
            )
        );
    }

    public function add()
    {

        if (!empty($this->request->data)) {
            $this->AcceptedStudent->create();

            if (empty($this->request->data['AcceptedStudent']['disability'])) {
                $this->request->data['AcceptedStudent']['disability'] = "";
            }

            if (strcasecmp($this->request->data['AcceptedStudent']['department_id'], 'No department') === 0) {
                $this->request->data['AcceptedStudent']['department_id'] = null;
            } elseif ($this->request->data['AcceptedStudent']['department_id'] == "") {
                $this->request->data['AcceptedStudent']['department_id'] = null;
            } else {
                if (!empty($this->request->data['AcceptedStudent']['department_id'])) {
                    $this->request->data['AcceptedStudent']['placementtype'] = REGISTRAR_ASSIGNED;
                    $this->request->data['AcceptedStudent']['department_id'] = $this->request->data['AcceptedStudent']['department_id'];
                }
            }

            if ($this->AcceptedStudent->check_program_type($this->request->data)) {
                $check_everything_similar = $this->AcceptedStudent->find(
                    'count',
                    array('conditions' => $this->request->data['AcceptedStudent'], 'recursive' => -1)
                );

                if ($check_everything_similar == 0) {
                    if ($this->request->data['AcceptedStudent']['program_id'] != PROGRAM_UNDEGRADUATE || $this->request->data['AcceptedStudent']['program_type_id'] != PROGRAM_TYPE_REGULAR) {
                        if (empty($this->request->data['AcceptedStudent']['EHEECE_total_results'])) {
                            unset($this->request->data['AcceptedStudent']['EHEECE_total_results']);
                        }
                    }

                    if ($this->AcceptedStudent->save($this->request->data)) {
                        $this->Flash->success('The accepted student has been saved');
                        $this->request->data = null;
                    } else {
                        $this->Flash->error('The accepted student could not be saved. Please, try again.');
                    }
                } else {
                    $this->Flash->error('You have already entered same student data previously.');
                }
            } else {
                $error = $this->AcceptedStudent->invalidFields();
                if (isset($error['program'])) {
                    $this->Flash->error($error['program'][0]);
                }
            }
        }

        $colleges = $this->AcceptedStudent->College->find('list', array('conditions' => array('College.active' => 1)));

        if (!empty($this->request->data['AcceptedStudent']['college_id'])) {
            $departments = $this->AcceptedStudent->Department->find('list', array(
                'conditions' => array(
                    'Department.college_id' => $this->request->data['AcceptedStudent']['college_id'],
                    'Department.active' => 1
                )
            ));
        } else {
            $temp = array_keys($colleges);
            $collegeIds = $temp[0];
            $departments = $this->AcceptedStudent->Department->find('list', array(
                'conditions' => array(
                    'Department.college_id' => $collegeIds,
                    'Department.active' => 1
                )
            ));
        }

        $regions = $this->AcceptedStudent->Region->find('list');
        $disabilities = $this->AcceptedStudent->Disability->find('list');
        $foreignPrograms = $this->AcceptedStudent->ForeignProgram->find('list');
        $placementTypes = $this->AcceptedStudent->PlacementType->find('list');

        $this->set(
            compact(
                'departments',
                'programTypes',
                'colleges',
                'programs',
                'regions',
                'disabilities',
                'foreignPrograms',
                'placementTypes'
            )
        );
    }

    public function edit($id = null)
    {

        if (!$id && empty($this->request->data)) {
            $this->Flash->error('Invalid Accepted Student.');
            return $this->redirect(array('action' => 'index'));
        }

        $this->AcceptedStudent->id = $id;

        if (!$this->AcceptedStudent->exists()) {
            $this->Flash->error('Invalid Accepted Student.');
            return $this->redirect(array('action' => 'index'));
        }

        if (!($this->Session->read('Auth.User')['role_id'] == ROLE_REGISTRAR || $this->Session->read(
                'Auth.User'
            )['role_id'] == ROLE_COLLEGE)) {
            $this->Flash->error(
                __(
                    'You are not elgibile to edit the any student records. This incident will be reported to system administrators. Please don\'t try this again.'
                )
            );
            $this->redirect(array('action' => 'index'));
        }

        $check_elegibility_to_edit = 0;

        if (!empty($this->department_ids)) {
            $check_elegibility_to_edit = $this->AcceptedStudent->find(
                'count',
                array(
                    'conditions' => array(
                        'AcceptedStudent.department_id' => $this->department_ids,
                        'AcceptedStudent.id' => $id
                    )
                )
            );
        } else {
            if (!empty($this->college_ids)) {
                $check_elegibility_to_edit = $this->AcceptedStudent->find(
                    'count',
                    array(
                        'conditions' => array(
                            'AcceptedStudent.college_id' => $this->college_ids,
                            'AcceptedStudent.id' => $id
                        )
                    )
                );
            }
        }


        if ($check_elegibility_to_edit == 0) {
            $this->Flash->error(
                __(
                    'You are not elgibile to edit the selected student profile. This happens when you are trying to edit student\'s profile which you are not assigned to edit.'
                )
            );
            $this->redirect(array('action' => 'index'));
        }

        debug($this->request->data);

        if (!empty($this->request->data)) {
            if (empty($this->request->data['AcceptedStudent']['disability'])) {
                $this->request->data['AcceptedStudent']['disability'] = null;
            }

            $this->set($this->request->data);

            if (isset($this->request->data['updateAcceptedStudentDetail'])) {
                // introduce a function in Accepted student and students models to check studentnumber uniqueness and uncomment the following code.

                //$this->request->data['AcceptedStudent']['studentnumber'] = (strtoupper(trim($this->request->data['AcceptedStudent']['studentnumber'])));

                if ($this->AcceptedStudent->validates()) {
                    if ($this->AcceptedStudent->check_program_type($this->request->data, $this->role_id)) {
                        if (isset($this->request->data['AcceptedStudent']['department_id']) && $this->request->data['AcceptedStudent']['department_id']) {
                            $campus_id = $this->AcceptedStudent->College->field(
                                'campus_id',
                                array('College.id' => $this->request->data['AcceptedStudent']['college_id'])
                            );
                            $this->request->data['AcceptedStudent']['campus_id'] = $campus_id;
                            $this->request->data['AcceptedStudent']['original_college_id'] = $this->request->data['AcceptedStudent']['college_id'];
                        }

                        if ($this->AcceptedStudent->save($this->request->data)) {
                            $studentDetail = $this->AcceptedStudent->Student->find('first', array(
                                'conditions' => array('Student.accepted_student_id' => $this->AcceptedStudent->id),
                                'contain' => array('AcceptedStudent')
                            ));

                            if (!empty($studentDetail)) {
                                debug($studentDetail);

                                $this->AcceptedStudent->Student->id = $studentDetail['Student']['id'];

                                $updateMinorStudentFields['Student']['id'] = $studentDetail['Student']['id'];

                                //$updateMinorStudentFields['Student']['first_name'] = ucwords(strtolower(trim($this->request->data['AcceptedStudent']['first_name'])));
                                //$updateMinorStudentFields['Student']['middle_name'] = ucwords(strtolower(trim($this->request->data['AcceptedStudent']['middle_name'])));
                                //$updateMinorStudentFields['Student']['last_name'] = ucwords(strtolower(trim($this->request->data['AcceptedStudent']['last_name'])));

                                $updateMinorStudentFields['Student']['gender'] = strtolower(
                                    trim($this->request->data['AcceptedStudent']['sex'])
                                );

                                //$updateMinorStudentFields['Student']['department_id'] = $studentDetail['AcceptedStudent']['department_id'];
                                //$updateMinorStudentFields['Student']['college_id'] = $studentDetail['AcceptedStudent']['college_id'];

                                /* if (isset($studentDetail['Student']['department_id']) && $studentDetail['Student']['department_id']) {
									$updateMinorStudentFields['Student']['original_college_id'] = $studentDetail['AcceptedStudent']['college_id'];
								} */

                                //$updateMinorStudentFields['Student']['program_id'] = $studentDetail['AcceptedStudent']['program_id'];
                                //$updateMinorStudentFields['Student']['program_type_id'] = $studentDetail['AcceptedStudent']['program_type_id'];

                                $updateMinorStudentFields['Student']['high_school'] = ucwords(
                                    strtolower(trim($this->request->data['AcceptedStudent']['high_school']))
                                );
                                $updateMinorStudentFields['Student']['moeadmissionnumber'] = trim(
                                    $this->request->data['AcceptedStudent']['moeadmissionnumber']
                                );

                                $updateMinorStudentFields['Student']['academicyear'] = $studentDetail['AcceptedStudent']['academicyear'];

                                //$updateMinorStudentFields['Student']['admissionyear'] =  $this->AcademicYear->get_academicYearBegainingDate($studentDetail['AcceptedStudent']['academicyear']);

                                if (isset($this->request->data['AcceptedStudent']['region_id']) && !empty($this->request->data['AcceptedStudent']['region_id']) && $this->request->data['AcceptedStudent']['region_id'] > 1) {
                                    if ((!empty($studentDetail['Student']['region_id']) && $studentDetail['Student']['region_id'] != $this->request->data['AcceptedStudent']['region_id']) || empty($studentDetail['Student']['region_id'])) {
                                        $updateMinorStudentFields['Student']['region_id'] = $this->request->data['AcceptedStudent']['region_id'];
                                    }
                                }

                                if (isset($this->request->data['AcceptedStudent']['zone_id']) && !empty($this->request->data['AcceptedStudent']['zone_id']) && $this->request->data['AcceptedStudent']['zone_id'] > 1) {
                                    if ((!empty($studentDetail['Student']['zone_id']) && $studentDetail['Student']['zone_id'] != $this->request->data['AcceptedStudent']['zone_id']) || empty($studentDetail['Student']['zone_id'])) {
                                        $updateMinorStudentFields['Student']['zone_id'] = $this->request->data['AcceptedStudent']['zone_id'];
                                    }
                                }

                                if (isset($this->request->data['AcceptedStudent']['woreda_id']) && !empty($this->request->data['AcceptedStudent']['woreda_id']) && $this->request->data['AcceptedStudent']['woreda_id'] > 1) {
                                    if ((!empty($studentDetail['Student']['woreda_id']) && $studentDetail['Student']['woreda_id'] != $this->request->data['AcceptedStudent']['woreda_id']) || empty($studentDetail['Student']['woreda_id'])) {
                                        $updateMinorStudentFields['Student']['woreda_id'] = $this->request->data['AcceptedStudent']['woreda_id'];
                                    }
                                }

                                if (isset($this->request->data['AcceptedStudent']['city_id']) && !empty($this->request->data['AcceptedStudent']['city_id']) && $this->request->data['AcceptedStudent']['city_id'] > 1) {
                                    if ((!empty($studentDetail['Student']['city_id']) && $studentDetail['Student']['city_id'] != $this->request->data['AcceptedStudent']['city_id']) || empty($studentDetail['Student']['city_id'])) {
                                        $updateMinorStudentFields['Student']['city_id'] = $this->request->data['AcceptedStudent']['city_id'];
                                    }
                                }

                                //$updateMinorStudentFields['Student']['studentnumber'] = $this->request->data['AcceptedStudent']['studentnumber'];

                                debug($updateMinorStudentFields);

                                if (debug(
                                    $this->AcceptedStudent->Student->saveAll(
                                        $updateMinorStudentFields,
                                        array('validate' => true)
                                    )
                                )) {
                                } else {
                                    $error = $this->AcceptedStudent->Student->invalidFields();
                                    debug($error);
                                }

                                //$admissionyear = $this->AcademicYear->get_academicYearBegainingDate($studentDetail['AcceptedStudent']['academicyear']);
                                //$this->AcceptedStudent->Student->saveField('admissionyear', $admissionyear);
                            }

                            $this->Flash->success('The accepted student data has been saved.');
                            $this->redirect(array('action' => 'index'));
                        } else {
                            $this->Flash->error('The accepted student data could not be saved. Please, try again.');
                        }
                    } else {
                        $error = $this->AcceptedStudent->invalidFields();

                        if (isset($error['program'])) {
                            $this->Flash->error($error['program'][0]);
                        }
                    }
                }
            }
        }

        if (empty($this->request->data)) {
            $this->request->data = $this->AcceptedStudent->read(null, $id);

            $this->request->data['AcceptedStudent']['sex'] = strtolower(
                trim($this->request->data['AcceptedStudent']['sex'])
            );
            //$this->request->data['AcceptedStudent']['studentnumber'] =  strtoupper(trim($this->request->data['AcceptedStudent']['studentnumber']));

            //$this->request->data['AcceptedStudent']['first_name'] = ucwords(strtolower(trim($this->request->data['AcceptedStudent']['first_name'])));
            //$this->request->data['AcceptedStudent']['middle_name'] = ucwords(strtolower(trim($this->request->data['AcceptedStudent']['middle_name'])));
            //$this->request->data['AcceptedStudent']['last_name'] = ucwords(strtolower(trim($this->request->data['AcceptedStudent']['last_name'])));

            $this->request->data['AcceptedStudent']['high_school'] = ucwords(
                strtolower(trim($this->request->data['AcceptedStudent']['high_school']))
            );
            $this->request->data['AcceptedStudent']['moeadmissionnumber'] = trim(
                $this->request->data['AcceptedStudent']['moeadmissionnumber']
            );
        }

        debug($this->request->data);

        $selected_department = $this->request->data['AcceptedStudent']['department_id'];

        if ($this->Session->read('Auth.User')['is_admin'] == 1 && $this->Session->read(
                'Auth.User'
            )['role_id'] == ROLE_REGISTRAR) {
            $colleges = $this->AcceptedStudent->College->find(
                'list',
                array('conditions' => array('College.active' => 1))
            );
            $departments = $this->AcceptedStudent->Department->find(
                'list',
                array('conditions' => array('Department.active' => 1))
            );
        } else {
            $departments = $this->AcceptedStudent->Department->find('list', array(
                'conditions' => array(
                    'OR' => array(
                        'Department.id' => (!empty($this->department_ids) ? $this->department_ids : $this->department_id),
                        'Department.college_id' => (!empty($this->college_ids) ? $this->college_ids : $this->college_id),
                    ),
                    'Department.active' => 1
                )
            ));

            if (!empty($departments)) {
                $college_ids = $this->AcceptedStudent->Department->find('list', array(
                    'conditions' => array(
                        'Department.id' => array_keys($departments)
                    ),
                    'fields' => array('Department.college_id', 'Department.college_id')
                ));

                $colleges = $this->AcceptedStudent->College->find('list', array(
                    'conditions' => array(
                        'College.id' => $college_ids,
                        'College.active' => 1
                    )
                ));
            } else {
                $colleges = $this->AcceptedStudent->College->find(
                    'list',
                    array('conditions' => array('College.active' => 1))
                );
            }
        }

        //$regions = $this->AcceptedStudent->Region->find('list');
        $disabilities = $this->AcceptedStudent->Disability->find('list');
        $foreignPrograms = $this->AcceptedStudent->ForeignProgram->find('list');
        $placementTypes = $this->AcceptedStudent->PlacementType->find('list');
        $campuses = ClassRegistry::init('Campus')->find('list');

        $currentacyeardata = $this->request->data['AcceptedStudent']['academicyear'];

        $isAdmittedAndHaveDepartment = $this->AcceptedStudent->find('first', array(
            'conditions' => array('AcceptedStudent.id' => $id),
            'contain' => array(
                'Student' => array(
                    'College',
                    'Department',
                    'CourseRegistration' => array(
                        'order' => 'CourseRegistration.id DESC'
                    )
                )
            )
        ));

        //debug($isAdmittedAndHaveDepartment);

        $studentDetail = $this->AcceptedStudent->find('first', array(
            'conditions' => array(
                'AcceptedStudent.id' => $id
            ),
            'contain' => array(
                'User',
                'Student',
                'Program',
                'ProgramType',
                'Department',
                'College',
                'Region',
                'Zone',
                'Woreda'
            )
        ));

        $foriegn_students_region_ids = $this->AcceptedStudent->Region->find(
            'list',
            array(
                'conditions' => array('Region.country_id <> ' => COUNTRY_ID_OF_ETHIOPIA),
                'fields' => array('Region.id', 'Region.id')
            )
        );

        debug($foriegn_students_region_ids);

        $regions = array();
        $zones = array();
        $woredas = array();
        $cities = array();

        $foriegn_student = 0;

        $country_id_of_region = COUNTRY_ID_OF_ETHIOPIA;

        $region_id_of_student = '';

        if (!empty($studentDetail['AcceptedStudent']['region_id']) || !empty($studentDetail['Student']['region_id'])) {
            $region_id_of_student = (!empty($studentDetail['AcceptedStudent']['region_id']) ? $studentDetail['AcceptedStudent']['region_id'] : (!empty($studentDetail['Student']['region_id']) ? $studentDetail['Student']['region_id'] : 0));

            $country_id_of_region = $this->AcceptedStudent->Region->field(
                'country_id',
                array('Region.id' => $region_id_of_student)
            );

            $countries = $this->AcceptedStudent->Student->Country->find(
                'list',
                array('conditions' => array('Country.id' => $country_id_of_region))
            );

            $regions = $this->AcceptedStudent->Region->find('list', array(
                'conditions' => array(
                    'Region.id' => $region_id_of_student,
                    'Region.country_id' => $country_id_of_region
                )
            ));

            $zones = $this->AcceptedStudent->Zone->find(
                'list',
                array('conditions' => array('Zone.region_id' => $region_id_of_student))
            );

            $city_zone_ids = $this->AcceptedStudent->Student->City->find('list', array(
                'conditions' => array(
                    'City.region_id' => $region_id_of_student
                ),
                'fields' => array('City.zone_id', 'City.zone_id')
            ));

            $woredas = $this->AcceptedStudent->Woreda->find('list', array(
                'conditions' => array(
                    'Woreda.zone_id' => (!empty($zones) ? array_keys(
                        $zones
                    ) : (!empty($city_zone_ids) ? $city_zone_ids : null)),
                )
            ));


            $cities = $this->AcceptedStudent->Student->City->find('list', array(
                'conditions' => array(
                    'OR' => array(
                        'City.id' => $studentDetail['Student']['city_id'],
                        'City.zone_id' => (!empty($zones) ? array_keys(
                            $zones
                        ) : (!empty($studentDetail['AcceptedStudent']['zone_id']) ? $studentDetail['AcceptedStudent']['zone_id'] : $studentDetail['Student']['zone_id'])),
                        'City.region_id' => $region_id_of_student,
                    )
                )
            ));
        } else {
            $countries = $this->AcceptedStudent->Student->Country->find('list');
            $regions = $this->AcceptedStudent->Region->find('list', array('conditions' => array('Region.active' => 1)));
            $zones = $this->AcceptedStudent->Zone->find('list', array('conditions' => array('Zone.active' => 1)));
            $woredas = $this->AcceptedStudent->Woreda->find('list', array('conditions' => array('Woreda.active' => 1)));
            $cities = $this->AcceptedStudent->Student->City->find(
                'list',
                array('conditions' => array('City.active' => 1))
            );
        }

        if (empty($regions)) {
            $regions = $this->AcceptedStudent->Region->find(
                'list',
                array('conditions' => array('Region.country_id' => $country_id_of_region))
            );
        }

        if (empty($zones)) {
            $zones = $this->AcceptedStudent->Zone->find('list');
        }

        if (empty($woredas)) {
            $woredas = $this->AcceptedStudent->Woreda->find('list');
        }

        if (empty($cities)) {
            if (!empty($region_id_of_student)) {
                $cities = $this->AcceptedStudent->Student->City->find(
                    'list',
                    array('conditions' => array('City.region_id' => $region_id_of_student))
                );
            } else {
                if (!empty($regions)) {
                    $cities = $this->AcceptedStudent->Student->City->find(
                        'list',
                        array('conditions' => array('City.region_id' => array_keys($regions)))
                    );
                } else {
                    $cities = $this->AcceptedStudent->Student->City->find('list');
                }
            }
        }

        if (!empty($foriegn_students_region_ids) && ((isset($studentDetail['AcceptedStudent']['region_id']) && !empty($studentDetail['AcceptedStudent']['region_id']) && in_array(
                        $studentDetail['AcceptedStudent']['region_id'],
                        $foriegn_students_region_ids
                    )) || (isset($studentDetail['Student']['region_id']) && !empty($studentDetail['Student']['region_id']) && in_array(
                        $studentDetail['Student']['region_id'],
                        $foriegn_students_region_ids
                    )))) {
            $foriegn_student = 1;
        }

        debug($countries);
        debug($regions);
        // debug($zones);
        // debug($woredas);
        // debug($cities);


        debug($foriegn_student);


        /* $regionsAll = $this->Student->Region->find('list', array('conditions' => array('Region.active' => 1, 'Region.country_id' => $country_id_of_region)));
		$zonesAll = $this->AcceptedStudent->Zone->find('list', array('conditions' => array('Zone.active' => 1)));
		$woredasAll = $this->AcceptedStudent->Woreda->find('list', array('conditions' => array('Woreda.active' => 1)));
		$citiesAll = $this->AcceptedStudent->Student->City->find('list', array('conditions' => array('City.active' => 1))); */

        $this->set(
            compact(
                'departments',
                'programTypes',
                'colleges',
                'programs',
                'currentacyeardata',
                'selected_department',
                'isAdmittedAndHaveDepartment',
                'disabilities',
                'foreignPrograms',
                'placementTypes',
                'campuses',
                'countries',
                'regions',
                'zones',
                'woredas',
                'cities',
                'studentDetail',
                /* 'regionsAll',
			'zonesAll',
			'woredasAll',
			'citiesAll', */
                'foriegn_student'
            )
        );
    }

    public function delete()
    {

        $student_not_deleted = array();
        $delete_count = 0;

        if (!empty($this->request->data['AcceptedStudent']['delete'])) {
            foreach ($this->request->data['AcceptedStudent']['delete'] as $id => $delete) {
                if ($delete == 1) {
                    $admitted = $this->AcceptedStudent->Student->find(
                        'first',
                        array('conditions' => array('Student.accepted_student_id' => $id), 'recursive' => -1)
                    );
                    //$admission_check = $this->AcceptedStudent->Student->checkAdmissionTransaction($admitted['Student']['id']);

                    if (count($admitted) || isset($admitted['Student']['id'])) {
                        $admission_check = $this->AcceptedStudent->Student->checkAdmissionTransaction(
                            $admitted['Student']['id']
                        );
                    } else {
                        $admission_check = 0;
                    }

                    $preference_check = $this->AcceptedStudent->Preference->find(
                        'count',
                        array('conditions' => array('Preference.accepted_student_id' => $id), 'recursive' => -1)
                    );

                    if ($admission_check == 0 && $preference_check == 0) {
                        if ($this->AcceptedStudent->delete($id)) {
                            $delete_count++;
                        }
                    } else {
                        $student_not_deleted[] = $id;
                    }
                }
            }
        }

        if (!empty($student_not_deleted)) {
            $this->Session->Write('student_not_deleted', $student_not_deleted);
        }

        if (count($this->request->data['AcceptedStudent']['delete']) == count($student_not_deleted)) {
            $this->Flash->error(
                'You can not delete the selected student(s), The students already have section assignment or course registrations or filled preference.'
            );
        } else {
            if (empty($student_not_deleted)) {
                if ($delete_count > 0) {
                    $this->Flash->success(
                        $delete_count . (($delete_count == 1) ? ' Accepted Student was' : ' Accepted Students were') . ' deleted.'
                    );
                    unset($this->request->data['AcceptedStudent']['delete']);
                    if (isset($this->request->data['AcceptedStudent']['select-all'])) {
                        unset($this->request->data['AcceptedStudent']['select-all']);
                    }
                } else {
                    $this->Flash->error('Please select atleast one student to delete');
                }
            } else {
                if ($delete_count > 0) {
                    $this->Flash->success(
                        $delete_count . (($delete_count == 1) ? ' Accepted Student was' : ' Accepted Students were') . ' deleted, but those red marked student coudn\'t be delete, already have section assignment or course registrations or filled preference.'
                    );
                    unset($this->request->data['AcceptedStudent']['delete']);
                    if (isset($this->request->data['AcceptedStudent']['select-all'])) {
                        unset($this->request->data['AcceptedStudent']['select-all']);
                    }
                }
            }

            if (!empty($student_not_deleted)) {
                $this->Flash->error(
                    count(
                        $student_not_deleted
                    ) . (($delete_count == 1) ? '  Accepted Student was' : '  Accepted Students were') . ' not deleted. They already have section assignment or course registrations or filled preference.'
                );
            }
        }

        $this->redirect(Router::url($this->referer(), true));
    }

    // Number  of accepted students categorized by results
    public function summery()
    {

        $thisyear = date('Y');
        $thismonth = date('m');
        $shortthisyear = substr($thisyear, 2, 2);

        if ($thismonth == "09" or $thismonth == "10" or $thismonth == "11" or $thismonth == "12") {
            $acyear = $thisyear . '/' . ($shortthisyear + 1);
        } else {
            $acyear = ($thisyear - 1) . '/' . $shortthisyear;
        }

        if ($this->Session->read('acyear')) {
            $this->Session->write('acyear', $acyear);
        } else {
            $this->Session->write('acyear', $acyear);
        }

        $total_selected_student = $this->AcceptedStudent->find(
            'count',
            array('conditions' => array("AcceptedStudent.academicyear LIKE" => "$acyear%"))
        );

        if ($result_critieria_data = $this->Session->read('result_critieria_data')) {
            $selected_student_result_category_count = array();

            if (!empty($result_critieria_data)) {
                foreach ($result_critieria_data as $key => $value) {
                    $selected_student_result_category_count[$value['PlacementsResultsCriteria']['name'] . '(' . $value['PlacementsResultsCriteria']['result_from'] . '-' . $value['PlacementsResultsCriteria']['result_to'] . ')'] = $this->AcceptedStudent->find(
                        'count',
                        array(
                            'conditions' => array(
                                "AcceptedStudent.academicyear LIKE" => "$acyear%",
                                "AcceptedStudent.EHEECE_total_results >=" => $value['PlacementsResultsCriteria']['result_from'],
                                "AcceptedStudent.EHEECE_total_results <=" => $value['PlacementsResultsCriteria']['result_to']
                            )
                        )
                    );
                    if ($this->Session->read('result_critieria_data')) {
                        $this->Session->delete('result_critieria_data');
                    }
                }
            }

            $this->Session->write('selected_student_result_category_count', $selected_student_result_category_count);
        }
    }

    public function generate($id = null)
    {

        //********* TO Filter students per academic year,College,Program, program_type and display
        $data = $this->AcceptedStudent->getidlessstudentsummery($this->AcademicYear->current_academicyear());
        $this->AcceptedStudent->recursive = 0;
        $acceptedStudents = null;
        $selectedsacdemicyear = null;
        $selected_program = null;
        $selected_program_type = null;
        $selected_college = null;

        $colleges = $this->AcceptedStudent->College->find('list', array('conditions' => array('College.active' => 1)));
        $selectedsacdemicyear = $this->AcademicYear->current_academicyear();
        $isbeforesearch = 1;

        $limit = 400;

        $this->set(
            compact('data', 'selectedsacdemicyear', 'programs', 'programTypes', 'colleges', 'isbeforesearch', 'limit')
        );

        if (!empty($this->request->data) && isset($this->request->data['search'])) {
            $isbeforesearch = 0;
            $selectedsacdemicyear = $this->request->data['AcceptedStudent']['academicyear'];
            $selected_college = $this->request->data['AcceptedStudent']['college_id'];
            $selected_program = $this->request->data['AcceptedStudent']['program_id'];
            $selected_program_type = $this->request->data['AcceptedStudent']['program_type_id'];

            if (!empty($this->request->data['AcceptedStudent']['limit'])) {
                $limit = $this->request->data['AcceptedStudent']['limit'];
            }

            $this->Paginator->settings = array(
                'conditions' => array(
                    "AcceptedStudent.academicyear" => $selectedsacdemicyear,
                    'AcceptedStudent.college_id' => $selected_college,
                    'AcceptedStudent.program_id' => $selected_program,
                    'AcceptedStudent.program_type_id' => $selected_program_type,
                    "OR" => array(
                        "AcceptedStudent.studentnumber is null",
                        "AcceptedStudent.studentnumber =''",
                        //"AcceptedStudent.studentnumber = 0",
                    ),
                ),
                'contain' => array(
                    'Department',
                    'College',
                    'ProgramType',
                    'Program'
                ),
                'order' => array(
                    'AcceptedStudent.first_name' => 'ASC',
                    'AcceptedStudent.middle_name' => 'ASC',
                    'AcceptedStudent.last_name' => 'ASC'
                ),
                'limit' => $limit,
                'maxLimit' => $limit,
                'recursive' => 0,
            );

            //$this->Paginator->settings = $this->paginate;
            debug($this->request->data);
            debug($this->Paginator->settings);
            $acceptedStudents = $this->Paginator->paginate('AcceptedStudent');
            debug(count($acceptedStudents));

            $this->set('show_list_generated', true);

            $this->set(
                compact(
                    'selectedsacdemicyear',
                    'acceptedStudents',
                    'selected_college',
                    'selected_program',
                    'selected_program_type',
                    'isbeforesearch'
                )
            );
        }

        // end of Filter

        if (isset($this->request->data['generateid'])) {
            $generate_count = 0;
            $Id_generation_failed_students_count = 0;
            $Id_generation_failed_students = null;
            //To check wheteher at least one check_box checked or not
            $check_count = 0;

            /* if (isset($this->request->data['AcceptedStudent']['generate']) && !empty($this->request->data['AcceptedStudent']['generate'])) {
				foreach ($this->request->data['AcceptedStudent']['generate'] as $value) {
					$check_count += $value;
				}
			} */

            $check_count = count($this->request->data['AcceptedStudent']['generate']);

            if (!empty($check_count)) {
                $generate_id_list = $this->request->data['AcceptedStudent']['generate'];

                /* foreach ($generate_id_list as $id => $generate) {
					if ($generate != 0) {
						$this->request->data = $this->AcceptedStudent->readAllById($id);
						break;
					}
				} */

                $generate_accepted_student_lists = $this->AcceptedStudent->find('list', array(
                    'conditions' => array(
                        'AcceptedStudent.id' => array_keys($generate_id_list)
                    ),
                    'order' => array(
                        'AcceptedStudent.first_name' => 'ASC',
                        'AcceptedStudent.middle_name' => 'ASC',
                        'AcceptedStudent.last_name' => 'ASC'
                    )
                ));

                $count = $this->AcceptedStudent->countId(
                    $this->request->data['College']['id'],
                    $this->request->data['AcceptedStudent']['academicyear'],
                    $this->request->data['AcceptedStudent']['program_id'],
                    $this->request->data['AcceptedStudent']['program_type_id']
                );

                $count = $count + 1;

                if (!empty($generate_accepted_student_lists)) {
                    foreach ($generate_accepted_student_lists as $id => $generate) {
                        $ccc = 0;
                        do {
                            if ($count >= 1 && $count <= 9) {
                                $count = '00' . $count;
                            } else {
                                if ($count >= 10 && $count <= 99) {
                                    $count = '0' . $count;
                                }
                            }

                            $loop_back = false;
                            //generate only for the selected students

                            if ($generate_id_list[$id] != 0) {
                                $this->request->data = $this->AcceptedStudent->readAllById($id);

                                if (!empty($this->request->data['AcceptedStudent']['college_id'])) {
                                    if (!empty($this->request->data['AcceptedStudent']['academicyear'])) {
                                        $programTypeShortName = $this->request->data['ProgramType']['shortname'];

                                        if (isset($this->request->data['College']['idnumber_prefix']) && !empty($this->request->data['College']['idnumber_prefix'])) {
                                            $CollageShortName = $this->request->data['College']['idnumber_prefix'];
                                        } else {
                                            $CollageShortName = $this->request->data['College']['shortname'];
                                        }

                                        $programShortName = $this->request->data['Program']['shortname'];

                                        //$acyear = $this->request->data['AcceptedStudent']['academicyear'];
                                        $GCyear = substr(
                                            ($this->request->data['AcceptedStudent']['academicyear']),
                                            0,
                                            4
                                        );
                                        $GCmonth = date('n');
                                        $GCday = date('j');

                                        debug($GCyear);

                                        if ($GCmonth >= 9) {
                                            $GCyear = $GCyear;
                                        } else {
                                            $GCyear = $GCyear + 1;
                                        }

                                        debug($GCyear);

                                        $ETyear = $this->EthiopicDateTime->GetEthiopicYear($GCday, $GCmonth, $GCyear);

                                        if ($GCmonth == 9) {
                                            $ETyear += 1;
                                        }
                                        //$shortAcyear = date('y',strtotime($ETyear));

                                        if ($GCmonth <= 8) {
                                            $ETshortAcyear = substr($ETyear, 2, 2);
                                            if ($ETshortAcyear < 10) {
                                                $ETshortAcyear = "0" . $ETshortAcyear;
                                            }
                                        } else {
                                            $ETshortAcyear = substr($ETyear, 2, 2);
                                        }

                                        //Count is total number of student that have student ID in specific collage and academic year
                                        if (strcasecmp($this->request->data['Program']['name'], 'Undergraduate') != 0) {
                                            if ((strcasecmp(
                                                        $this->request->data['Program']['name'],
                                                        'PhD'
                                                    ) == 0) || $this->request->data['AcceptedStudent']['program_id'] == PROGRAM_PhD) {
                                                $generatedStudentId = 'PhD/'/*  . $CollageShortName . '/'*/ . $count . '/' . $ETshortAcyear;
                                            } else {
                                                if (strcasecmp($this->request->data['Program']['name'], 'PGDT') == 0) {
                                                    $generatedStudentId = 'PGDT/'/*  . '/' */ . $count . '/' . $ETshortAcyear;
                                                } else {
                                                    if ((strcasecmp(
                                                                $this->request->data['Program']['name'],
                                                                'Post graduate'
                                                            ) == 0) || $this->request->data['AcceptedStudent']['program_id'] == PROGRAM_POST_GRADUATE) {
                                                        $generatedStudentId = 'P' . $programTypeShortName /* . $programShortName . */ . $CollageShortName . '/' . $count . '/' . $ETshortAcyear;
                                                    } else {
                                                        $generatedStudentId = $CollageShortName . '/' . $programShortName . '/' . $programTypeShortName . '/' . $count . '/' . $ETshortAcyear;
                                                    }
                                                }
                                            }
                                        } else {
                                            if ($this->request->data['AcceptedStudent']['academicyear'] >= '2019/20' && $this->request->data['AcceptedStudent']['program_type_id'] == 1) {
                                                $generatedStudentId = $CollageShortName . $programTypeShortName . '/' . $count . '/' . $ETshortAcyear;
                                            } else {
                                                $generatedStudentId = $programTypeShortName . $CollageShortName . '/' . $count . '/' . $ETshortAcyear;
                                            }
                                        }

                                        if (empty($this->request->data['AcceptedStudent']['studentnumber'])) {
                                            //Check whether generated id alreday in database or not
                                            $is_generatedStudentId_already_in_database = $this->AcceptedStudent->find(
                                                'count',
                                                array('conditions' => array('AcceptedStudent.studentnumber' => $generatedStudentId))
                                            );
                                            if ($is_generatedStudentId_already_in_database == 0) {
                                                $this->AcceptedStudent->id = $this->request->data['AcceptedStudent']['id'];
                                                $this->AcceptedStudent->saveField('studentnumber', $generatedStudentId);
                                                $generate_count++;
                                            } else {
                                                $loop_back = true;
                                            }
                                        }
                                    } else {
                                        $student_name = $this->AcceptedStudent->field(
                                            'AcceptedStudent.full_name',
                                            array('AcceptedStudent.id' => $id)
                                        );
                                        $Id_generation_failed_students_count++;
                                        $Id_generation_failed_students .= "<ol> For " . $student_name . " please provide academic year.</ol>";
                                    }
                                } else {
                                    $student_name = $this->AcceptedStudent->field(
                                        'AcceptedStudent.full_name',
                                        array('AcceptedStudent.id' => $id)
                                    );
                                    $Id_generation_failed_students_count++;
                                    $Id_generation_failed_students .= "<ol> For " . $student_name . " must be belongs to a collage.</ol>";
                                }
                            }
                            $count = $count + 1;
                        } while ($loop_back == true);
                    }
                }

                if ($Id_generation_failed_students_count == 0) {
                    $this->Flash->success(
                        $generate_count . ' Student ID' . (($generate_count == 1) ? ' was' : 's were') . ' generated',
                        'default',
                        array('class' => 'success-box success-message')
                    );
                } else {
                    $this->Session->setFlash(
                        '<span style="margin-right: 15px;"></span>' . $generate_count . ' Student ID' . (($generate_count == 1) ? ' was' : 's were') . ' generated successfully but For ' . $Id_generation_failed_students_count . ' Students the system failed to generate student Id. Please modifiy those students missing record based on the following lists <ul>' . $Id_generation_failed_students . '<ul>',
                        'default',
                        array('class' => 'info-box info-message')
                    );
                }

                unset($this->request->data['AcceptedStudent']);
            } else {
                $this->Flash->error("Please select atleast one student");
            }

            $this->set('show_list_generated', true);
            $this->redirect(array('action' => 'generate'));
        }
    }

    public function move_readmitted_to_freshman()
    {

        if ($this->role_id == ROLE_REGISTRAR || ROLE_REGISTRAR == $this->Session->read(
                'Auth.User'
            )['Role']['parent_id']) {
            $colleges = $this->AcceptedStudent->College->find(
                'list',
                array('conditions' => array('College.active' => 1), 'order' => array('College.name' => 'ASC'))
            );
        } else {
            if (isset($this->college_id) && !empty($this->college_id)) {
                $colleges = $this->AcceptedStudent->College->find(
                    'list',
                    array(
                        'conditions' => array('College.id' => $this->college_id, 'College.active' => 1),
                        'order' => array('College.name' => 'ASC')
                    )
                );
            } else {
                $colleges = $this->AcceptedStudent->College->find(
                    'list',
                    array('conditions' => array('College.active' => 1), 'order' => array('College.name' => 'ASC'))
                );
            }
        }

        $selectedsacdemicyear = $this->AcademicYear->current_academicyear();
        $isbeforesearch = 1;
        $readmittedAC = $this->AcademicYear->academicYearInArray(date('Y') - 1, date('Y') - 1);

        $this->set(
            compact(
                'data',
                'selectedsacdemicyear',
                'programs',
                'programTypes',
                'colleges',
                'readmittedAC',
                'isbeforesearch'
            )
        );

        // search readmitted students
        if (!empty($this->request->data) && isset($this->request->data['continue'])) {
            debug($this->request->data);

            $acceptedStudents = ClassRegistry::init('Readmission')->find('all', array(
                'conditions' => array(
                    "Readmission.registrar_approval" => 1,
                    "Readmission.academic_commision_approval" => 1,
                    "Readmission.academic_year" => $this->request->data['Search']['academicyear'],
                    "Student.college_id " => $this->request->data['Search']['college_id'],
                    "Student.program_id" => $this->request->data['Search']['program_id'],
                    "Student.program_type_id" => $this->request->data['Search']['program_type_id'],
                    'Student.first_name LIKE' => $this->request->data['Search']['name'] . '%',
                ),
                'contain' => array(
                    'Student' => array(
                        'AcceptedStudent',
                        'Department',
                        'College' => array('Campus')
                    )
                )
            ));

            $available_campuses = $this->AcceptedStudent->Campus->find('list');
            $selected_colleges = $this->AcceptedStudent->College->find(
                'list',
                array('conditions' => array('College.active' => 1), 'order' => array('College.name' => 'ASC'))
            );

            $this->set(
                compact(
                    'acceptedStudents',
                    'available_campuses',
                    'selected_colleges'
                )
            );
        }

        if (!empty($this->request->data) && isset($this->request->data['readmitted'])) {
            $collegem = $this->AcceptedStudent->College->find(
                'first',
                array(
                    'conditions' => array(
                        'College.campus_id' => $this->request->data['AcceptedStudent']['campus_id'],
                        'College.id' => $this->request->data['AcceptedStudent']['selected_college_id'],
                        'College.active' => 1
                    ),
                    'recursive' => -1
                )
            );
            debug($collegem);

            if (!empty($collegem)) {
                $selectedApproved = $this->request->data['AcceptedStudent']['approve'];
                $studentsDetails = array();

                if (!empty($selectedApproved)) {
                    foreach ($selectedApproved as $k => $v) {
                        if ($v == 1) {
                            $readmittedS = $this->AcceptedStudent->Student->find('first', array(
                                'conditions' => array('Student.accepted_student_id' => $k),
                                'contain' => array(
                                    'CourseRegistration',
                                    'CourseAdd',
                                    'AcceptedStudent'
                                )
                            ));
                            $studentsDetails[] = $readmittedS;
                        }
                    }
                }
                debug($studentsDetails);

                //check if selected student
                if (!empty($studentsDetails)) {
                    $readmittedCount = 0;
                    foreach ($studentsDetails as $std) {
                        $campusDetail = $this->AcceptedStudent->College->Campus->find(
                            'first',
                            array(
                                'conditions' => array('Campus.id' => $this->request->data['AcceptedStudent']['campus_id']),
                                'recursive' => -1
                            )
                        );
                        $changeCampusAndCollege = array();

                        //accepted student
                        $changeCampusAndCollege['AcceptedStudent']['id'] = $std['AcceptedStudent']['id'];
                        $changeCampusAndCollege['AcceptedStudent']['campus_id'] = $this->request->data['AcceptedStudent']['campus_id'];
                        $changeCampusAndCollege['AcceptedStudent']['original_college_id'] = $campusDetail['Campus']['available_for_college'];
                        $changeCampusAndCollege['AcceptedStudent']['college_id'] = $this->request->data['AcceptedStudent']['selected_college_id'];

                        $changeCampusAndCollege['AcceptedStudent']['department_id'] = null;
                        $changeCampusAndCollege['AcceptedStudent']['curriculum_id'] = null;
                        $changeCampusAndCollege['AcceptedStudent']['Placement_Approved_By_Department'] = null;
                        $changeCampusAndCollege['AcceptedStudent']['placementtype'] = null;

                        $changeCampusAndCollege['AcceptedStudent']['placement_based'] = null;

                        //admitted student
                        $changeCampusAndCollege['Student']['id'] = $std['Student']['id'];

                        $changeCampusAndCollege['Student']['original_college_id'] = $campusDetail['Campus']['available_for_college'];
                        $changeCampusAndCollege['Student']['college_id'] = $this->request->data['AcceptedStudent']['selected_college_id'];

                        $changeCampusAndCollege['Student']['department_id'] = null;
                        $changeCampusAndCollege['Student']['curriculum_id'] = null;

                        // move the historical grade,registration to the new table
                        $historicalData = array();
                        $tobeDeletedRegIds = array();
                        $tobeDeletedAddIds = array();
                        $tobeDeletedGradeIds = array();
                        $countr = 0;

                        if (isset($std['CourseRegistration']) && !empty($std['CourseRegistration'])) {
                            foreach ($std['CourseRegistration'] as $rk => $rv) {
                                $approvedGrade = ClassRegistry::init('ExamGrade')->getApprovedGrade($rv['id'], 1);
                                $tobeDeletedGradeIds[$countr] = $approvedGrade['grade_id'];
                                $tobeDeletedRegIds[$countr] = $rv['id'];
                                $historicalData[$countr]['HistoricalStudentCourseGradeExclude']['grade'] = $approvedGrade['grade'];
                                $historicalData[$countr]['HistoricalStudentCourseGradeExclude']['student_id'] = $rv['student_id'];
                                $historicalData[$countr]['HistoricalStudentCourseGradeExclude']['academic_year'] = $rv['academic_year'];
                                $historicalData[$countr]['HistoricalStudentCourseGradeExclude']['semester'] = $rv['semester'];
                                $historicalData[$countr]['HistoricalStudentCourseGradeExclude']['published_course_id'] = $rv['published_course_id'];
                                $historicalData[$countr]['HistoricalStudentCourseGradeExclude']['course_registration_id'] = $rv['id'];
                                $historicalData[$countr]['HistoricalStudentCourseGradeExclude']['processed_by'] = $this->Session->read(
                                    'Auth.User'
                                )['id'];

                                //ALTER TABLE `historical_student_course_grade_excludes` ADD `processed_by` VARCHAR(36) NULL DEFAULT NULL AFTER `course_add_id`;

                                $countr++;
                            }
                        }

                        if (isset($std['CourseAdd']) && !empty($std['CourseAdd'])) {
                            foreach ($std['CourseAdd'] as $rk => $rv) {
                                $approvedGrade = ClassRegistry::init('ExamGrade')->getApprovedGrade($rv['id'], 0);
                                $tobeDeletedGradeIds[$countr] = $approvedGrade['grade_id'];
                                $tobeDeletedAddIds[$countr] = $rv['id'];

                                $historicalData[$countr]['HistoricalStudentCourseGradeExclude']['grade'] = $approvedGrade['grade'];
                                $historicalData[$countr]['HistoricalStudentCourseGradeExclude']['student_id'] = $rv['student_id'];
                                $historicalData[$countr]['HistoricalStudentCourseGradeExclude']['academic_year'] = $rv['academic_year'];
                                $historicalData[$countr]['HistoricalStudentCourseGradeExclude']['semester'] = $rv['semester'];
                                $historicalData[$countr]['HistoricalStudentCourseGradeExclude']['published_course_id'] = $rv['published_course_id'];
                                $historicalData[$countr]['HistoricalStudentCourseGradeExclude']['course_add_id'] = $rv['id'];
                                $historicalData[$countr]['HistoricalStudentCourseGradeExclude']['processed_by'] = $this->Session->read(
                                    'Auth.User'
                                )['id'];

                                $countr++;
                            }
                        }

                        $historySaved = false;

                        if (isset($historicalData) && !empty($historicalData)) {
                            if (ClassRegistry::init('HistoricalStudentCourseGradeExclude')->saveAll(
                                $historicalData,
                                array('validate' => false)
                            )) {
                                $historySaved = true;
                            }
                        }

                        if ($historySaved) {
                            //cancel status
                            $cancelSt = ClassRegistry::init('StudentExamStatus')->deleteAll(
                                array('StudentExamStatus.student_id' => $std['Student']['id']),
                                false
                            );
                            if (isset($tobeDeletedRegIds) && !empty($tobeDeletedRegIds)) {
                                $cancelReg = ClassRegistry::init('CourseRegistration')->deleteAll(
                                    array('CourseRegistration.id' => $tobeDeletedRegIds),
                                    false
                                );
                            }

                            if (isset($tobeDeletedAddIds) && !empty($tobeDeletedAddIds)) {
                                $cancelAdd = ClassRegistry::init('CourseAdd')->deleteAll(
                                    array('CourseAdd.id' => $tobeDeletedAddIds),
                                    false
                                );
                            }

                            if (isset($tobeDeletedGradeIds) && !empty($tobeDeletedGradeIds)) {
                                $cancelExamGrade = ClassRegistry::init('ExamGrade')->deleteAll(
                                    array('ExamGrade.id' => $tobeDeletedGradeIds),
                                    false
                                );
                            }
                        }

                        if (isset($changeCampusAndCollege) && !empty($changeCampusAndCollege)) {
                            if (isset($changeCampusAndCollege['AcceptedStudent']) && !empty($changeCampusAndCollege['AcceptedStudent'])) {
                                if ($this->AcceptedStudent->save(
                                    $changeCampusAndCollege['AcceptedStudent'],
                                    array('validate' => false)
                                )) {
                                    $readmittedCount++;
                                    if (isset($changeCampusAndCollege['Student']) && !empty($changeCampusAndCollege['Student'])) {
                                        if ($this->AcceptedStudent->Student->save(
                                            $changeCampusAndCollege['Student'],
                                            array('validate' => false)
                                        )) {
                                        }
                                    }
                                }
                            }
                        }
                    }

                    if ($readmittedCount > 0) {
                        $this->Flash->success(
                            'Readmitted ' . ($readmittedCount == 1 ? $readmittedCount . ' student' : $readmittedCount . ' students') . "  to freshman successfully."
                        );
                    }
                } else {
                    $this->Flash->error("Please select at least one student for readmission placement to freshman.");
                }
            } else {
                $this->Flash->error("The selected campus and college mismatched, Please select the correct match.");
            }
        }
    }

    public function place_to_campus($id = null)
    {

        //********* TO Filter students per academic year, College, Program, program_type and display

        $this->AcceptedStudent->recursive = 0;

        $acceptedStudents = null;
        $selectedsacdemicyear = null;
        $selected_program = null;
        $selected_program_type = null;
        $selected_college = null;

        $colleges = $this->AcceptedStudent->College->find(
            'list',
            array('conditions' => array('College.id' => array(2, 6)))
        );

        $selectedsacdemicyear = $this->AcademicYear->current_academicyear();
        $isbeforesearch = 1;

        $this->set(
            compact(
                'data',
                'selectedsacdemicyear',
                'programs',
                'programTypes',
                'colleges',
                'isbeforesearch'
            )
        );

        if (!empty($this->request->data) && isset($this->request->data['search'])) {
            $isbeforesearch = 0;

            $selectedsacdemicyear = $this->request->data['AcceptedStudent']['academicyear'];
            $selected_college = $this->request->data['AcceptedStudent']['college_id'];
            $selected_program = $this->request->data['AcceptedStudent']['program_id'];
            $selected_program_type = $this->request->data['AcceptedStudent']['program_type_id'];

            if (!empty($this->request->data['AcceptedStudent']['limit'])) {
                $limit = $this->request->data['AcceptedStudent']['limit'];
            } else {
                $limit = 10000;
            }


            $conditions = array(
                "AcceptedStudent.academicyear" => $selectedsacdemicyear,
                'AcceptedStudent.college_id' => $selected_college,
                'AcceptedStudent.program_id' => $selected_program,
                'AcceptedStudent.program_type_id' => $selected_program_type,
                'AcceptedStudent.campus_id' => 0,

            );

            $this->paginate = array(
                'conditions' => $conditions,
                'order' => array('AcceptedStudent.first_name ASC'),
                'limit' => $limit,
                'maxLimit' => $limit,
                'recursive' => -1
            );

            $this->Paginator->settings = $this->paginate;
            $acceptedStudents = $this->Paginator->paginate('AcceptedStudent');

            if (isset($acceptedStudents) && !empty($acceptedStudents)) {
                $campuses = $this->AcceptedStudent->College->Campus->find(
                    'all',
                    array('conditions' => array('Campus.available_for_college' => $selected_college), 'recursive' => -1)
                );
                $campusesAssigned = array();

                if (!empty($campuses)) {
                    foreach ($campuses as $ck => $cv) {
                        $maleToBeAssigned = array();
                        $femaleToBeAssigned = array();
                        $capacity = 0;
                        $maleCapacity = $cv['Campus']["male_capacity"];
                        $femaleCapacity = $cv['Campus']["female_capacity"];

                        $maleInTheSystem = $this->AcceptedStudent->find('count', array(
                            'conditions' => array(
                                "AcceptedStudent.academicyear" => $selectedsacdemicyear,
                                "AcceptedStudent.college_id" => $selected_college,
                                "AcceptedStudent.program_id" => $selected_program,
                                "AcceptedStudent.program_type_id" => $selected_program_type,
                                "AcceptedStudent.campus_id" => 0,
                                "AcceptedStudent.sex like" => "male%"
                            ),
                            'fields' => array('id', 'campus_id'),
                            'recursive' => -1
                        ));

                        if ($maleCapacity < $maleInTheSystem) {
                            $capacity = $maleCapacity;
                        } else {
                            $capacity = $maleInTheSystem;
                        }

                        $randomMale = $this->AcceptedStudent->find('all', array(
                            'conditions' => array(
                                "AcceptedStudent.academicyear" => $selectedsacdemicyear,
                                "AcceptedStudent.college_id" => $selected_college,
                                "AcceptedStudent.program_id" => $selected_program,
                                "AcceptedStudent.program_type_id" => $selected_program_type,
                                "AcceptedStudent.campus_id" => 0,
                                "AcceptedStudent.sex like" => "male%"

                            ),
                            'order' => 'rand()',
                            'limit' => $capacity,
                            'fields' => array('id', 'campus_id'),
                            'recursive' => -1
                        ));

                        $count = 0;

                        if (!empty($randomMale)) {
                            foreach ($randomMale as $rk => $rv) {
                                $maleToBeAssigned['AcceptedStudent'][$count]['id'] = $rv['AcceptedStudent']['id'];
                                $maleToBeAssigned['AcceptedStudent'][$count]['campus_id'] = $cv['Campus']['id'];
                                $count++;
                            }
                        }

                        //assigned the male
                        if (isset($maleToBeAssigned['AcceptedStudent']) && !empty($maleToBeAssigned['AcceptedStudent'])) {
                            if ($this->AcceptedStudent->saveAll($maleToBeAssigned['AcceptedStudent'])) {
                                $campusesAssigned[$cv['Campus']['name']]['male_assigned'] = count(
                                    $maleToBeAssigned['AcceptedStudent']
                                );
                                $campusesAssigned[$cv['Campus']['name']]['campus_male_capacity'] = $cv['Campus']["male_capacity"];
                            }
                        }

                        $femaleInTheSystem = $this->AcceptedStudent->find('count', array(
                            'conditions' => array(
                                "AcceptedStudent.academicyear" => $selectedsacdemicyear,
                                "AcceptedStudent.college_id" => $selected_college,
                                "AcceptedStudent.program_id" => $selected_program,
                                "AcceptedStudent.program_type_id" => $selected_program_type,
                                "AcceptedStudent.campus_id" => 0,
                                "AcceptedStudent.sex like" => "female%"
                            ),
                            'fields' => array('id', 'campus_id'),
                            'recursive' => -1
                        ));

                        if ($femaleCapacity < $femaleInTheSystem) {
                            $capacity = $femaleCapacity;
                        } else {
                            $capacity = $femaleInTheSystem;
                        }

                        $randomfemale = $this->AcceptedStudent->find('all', array(
                            'conditions' => array(
                                "AcceptedStudent.academicyear" => $selectedsacdemicyear,
                                "AcceptedStudent.college_id" => $selected_college,
                                "AcceptedStudent.program_id" => $selected_program,
                                "AcceptedStudent.program_type_id" => $selected_program_type,
                                "AcceptedStudent.campus_id" => 0,
                                "AcceptedStudent.sex like" => "female%"

                            ),
                            'order' => 'rand()',
                            'limit' => $cv['Campus']["female_capacity"],
                            'fields' => array('id', 'campus_id'),
                            'recursive' => -1
                        ));

                        $count = 0;

                        if (!empty($randomfemale)) {
                            foreach ($randomfemale as $rk => $rv) {
                                $femaleToBeAssigned['AcceptedStudent'][$count]['id'] = $rv['AcceptedStudent']['id'];
                                $femaleToBeAssigned['AcceptedStudent'][$count]['campus_id'] = $cv['Campus']['id'];
                                $count++;
                            }
                        }

                        //assigned the female
                        if (isset($femaleToBeAssigned['AcceptedStudent']) && !empty($femaleToBeAssigned['AcceptedStudent'])) {
                            if ($this->AcceptedStudent->saveAll($femaleToBeAssigned['AcceptedStudent'])) {
                                $campusesAssigned[$cv['Campus']['name']]['female_assigned'] = count(
                                    $femaleToBeAssigned['AcceptedStudent']
                                );
                                $campusesAssigned[$cv['Campus']['name']]['campus_female_capacity'] = $cv['Campus']["female_capacity"];
                            }
                        }
                    }
                }

                if (isset($campusesAssigned) && !empty($campusesAssigned)) {
                    $message = '';
                    $message .= '<ul>';
                    foreach ($campusesAssigned as $ckk => $cv) {
                        $totalA = $cv['female_assigned'] + $cv['male_assigned'];
                        $message .= '<li> In ' . $ckk . ' a total of ' . $totalA . ' students are assigned. The total male assigned is ' . $cv['male_assigned'] . ' and female assigned is ' . $cv['female_assigned'] . '</li> <br/>';
                    }
                    $message .= '</ul>';
                    $this->Session->setFlash(
                        '<span></span> Campus placement is done and you can view their placement "AcceptedStudents=>View Campus Assignment" ! <br/><br/><br/>' . $message . '',
                        'default',
                        array('class' => 'success-box success-message')
                    );
                }
            }

            $this->set('show_list_generated', true);

            $this->set(
                compact(
                    'selectedsacdemicyear',
                    'acceptedStudents',
                    'selected_college',
                    'selected_program',
                    'selected_program_type',
                    'isbeforesearch'
                )
            );
        }

        // delete the assignment

        if (!empty($this->request->data) && isset($this->request->data['cancel'])) {
            $assigned = $this->AcceptedStudent->find('all', array(
                'conditions' => array(
                    "AcceptedStudent.academicyear" => $this->request->data['AcceptedStudent']['academicyear'],
                    "AcceptedStudent.college_id" => $this->request->data['AcceptedStudent']['college_id'],
                    "AcceptedStudent.program_id" => $this->request->data['AcceptedStudent']['program_id'],
                    "AcceptedStudent.program_type_id" => $this->request->data['AcceptedStudent']['program_type_id'],
                    "AcceptedStudent.campus_id !=" => 0,
                ),
                'fields' => array('id', 'campus_id'),
                'recursive' => -1
            ));

            if (isset($assigned) && !empty($assigned)) {
                $count = 0;
                $cancelAssignment = array();

                foreach ($assigned as $rk => $rv) {
                    $cancelAssignment['AcceptedStudent'][$count]['id'] = $rv['AcceptedStudent']['id'];
                    $cancelAssignment['AcceptedStudent'][$count]['campus_id'] = 0;
                    $count++;
                }

                if (!empty($cancelAssignment['AcceptedStudent'])) {
                    if ($this->AcceptedStudent->saveAll($cancelAssignment['AcceptedStudent'])) {
                        $this->Flash->success(
                            'Campus placement is cancelled for ' . count(
                                $cancelAssignment['AcceptedStudent']
                            ) . ' students.'
                        );
                    }
                }
            }
        }
    }

    public function place_student_to_college_for_section($id = null)
    {

        //********* TO Filter students per academic year, College, Program, program_type and display

        $this->AcceptedStudent->recursive = 0;
        $acceptedStudents = null;
        $selectedsacdemicyear = null;
        $selected_program = null;
        $selected_program_type = null;
        $selected_college = null;

        $colleges = $this->AcceptedStudent->College->find(
            'list',
            array('conditions' => array('College.id' => array(2, 6)))
        );

        $selectedsacdemicyear = $this->AcademicYear->current_academicyear();
        $isbeforesearch = 1;

        $this->set(
            compact(
                'data',
                'selectedsacdemicyear',
                'programs',
                'programTypes',
                'colleges',
                'isbeforesearch'
            )
        );

        if (!empty($this->request->data) && isset($this->request->data['search'])) {
            $isbeforesearch = 0;
            $selectedsacdemicyear = $this->request->data['AcceptedStudent']['academicyear'];
            $selected_college = $this->request->data['AcceptedStudent']['college_id'];
            $selected_program = $this->request->data['AcceptedStudent']['program_id'];
            $selected_program_type = $this->request->data['AcceptedStudent']['program_type_id'];

            if (!empty($this->request->data['AcceptedStudent']['limit'])) {
                $limit = $this->request->data['AcceptedStudent']['limit'];
            } else {
                $limit = 10000;
            }

            $acceptedStudents = $this->AcceptedStudent->find('all', array(
                'conditions' => array(
                    "AcceptedStudent.academicyear" => $selectedsacdemicyear,
                    "AcceptedStudent.college_id" => $selected_college,
                    "AcceptedStudent.program_id" => $selected_program,
                    "AcceptedStudent.program_type_id" => $selected_program_type,
                    "AcceptedStudent.campus_id != 0"
                ),
                'limit' => $limit,
                'maxLimit' => $limit,
                'fields' => array('id', 'campus_id', 'college_id', 'original_college_id'),
                'contain' => array('Student'),
                'recursive' => -1
            ));


            if (isset($acceptedStudents) && !empty($acceptedStudents)) {
                $campuses = $this->AcceptedStudent->College->Campus->find(
                    'all',
                    array('conditions' => array('Campus.available_for_college' => $selected_college), 'recursive' => -1)
                );
                $totalAssigned = 0;

                if (!empty($campuses)) {
                    foreach ($campuses as $ck => $cv) {
                        $colleges = $this->AcceptedStudent->College->find('all', array(
                            'conditions' => array(
                                'College.campus_id' => $cv['Campus']['id'],
                                'College.available_for_placement' => 1
                            ),
                            'recursive' => -1
                        ));

                        $maleCapacity = $cv['Campus']["male_capacity"] / count($colleges);
                        $femaleCapacity = $cv['Campus']["female_capacity"] / count($colleges);

                        if (!empty($colleges)) {
                            foreach ($colleges as $cl => $clv) {
                                $acceptedStudentToBeAssigned = array();
                                $admittedStudentToBeAssigned = array();
                                $count = 0;

                                $allInCampuse = $this->AcceptedStudent->find('all', array(
                                    'conditions' => array(
                                        "AcceptedStudent.academicyear" => $selectedsacdemicyear,
                                        "AcceptedStudent.college_id" => $selected_college,
                                        "AcceptedStudent.program_id" => $selected_program,
                                        "AcceptedStudent.program_type_id" => $selected_program_type,
                                        "AcceptedStudent.campus_id" => $cv['Campus']['id'],
                                    ),
                                    'limit' => $maleCapacity + $femaleCapacity,
                                    'maxLimit' => $maleCapacity + $femaleCapacity,
                                    'contain' => array('Student')
                                ));


                                if (!empty($allInCampuse)) {
                                    foreach ($allInCampuse as $rk => $rv) {
                                        if (isset($rv['AcceptedStudent']['campus_id']) && !empty($rv['AcceptedStudent']['campus_id'])) {
                                            $acceptedStudentToBeAssigned['AcceptedStudent'][$count]['id'] = $rv['AcceptedStudent']['id'];
                                            $acceptedStudentToBeAssigned['AcceptedStudent'][$count]['college_id'] = $clv['College']['id'];
                                            // $acceptedStudentToBeAssigned['AcceptedStudent'][$count]['campus_id']=$clv['College']['campus_id'];

                                            if (isset($rv['Student']['id']) && !empty($rv['Student']['id'])) {
                                                $admittedStudentToBeAssigned['Student'][$count]['id'] = $rv['Student']['id'];
                                                $admittedStudentToBeAssigned['Student'][$count]['college_id'] = $clv['College']['id'];
                                            }

                                            $count++;
                                        }
                                    }
                                }

                                //save the result
                                if (isset($acceptedStudentToBeAssigned) && !empty($acceptedStudentToBeAssigned)) {
                                    $totalAssigned += count($acceptedStudentToBeAssigned['AcceptedStudent']);
                                    if ($this->AcceptedStudent->saveAll(
                                        $acceptedStudentToBeAssigned['AcceptedStudent']
                                    )) {
                                        if (isset($admittedStudentToBeAssigned['Student']) && !empty($admittedStudentToBeAssigned['Student'])) {
                                            if ($this->AcceptedStudent->Student->saveAll(
                                                $admittedStudentToBeAssigned['Student'],
                                                array('validate' => false)
                                            )) {
                                            }
                                        }
                                    }
                                }
                            }
                        }
                    }
                }

                if ($totalAssigned > 0) {
                    $this->Flash->success(
                        '<span></span> College placement for section management of ' . $totalAssigned . ' students are done and you can view their placement "AcceptedStudents=>View Campus Assignment" !'
                    );
                    $this->redirect(array('action' => 'view_campus_assignment'));
                }
            }

            $this->set('show_list_generated', true);

            $this->set(
                compact(
                    'selectedsacdemicyear',
                    'acceptedStudents',
                    'selected_college',
                    'selected_program',
                    'selected_program_type',
                    'isbeforesearch'
                )
            );
        }

        // delete the assignment

        if (!empty($this->request->data) && isset($this->request->data['backtomoe'])) {
            $assigned = $this->AcceptedStudent->find('all', array(
                'conditions' => array(
                    "AcceptedStudent.academicyear" => $this->request->data['AcceptedStudent']['academicyear'],
                    "AcceptedStudent.original_college_id" => $this->request->data['AcceptedStudent']['college_id'],
                    "AcceptedStudent.program_id" => $this->request->data['AcceptedStudent']['program_id'],
                    "AcceptedStudent.program_type_id" => $this->request->data['AcceptedStudent']['program_type_id'],
                    "AcceptedStudent.campus_id !=" => 0,
                    //"AcceptedStudent.original_college_id!="=>0
                ),
                'limit' => 10000,
                'maxLimit' => 10000,
                'contain' => array('Student')
            ));

            if (isset($assigned) && !empty($assigned)) {
                $count = 0;
                $backToMoEAssignmentAccepted = array();
                $backToMoEAssignmentAdmitted = array();

                foreach ($assigned as $rk => $rv) {
                    $backToMoEAssignmentAccepted['AcceptedStudent'][$count]['id'] = $rv['AcceptedStudent']['id'];
                    $backToMoEAssignmentAccepted['AcceptedStudent'][$count]['college_id'] = $rv['AcceptedStudent']['original_college_id'];

                    if (isset($rv['Student']['id']) && !empty($rv['Student']['id'])) {
                        $backToMoEAssignmentAdmitted['Student'][$count]['id'] = $rv['Student']['id'];
                        $backToMoEAssignmentAdmitted['Student'][$count]['college_id'] = $rv['AcceptedStudent']['original_college_id'];
                    }
                    $count++;
                }

                if ($this->AcceptedStudent->saveAll($backToMoEAssignmentAccepted['AcceptedStudent'])) {
                    $this->Flash->success(
                        'College placement for section is restored for ' . count(
                            $backToMoEAssignmentAccepted['AcceptedStudent']
                        ) . ' students back to the original college admitted.'
                    );
                    if (isset($backToMoEAssignmentAdmitted['Student']) && !empty($backToMoEAssignmentAdmitted['Student'])) {
                        if ($this->AcceptedStudent->Student->saveAll(
                            $backToMoEAssignmentAdmitted['Student'],
                            array('validate' => false)
                        )) {
                        }
                    }
                } else {
                    debug($backToMoEAssignmentAccepted);
                    debug($backToMoEAssignmentAdmitted);
                }
            }
        }
    }

    public function view_campus_assignment()
    {

        //$this->AcceptedStudent->recursive = 0;
        $acceptedStudents = null;
        $selectedsacdemicyear = null;
        $selected_program = null;
        $selected_program_type = null;
        $selected_college = null;

        $programs = $this->AcceptedStudent->Program->find('list');
        $programTypes = $this->AcceptedStudent->ProgramType->find('list');

        $colleges = $this->AcceptedStudent->College->find(
            'list',
            array('conditions' => array(/* 'College.id' => $this->college_ids,  */ 'College.active' => 1))
        );

        $campuses = $this->AcceptedStudent->Campus->find('list');

        $selectedsacdemicyear = $this->AcademicYear->current_academicyear();
        $isbeforesearch = 1;

        $this->set(
            compact(
                'selectedsacdemicyear',
                'campuses',
                'programs',
                'programTypes',
                'departments',
                'colleges',
                'isbeforesearch'
            )
        );

        if ($this->Session->read('search_data')) {
            $this->request->data['search'] = true;
        }

        if (!empty($this->request->data) && isset($this->request->data['search'])) {
            $isbeforesearch = 0;
            $selectedsacdemicyear = $this->request->data['AcceptedStudent']['academicyear'];
            $conditions = array();

            if (!empty($this->request->data['AcceptedStudent']['college_id'])) {
                $conditions['AcceptedStudent.college_id'] = $this->request->data['AcceptedStudent']['college_id'];
            } else {
                $conditions['AcceptedStudent.college_id'] = $this->college_ids;
            }

            if (!empty($this->request->data['AcceptedStudent']['campus_id'])) {
                $conditions['AcceptedStudent.campus_id'] = $this->request->data['AcceptedStudent']['campus_id'];
            }

            if (!empty($this->request->data['AcceptedStudent']['program_id'])) {
                $conditions['AcceptedStudent.program_id'] = $this->request->data['AcceptedStudent']['program_id'];
            } else {
                $conditions['AcceptedStudent.program_id'] = $this->program_id;
            }

            if (!empty($this->request->data['AcceptedStudent']['program_type_id'])) {
                $conditions['AcceptedStudent.program_type_id'] = $this->request->data['AcceptedStudent']['program_type_id'];
            } else {
                $conditions['AcceptedStudent.program_type_id'] = $this->program_type_ids;
            }

            if (!empty($this->request->data['AcceptedStudent']['academicyear'])) {
                $conditions['AcceptedStudent.academicyear'] = $this->request->data['AcceptedStudent']['academicyear'];
            } else {
                $conditions['AcceptedStudent.academicyear'] = $this->AcademicYear->current_academicyear();
            }

            if (!empty($this->request->data['AcceptedStudent']['sex'])) {
                $conditions['AcceptedStudent.sex like'] = $this->request->data['AcceptedStudent']['sex'] . '%';
            }

            $this->paginate = array(
                'fields' => array('full_name', 'sex', 'studentnumber'),
                'order' => array('AcceptedStudent.full_name ASC '),
                'contain' => array('Region' => array('fields' => 'name'))
            );

            debug($this->request->data);
            $limit = 2000;

            if (isset($this->request->data['Search']['limit']) && !empty($this->request->data['Search']['limit'])) {
                $this->paginate['limit'] = $this->request->data['Search']['limit'];
                $this->paginate['maxLimit'] = $this->request->data['Search']['limit'];
            }

            $this->paginate['conditions'] = $conditions;
            $this->Paginator->settings = $this->paginate;


            $acceptedStudents = $this->Paginator->paginate('AcceptedStudent');

            if (!empty($this->request->data['AcceptedStudent']['college_id'])) {
                $selected_college_name = $this->AcceptedStudent->College->field(
                    'College.name',
                    array('College.id' => $this->request->data['AcceptedStudent']['college_id'])
                );
            }

            if (!empty($this->request->data['AcceptedStudent']['campus_id'])) {
                $selected_campus_name = $this->AcceptedStudent->Campus->field(
                    'Campus.name',
                    array('Campus.id' => $this->request->data['AcceptedStudent']['campus_id'])
                );
                //$selected_campus_name="";
            }


            $selected_program_name = $this->AcceptedStudent->Program->field(
                'Program.name',
                array('Program.id' => $this->request->data['AcceptedStudent']['program_id'])
            );
            $selected_program_type_name = $this->AcceptedStudent->ProgramType->field(
                'ProgramType.name',
                array('ProgramType.id' => $this->request->data['AcceptedStudent']['program_type_id'])
            );

            $this->Session->write('acceptedStudents', $acceptedStudents);
            $this->Session->write('selected_college_name', $selected_college_name);
            $this->Session->write('selected_campus_name', $selected_campus_name);
            $this->Session->write('selected_program_name', $selected_program_name);
            $this->Session->write('selected_program_type_name', $selected_program_type_name);
            $this->Session->write('selected_acdemicyear', $selectedsacdemicyear);

            $this->set(
                compact(
                    'selectedsacdemicyear',
                    'acceptedStudents',
                    'selected_college_name',
                    'selected_program_name',
                    'selected_program_type_name',
                    'selected_campus_name',
                    'isbeforesearch',
                    'departments',
                    'selected_department_name',
                    'campuses'
                )
            );
        }
    }

    public function transfer_campus()
    {

        // transfer campus
        if (!empty($this->request->data) && isset($this->request->data['transfer'])) {
            $selected_count = array_count_values($this->request->data['AcceptedStudent']['approve']);

            if (isset($selected_count[1]) && $selected_count[1] > 0) {
                unset($this->request->data['AcceptedStudent']['SelectAll']);

                $approve_placement = $this->request->data;
                $update_admitted_students_department = array();
                $selected_academic_year = $approve_placement['AcceptedStudent']['academicyear'];
                $campus_id = $approve_placement['AcceptedStudent']['campus_id'];
                $selected_college_id = $approve_placement['AcceptedStudent']['selected_college_id'];
                // check if the selected campus and college in correct order
                $selectedCollege = $this->AcceptedStudent->College->find(
                    'first',
                    array('conditions' => array('College.id' => $selected_college_id))
                );

                if ($selectedCollege['College']['campus_id'] != $campus_id) {
                    $this->Flash->error('Campus and college mismatch.');
                    $this->redirect(array('action' => 'transfer_campus'));
                }

                $selected_approved_students = $approve_placement['AcceptedStudent']['approve'];
                $selectedStudents = array_keys($selected_approved_students, 1);

                unset($approve_placement['AcceptedStudent']['academicyear']);
                unset($approve_placement['AcceptedStudent']['campus_id']);
                unset($approve_placement['AcceptedStudent']['approve']);

                if (!empty($selectedStudents)) {
                    //transfer them to the selected campus
                    //update section if there is a section for that students
                    //what if there is registration, update registration with the new
                    //section
                    $countActualTransfered = 0;

                    foreach ($selectedStudents as $sk => $sv) {
                        $studentDetail = $this->AcceptedStudent->find('first', array(
                            'conditions' => array(
                                "AcceptedStudent.id" => $sv
                            ),
                            'contain' => array(
                                'Student' => array('CourseRegistration')
                            )
                        ));

                        //does the student have ID and Admitted ?
                        if (isset($studentDetail['Student']['id']) && !empty($studentDetail['Student']['id']) && isset($studentDetail['AcceptedStudent']['studentnumber']) && !empty($studentDetail['AcceptedStudent']['studentnumber'])) {
                            $sectionChecking = ClassRegistry::init('StudentsSection')->doesTheStudentHasSection(
                                $studentDetail['Student']['id'],
                                $selected_academic_year
                            );

                            //check the college who have the selected campus matched.
                            //does the student have course registration ?
                            if ($sectionChecking == 1) {
                                // do nothing
                            } else {
                                if (isset($sectionChecking) && !empty($sectionChecking)) {
                                    // 1. Any registration, cancel if there is no grade
                                    $courseRegistration = $this->AcceptedStudent->Student->CourseRegistration->find(
                                        'first',
                                        array(
                                            'conditions' => array(
                                                'CourseRegistration.student_id' => $studentDetail['Student']['id'],
                                                'CourseRegistration.section_id' => $sectionChecking['StudentsSection']['section_id'],
                                            ),
                                            'contain' => array('ExamGrade', 'ExamResult')
                                        )
                                    );

                                    if (isset($courseRegistration['ExamGrade']) && !empty($courseRegistration['ExamGrade']) || isset($courseRegistration['ExamResult']) && !empty($courseRegistration['ExamResult'])) {
                                        //nothing to do
                                        continue;
                                    } else {
                                        if (isset($courseRegistration['CourseRegistration']) && !empty($courseRegistration['CourseRegistration'])) {
                                            // there is registration but not grade
                                            // cancel registration, add, and section , what if readmitted student ? we need to do validation.
                                            ClassRegistry::init('CourseRegistration')->deleteAll(
                                                array('CourseRegistration.student_id' => $studentDetail['Student']['id']),
                                                false
                                            );
                                            // cancel section of the student
                                            ClassRegistry::init('StudentsSection')->delete(
                                                $sectionChecking['StudentsSection']['id']
                                            );
                                        } else {
                                            if (empty($courseRegistration['CourseRegistration'])) {
                                                // cancel section of the student
                                                ClassRegistry::init('StudentsSection')->delete(
                                                    $sectionChecking['StudentsSection']['id']
                                                );
                                                //place them to campuse
                                                //find campus college available for placement
                                                if (isset($studentDetail['AcceptedStudent']['id']) && !empty($studentDetail['AcceptedStudent']['id'])) {
                                                    if (isset($campus_id) && !empty($campus_id) && isset($selected_college_id) && !empty($selected_college_id)) {
                                                        $this->AcceptedStudent->id = $studentDetail['AcceptedStudent']['id'];
                                                        $this->AcceptedStudent->saveField('campus_id', $campus_id);
                                                        $this->AcceptedStudent->saveField(
                                                            'college_id',
                                                            $selected_college_id
                                                        );
                                                        $this->AcceptedStudent->Student->id = $studentDetail['Student']['id'];
                                                        //$this->AcceptedStudent->Student->saveField('campus_id',$campus_id);
                                                        $this->AcceptedStudent->Student->saveField(
                                                            'college_id',
                                                            $selected_college_id
                                                        );
                                                        $countActualTransfered++;
                                                    }
                                                }
                                            } else {
                                                // nothing is recorded
                                                // update the new campus
                                                if (isset($studentDetail['AcceptedStudent']['id']) && !empty($studentDetail['AcceptedStudent']['id'])) {
                                                    $this->AcceptedStudent->id = $studentDetail['AcceptedStudent']['id'];
                                                    $this->AcceptedStudent->saveField('campus_id', $campus_id);
                                                    $this->AcceptedStudent->saveField(
                                                        'college_id',
                                                        $selected_college_id
                                                    );
                                                    $this->AcceptedStudent->Student->id = $studentDetail['Student']['id'];
                                                    //$this->AcceptedStudent->Student->saveField('campus_id',$campus_id);
                                                    $this->AcceptedStudent->Student->saveField(
                                                        'college_id',
                                                        $selected_college_id
                                                    );
                                                    $countActualTransfered++;
                                                }
                                            }
                                        }
                                    }
                                } else {
                                    //nothing is here transfer them simply.

                                    if (isset($studentDetail['AcceptedStudent']['id']) && !empty($studentDetail['AcceptedStudent']['id'])) {
                                        if (isset($campus_id) && !empty($campus_id) && isset($selected_college_id) && !empty($selected_college_id)) {
                                            $this->AcceptedStudent->id = $studentDetail['AcceptedStudent']['id'];
                                            $this->AcceptedStudent->saveField('campus_id', $campus_id);
                                            $this->AcceptedStudent->saveField('college_id', $selected_college_id);
                                            if (isset($studentDetail['Student']['id']) && !empty($studentDetail['Student']['id'])) {
                                                $this->AcceptedStudent->Student->id = $studentDetail['Student']['id'];
                                                //$this->AcceptedStudent->Student->saveField('campus_id',$campus_id);
                                                $this->AcceptedStudent->Student->saveField(
                                                    'college_id',
                                                    $selected_college_id
                                                );
                                            }
                                            $countActualTransfered++;
                                        }
                                    }
                                }
                            }
                        }
                    }

                    //update section
                    if ($countActualTransfered > 0) {
                        $this->Flash->success(
                            'Among the selected students ' . $countActualTransfered . ' is successfully transfered to the selected campus.'
                        );
                    }
                } else {
                    $this->Flash->error('Please select atleast one student to transfer campus.');
                    $this->request->data['searchbutton'] = true;
                }
            }
        }


        if (!empty($this->request->data) && isset($this->request->data['searchbutton'])) {
            $selected_academic_year = $this->request->data['AcceptedStudent']['academicyear'];
            if ($selected_academic_year) {
            } else {
                $selected_academic_year = $this->AcademicYear->current_academicyear();
            }

            $program_id = $this->request->data['AcceptedStudent']['program_id'];
            $this->set('selected_academicyear', $selected_academic_year);

            $placedstudent = $this->AcceptedStudent->find('all', array(
                'conditions' => array(
                    'AcceptedStudent.first_name LIKE' => $this->request->data['AcceptedStudent']['name'] . '%',
                    'AcceptedStudent.academicyear' => $selected_academic_year,
                    'AcceptedStudent.campus_id' => $this->request->data['AcceptedStudent']['campus_id'],
                    'AcceptedStudent.program_id' => $program_id,
                    'AcceptedStudent.program_type_id' => $this->request->data['AcceptedStudent']['program_type_id'],
                    'AcceptedStudent.original_college_id' => $this->request->data['AcceptedStudent']['college_id'],
                    "OR" => array(
                        'AcceptedStudent.department_id IS NULL',
                        'AcceptedStudent.department_id = ""',
                        'AcceptedStudent.department_id = ""'
                    )
                ),
                'contain' => array('Student', 'Campus', 'College')
            ));

            if (empty($placedstudent)) {
                $this->Flash->info(
                    'There is no students  in the system that needs campus transfer in the given criteria.'
                );
            } else {
                $available_campuses = $this->AcceptedStudent->Campus->find('list', array(
                    'conditions' => array(
                        'Campus.available_for_college' => $this->request->data['AcceptedStudent']['college_id']
                    )
                ));

                $selected_colleges = $this->AcceptedStudent->College->find('list', array(
                    'conditions' => array(
                        'College.campus_id' => array_keys($available_campuses),
                        'College.available_for_placement' => 1
                    )
                ));

                $this->set('autoplacedstudents', $placedstudent);
                $this->set('selected_academicyear', $selected_academic_year);
                $this->set('auto_approve', true);
                $this->set(compact('selected_colleges', 'available_campuses'));
            }
        }

        $academicYearLists = $this->AcademicYear->academicYearInArray(date('Y') - 1, date('Y'));
        $programs = $this->AcceptedStudent->Program->find('list');
        $programTypes = $this->AcceptedStudent->ProgramType->find('list');
        $campuses = $this->AcceptedStudent->Campus->find('list');
        $colleges = $this->AcceptedStudent->College->find(
            'list',
            array('conditions' => array('College.id' => array(2, 6)))
        );

        // $colleges = $this->AcceptedStudent->College->find('list',  array('conditions'=>array('College.available_for_placement'=>1))); /
        $this->set(compact('academicYearLists', 'colleges', 'campuses', 'programs', 'programTypes'));
    }

    public function __init_search()
    {

        if (!empty($this->request->data['AcceptedStudent'])) {
            $search_session = $this->request->data['AcceptedStudent'];
            $this->Session->write('search_data_accepted_student', $search_session);
        } else {
            if ($this->Session->check('search_data_accepted_student')) {
                $search_session = $this->Session->read('search_data_accepted_student');
                $this->request->data['AcceptedStudent'] = $search_session;
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

    public function export_print_students_number()
    {

        //$this->AcceptedStudent->recursive = 0;
        $acceptedStudents = null;
        $selectedsacdemicyear = null;
        $selected_program = null;
        $selected_program_type = null;
        $selected_college = null;
        $limit = 100;

        $programs = $this->AcceptedStudent->Program->find('list');
        $programTypes = $this->AcceptedStudent->ProgramType->find('list');

        if (!empty($this->department_ids)) {
            $departments = $this->AcceptedStudent->Department->find(
                'list',
                array('conditions' => array('Department.id' => $this->department_ids, 'Department.active' => 1))
            );
        } else {
            if (!empty($this->college_ids)) {
                $colleges = $this->AcceptedStudent->College->find(
                    'list',
                    array('conditions' => array('College.id' => $this->college_ids, 'College.active' => 1))
                );
            } else {
                if (!empty($this->department_id)) {
                    $departments = $this->AcceptedStudent->Department->find(
                        'list',
                        array('conditions' => array('Department.id' => $this->department_ids, 'Department.active' => 1))
                    );
                } else {
                    if (!empty($this->college_id)) {
                        $colleges = $this->AcceptedStudent->College->find(
                            'list',
                            array('conditions' => array('College.id' => $this->college_ids, 'College.active' => 1))
                        );
                    }
                }
            }
        }

        if ($this->Session->read('Auth.User')['role_id'] == ROLE_REGISTRAR) {
            $programs = $this->AcceptedStudent->Program->find(
                'list',
                array('conditions' => array('Program.id' => $this->program_id))
            );
            $programTypes = $this->AcceptedStudent->ProgramType->find(
                'list',
                array('conditions' => array('ProgramType.id' => $this->program_type_id))
            );
        }

        $regions = $this->AcceptedStudent->Region->find('list');

        $selectedsacdemicyear = $this->AcademicYear->current_academicyear();
        $isbeforesearch = 1;

        $this->set(
            compact(
                'selectedsacdemicyear',
                'programs',
                'programTypes',
                'departments',
                'colleges',
                'isbeforesearch',
                'regions',
                'limit'
            )
        );
        $this->__init_search();

        if ($this->Session->read('search_data')) {
            $this->request->data['search'] = true;
        }

        if (!empty($this->request->data) && isset($this->request->data['search'])) {
            $isbeforesearch = 0;
            $selectedsacdemicyear = $this->request->data['AcceptedStudent']['academicyear'];
            $conditions = array();

            if (!empty($this->request->data['AcceptedStudent']['college_id'])) {
                $conditions['AcceptedStudent.college_id'] = $this->request->data['AcceptedStudent']['college_id'];
            } else {
                if (isset($this->college_ids) && !empty($this->college_ids)) {
                    $conditions['AcceptedStudent.college_id'] = $this->college_ids;
                }
            }

            if (!empty($this->request->data['AcceptedStudent']['department_id'])) {
                $conditions['AcceptedStudent.department_id'] = $this->request->data['AcceptedStudent']['department_id'];
            } else {
                if (isset($this->department_ids) && !empty($this->department_ids)) {
                    $conditions['AcceptedStudent.department_id'] = $this->department_ids;
                }
            }

            if (!empty($this->request->data['AcceptedStudent']['program_id'])) {
                $conditions['AcceptedStudent.program_id'] = $this->request->data['AcceptedStudent']['program_id'];
            } else {
                if (isset($this->program_id) && !empty($this->program_id)) {
                    $conditions['AcceptedStudent.program_id'] = $this->program_id;
                }
            }

            if (!empty($this->request->data['AcceptedStudent']['program_type_id'])) {
                $conditions['AcceptedStudent.program_type_id'] = $this->request->data['AcceptedStudent']['program_type_id'];
            } else {
                if (isset($this->program_type_id) && !empty($this->program_type_id)) {
                    $conditions['AcceptedStudent.program_type_id'] = $this->program_type_id;
                }
            }

            if (!empty($this->request->data['AcceptedStudent']['academicyear'])) {
                $conditions['AcceptedStudent.academicyear'] = $this->request->data['AcceptedStudent']['academicyear'];
            } else {
                $conditions['AcceptedStudent.academicyear'] = $this->AcademicYear->current_academicyear();
            }

            if (!empty($this->request->data['AcceptedStudent']['region_id'])) {
                $conditions['AcceptedStudent.region_id'] = $this->request->data['AcceptedStudent']['region_id'];
            }

            $conditions['NOT'] = array('AcceptedStudent.studentnumber' => array('', 'null'));

            if (!empty($this->request->data['AcceptedStudent']['limit'])) {
                $limit = $this->request->data['AcceptedStudent']['limit'];
            }

            if ($this->request->data['AcceptedStudent']['admitted'] == 1) {
                $conditions[] = array('Student.id IS NULL');
            } else {
                if ($this->request->data['AcceptedStudent']['admitted'] == 2) {
                    $conditions[] = array('Student.id IS NOT NULL');
                }
            }

            /* $this->paginate = array(
				'limit' => $limit,
				'maxLimit' => $limit,
				'fields' => array('full_name', 'sex', 'studentnumber'),
				'order' => array('AcceptedStudent.full_name ASC '),
				'contain' => array('Region' => array('fields' => 'name'))
			); */

            //$this->Paginator->settings['conditions'] = $conditions;

            $this->Paginator->settings = array(
                'conditions' => $conditions,
                'contain' => array(
                    'Region' => array('fields' => 'name'),
                    'Department' => array('fields' => 'name'),
                    'Student' => array('fields' => 'student_national_id')
                ),
                'fields' => array('id', 'full_name', 'sex', 'studentnumber'),
                'order' => array('AcceptedStudent.full_name ASC '),
                'limit' => $limit,
                'maxLimit' => $limit,
                'recursive' => -1
            );

            $acceptedStudents = $this->Paginator->paginate('AcceptedStudent');

            if (!empty($this->request->data['AcceptedStudent']['department_id'])) {
                $selected_dept = $this->AcceptedStudent->Department->find(
                    'first',
                    array('conditions' => array('Department.id' => $this->request->data['AcceptedStudent']['department_id']))
                );
                $selected_department_name = $selected_dept['Department']['name'];
                $selected_college_name = $this->AcceptedStudent->College->field(
                    'College.name',
                    array('College.id' => $selected_dept['Department']['college_id'])
                );
                $selected_campus_id = $this->AcceptedStudent->College->field(
                    'College.campus_id',
                    array('College.id' => $selected_dept['Department']['college_id'])
                );
                $selected_campus_name = $this->AcceptedStudent->College->Campus->field(
                    'Campus.name',
                    array('Campus.id' => $selected_campus_id)
                );
            }

            if (!empty($this->request->data['AcceptedStudent']['college_id'])) {
                $selected_college_name = $this->AcceptedStudent->College->field(
                    'College.name',
                    array('College.id' => $this->request->data['AcceptedStudent']['college_id'])
                );
                $selected_campus_id = $this->AcceptedStudent->College->field(
                    'College.campus_id',
                    array('College.id' => $this->request->data['AcceptedStudent']['college_id'])
                );
                $selected_campus_name = $this->AcceptedStudent->College->Campus->field(
                    'Campus.name',
                    array('Campus.id' => $selected_campus_id)
                );
                $selected_college_name = $this->AcceptedStudent->College->field(
                    'College.name',
                    array('College.id' => $this->request->data['AcceptedStudent']['college_id'])
                );
                $selected_department_name = "Pre/Freshman";
            }

            $selected_program_name = $this->AcceptedStudent->Program->field(
                'Program.name',
                array('Program.id' => $this->request->data['AcceptedStudent']['program_id'])
            );
            $selected_program_type_name = $this->AcceptedStudent->ProgramType->field(
                'ProgramType.name',
                array('ProgramType.id' => $this->request->data['AcceptedStudent']['program_type_id'])
            );

            $this->Session->write('acceptedStudents', $acceptedStudents);
            $this->Session->write('selected_college_name', $selected_college_name);
            $this->Session->write('selected_department_name', $selected_department_name);
            $this->Session->write('selected_program_name', $selected_program_name);
            $this->Session->write('selected_program_type_name', $selected_program_type_name);
            $this->Session->write('selected_acdemicyear', $selectedsacdemicyear);
            $this->Session->write('selected_campus_name', $selected_campus_name);
            $this->Session->write('selected_limit', $limit);

            $this->set(
                compact(
                    'selectedsacdemicyear',
                    'acceptedStudents',
                    'selected_college_name',
                    'selected_program_name',
                    'selected_program_type_name',
                    'isbeforesearch',
                    'departments',
                    'selected_department_name',
                    'selected_campus_name',
                    'limit'
                )
            );
        }
    }

    public function download_csv()
    {

        $acceptedStudents = $this->Session->read('acceptedStudents');
        $compactData = array();
        $counter = 1;

        if (!empty($acceptedStudents)) {
            foreach ($acceptedStudents as $key => $acceptedStudent) {
                //debug($acceptedStudent);
                $compactData[$key]['AcceptedStudent']['#'] = $counter++;
                $compactData[$key]['AcceptedStudent']['Full Name'] = $acceptedStudent['AcceptedStudent']['full_name'];
                $compactData[$key]['AcceptedStudent']['Sex'] = strcasecmp(
                    trim($acceptedStudent['AcceptedStudent']['sex']),
                    'male'
                ) == 0 ? 'M' : (strcasecmp(trim($acceptedStudent['AcceptedStudent']['sex']), 'female') == 0 ? 'F' : '');
                $compactData[$key]['AcceptedStudent']['Student ID'] = $acceptedStudent['AcceptedStudent']['studentnumber'];
                $compactData[$key]['AcceptedStudent']['Department'] = (isset($acceptedStudent['Department']) && !is_null(
                    $acceptedStudent['Department']['name']
                ) ? $acceptedStudent['Department']['name'] : 'Pre/Freshman');
                $compactData[$key]['AcceptedStudent']['Region'] = $acceptedStudent['Region']['name'];
                $compactData[$key]['AcceptedStudent']['Student National ID'] = (isset($acceptedStudent['Student']) && !empty($acceptedStudent['Student']['student_national_id']) ? $acceptedStudent['Student']['student_national_id'] : '');
            }
        }

        $this->set('acceptedStudents', $compactData);

        $selected_college_name = $this->Session->read('selected_college_name');
        $selected_department_name = $this->Session->read('selected_department_name');
        $selected_program_name = $this->Session->read('selected_program_name');
        $selected_program_type_name = $this->Session->read('selected_program_type_name');
        $selected_acdemicyear = $this->Session->read('selected_acdemicyear');

        $this->set(
            compact(
                'selected_college_name',
                'selected_department_name',
                'selected_program_name',
                'selected_program_type_name',
                'selected_acdemicyear'
            )
        );

        $this->layout = null;
        $this->autoLayout = false;
        Configure::write('debug', '0');
    }

    public function print_students_number_pdf()
    {

        $acceptedStudents = $this->Session->read('acceptedStudents');
        $selected_college_name = $this->Session->read('selected_college_name');
        $selected_department_name = $this->Session->read('selected_department_name');
        $selected_campus_name = $this->Session->read('selected_campus_name');
        $selected_program_name = $this->Session->read('selected_program_name');
        $selected_program_type_name = $this->Session->read('selected_program_type_name');
        $selected_acdemicyear = $this->Session->read('selected_acdemicyear');

        $this->set(
            compact(
                'acceptedStudents',
                'selected_campus_name',
                'selected_college_name',
                'selected_department_name',
                'selected_program_name',
                'selected_program_type_name',
                'selected_acdemicyear'
            )
        );

        $this->layout = 'pdf';
        $this->render();
    }

    public function export_students_number_xls()
    {

        $acceptedStudents = $this->Session->read('acceptedStudents');
        $selected_college_name = $this->Session->read('selected_college_name');
        $selected_department_name = $this->Session->read('selected_department_name');
        $selected_campus_name = $this->Session->read('selected_campus_name');
        $selected_program_name = $this->Session->read('selected_program_name');
        $selected_program_type_name = $this->Session->read('selected_program_type_name');
        $selected_acdemicyear = $this->Session->read('selected_acdemicyear');

        $this->set(
            compact(
                'acceptedStudents',
                'selected_campus_name',
                'selected_college_name',
                'selected_department_name',
                'selected_program_name',
                'selected_program_type_name',
                'selected_acdemicyear'
            )
        );
    }

    public function direct_placement()
    {

        if (!empty($this->request->data)) {
            $selectedAcademicYear = null;
            $search = false;

            if (!empty($this->request->data['AcceptedStudent']['academicyear'])) {
                $selectedAcademicYear = $this->request->data['AcceptedStudent']['academicyear'];
                $search = true;
            } else {
                $selectedAcademicYear = $this->AcademicYear->current_academicyear();
            }

            if (!empty($selectedAcademicYear)) {
                // condition to list accepted student of given academic year and college
                if (isset($this->request->data['search'])) {
                    $conditions = array(
                        "AcceptedStudent.college_id" => $this->college_id,
                        "AcceptedStudent.Placement_Approved_By_Department is null"
                    );

                    if (!empty($selectedAcademicYear)) {
                        $conditions[] = "AcceptedStudent.academicyear LIKE '" . $selectedAcademicYear . "%'";
                    }

                    if (!empty($this->request->data['AcceptedStudent']['name'])) {
                        $conditions[] = "AcceptedStudent.first_name LIKE '%" . $this->request->data['AcceptedStudent']['name'] . "%'";
                        $conditions[] = "AcceptedStudent.middle_name LIKE '%" . $this->request->data['AcceptedStudent']['name'] . "%'";
                        $conditions[] = "AcceptedStudent.last_name LIKE '%" . $this->request->data['AcceptedStudent']['name'] . "%'";
                    }

                    if (!empty($this->request->data['AcceptedStudent']['limit'])) {
                        $this->Paginator->settings['limit'] = $this->request->data['AcceptedStudent']['limit'];
                        $this->Paginator->settings['maxLimit'] = $this->request->data['AcceptedStudent']['limit'];
                    } else {
                        $this->Paginator->settings['limit'] = 100;
                        $this->Paginator->settings['maxLimit'] = 100;
                    }

                    $this->Paginator->settings['conditions'] = $conditions;
                    $this->set('acceptedStudents', $this->Paginator->paginate('AcceptedStudent'));

                    $departments = $this->AcceptedStudent->Department->find(
                        'list',
                        array(
                            'conditions' => array(
                                'Department.college_id' => $this->college_id,
                                'Department.active' => 1
                            )
                        )
                    );
                    $programTypes = $this->AcceptedStudent->ProgramType->find('list');
                    $programs = $this->AcceptedStudent->Program->find('list');
                    $this->set(compact('departments', 'programTypes', 'programs'));
                    return;
                }

                debug($this->request->data);

                if (isset($this->request->data['assigndirectly'])) {
                    $directlyplacementstudents = array();

                    if (!empty($this->request->data['AcceptedStudent']['department_id'])) {
                        $department_id = $this->request->data['AcceptedStudent']['department_id'];
                        $this->set('selecteddepartment', $department_id);
                        $counter = 0;

                        $arraycountvalue = array_count_values(
                            $this->request->data['AcceptedStudent']['directplacement']
                        );

                        if (isset($arraycountvalue[1]) && !empty($arraycountvalue[1])) {
                            $check_again_students_not_assigned_by_others = array();

                            if (!empty($this->request->data['AcceptedStudent']['directplacement'])) {
                                foreach ($this->request->data['AcceptedStudent']['directplacement'] as $key => $value) {
                                    if ($value) {
                                        $directlyplacementstudents['AcceptedStudent'][$counter]['id'] = $key;
                                        $directlyplacementstudents['AcceptedStudent'][$counter]['department_id'] = $department_id;
                                        $directlyplacementstudents['AcceptedStudent'][$counter]['placementtype'] = DIRECT_PLACEMENT;
                                        $check_again_students_not_assigned_by_others[] = $key;

                                        $student = $this->AcceptedStudent->find('first', array(
                                            'conditions' => array('AcceptedStudent.id' => $key),
                                            'contain' => array('Student')
                                        ));

                                        $directlyplacementstudents['Student'][$counter]['department_id'] = $department_id;
                                        $directlyplacementstudents['Student'][$counter]['id'] = $student['Student']['id'];
                                        $counter++;
                                    }
                                }
                            }

                            if (!empty($directlyplacementstudents['AcceptedStudent'])) {
                                if ($this->AcceptedStudent->saveAll($directlyplacementstudents['AcceptedStudent'])) {
                                    $conditions = array(
                                        "AcceptedStudent.academicyear LIKE " => $selectedAcademicYear . '%',
                                        "AcceptedStudent.college_id" => $this->college_id,
                                        'AcceptedStudent.placementtype' => DIRECT_PLACEMENT,
                                        "AcceptedStudent.Placement_Approved_By_Department is null"
                                    );

                                    $departmentname = $this->AcceptedStudent->Department->field(
                                        'Department.name',
                                        array('Department.id' => $department_id)
                                    );

                                    if ($this->AcceptedStudent->Student->saveAll(
                                        $directlyplacementstudents['Student'],
                                        array('validate' => false)
                                    )) {
                                    }


                                    /* $this->Paginator->settings['conditions'] = $conditions;
									$acceptedStudents = $this->Paginator->paginate('AcceptedStudent');

									$this->set('acceptedStudents', $acceptedStudents);
									$departmentname = null;

									if (!empty($acceptedStudents)) {
										foreach ($acceptedStudents as $acceptedStudent) {
											$departmentname = $acceptedStudent['Department']['name'];
											break;
										}
									} */

                                    $this->Flash->success(
                                        'The student has been directly placed to ' . $departmentname . ' department.'
                                    );
                                } else {
                                    $this->Flash->error('The direct placement could not be saved. Please, try again.');
                                }
                            }
                        } else {
                            $this->Flash->error(
                                "No student is selected.  Please select atleast one student you want to assign to department."
                            );
                        }
                    } else {
                        $this->Flash->error(
                            "No department is selected. Please select the department you want to assign."
                        );
                    }
                } elseif (isset($this->request->data['transfertodepartment'])) {
                    if ($this->_transferToDepartment($this->request->data) == "NODEPARTMENT") {
                        $this->Flash->error(
                            "No department is selected. Please select the department you want to transfer."
                        );
                        //$this->redirect(array('action'=>'direct_placement'));
                    } elseif ($this->_transferToDepartment($this->request->data) == "NOSTUDENT") {
                        $this->Flash->error(
                            "No student is selected. Please select atleast one student you want to transfer to department."
                        );
                        //$this->redirect(array('action'=>'direct_placement'));
                    } elseif (is_array($this->_transferToDepartment($this->request->data))) {
                        $transfer = $this->_transferToDepartment($this->request->data);

                        if ($this->AcceptedStudent->saveAll($transfer['AcceptedStudent'])) {
                            $conditions = array(
                                "AcceptedStudent.academicyear LIKE " => $selectedAcademicYear . '%',
                                "AcceptedStudent.college_id" => $this->college_id,
                                "AcceptedStudent.Placement_Approved_By_Department is null"
                            );

                            $acceptedStudents = $this->paginate($conditions);
                            //$acceptedStudents=$this->paginate($conditions);
                            $this->set('acceptedStudents', $acceptedStudents);

                            $departmentname = $this->AcceptedStudent->find('first', array(
                                "conditions" => array(
                                    "AcceptedStudent.academicyear LIKE" => $selectedAcademicYear . '%',
                                    "AcceptedStudent.college_id" => $this->college_id,
                                    "AcceptedStudent.department_id" => $transfer['AcceptedStudent'][0]['department_id']
                                )
                            ));


                            if ($this->AcceptedStudent->Student->saveAll(
                                $transfer['Student'],
                                array('validate' => false)
                            )) {
                            }

                            $this->Flash->success(
                                'The student has been transferred to ' . $departmentname['Department']['name'] . ' department '
                            );
                        } else {
                            $this->Flash->error('The direct placement could not be saved. Please, try again.');
                        }
                    }
                } elseif (isset($this->request->data['cancelplacement'])) {
                    if ($this->_cancelPlacement($this->request->data) == "NOSTUDENT") {
                        $this->Flash->error(
                            "No student is selected. Please select atleast one student you want to cancel."
                        );
                        //$this->redirect(array('action'=>'direct_placement'));
                    } elseif (is_array($this->_cancelPlacement($this->request->data))) {
                        $cancelPlacement = $this->_cancelPlacement($this->request->data);

                        if ($this->AcceptedStudent->saveAll($cancelPlacement['AcceptedStudent'])) {
                            $conditions = array(
                                "AcceptedStudent.academicyear LIKE " => $selectedAcademicYear . '%',
                                "AcceptedStudent.college_id" => $this->college_id,
                                "AcceptedStudent.Placement_Approved_By_Department is null"
                            );
                            $acceptedStudents = $this->paginate($conditions);
                            $this->set('acceptedStudents', $acceptedStudents);
                            $this->Flash->success('The student placement is cancelled ');
                        } else {
                            $this->Flash->error('The direct placement could not be saved. Please, try again.');
                        }
                    }
                }
            }

            $this->redirect(array('action' => 'direct_placement', 'page' => $this->passedArgs['page']));
        } else {
            $conditions = array(
                "AcceptedStudent.academicyear LIKE" => $this->AcademicYear->current_academicyear() . '%',
                "AcceptedStudent.college_id" => $this->college_id,
                "AcceptedStudent.Placement_Approved_By_Department is null"
            );

            $this->Paginator->settings['conditions'] = $conditions;
            $this->set('acceptedStudents', $this->Paginator->paginate('AcceptedStudent'));
        }

        $departments = $this->AcceptedStudent->Department->find(
            'list',
            array('conditions' => array('Department.college_id' => $this->college_id, 'Department.active' => 1))
        );

        $programTypes = $this->AcceptedStudent->ProgramType->find('list');
        $programs = $this->AcceptedStudent->Program->find('list');

        $this->set(compact('departments', 'programTypes', 'programs'));
    }

    public function _eligiblestudentforplacement($selectedAcademicYear = null, $college_id = null)
    {

        $checkStudentIsAvailabeForPlacement = $this->AcceptedStudent->find('count', array(
            'conditions' => array(
                "OR" => array(
                    'AcceptedStudent.department_id IS NULL',
                    'AcceptedStudent.department_id = ""',
                    'AcceptedStudent.department_id = 0',
                ),
                "AcceptedStudent.academicyear LIKE" => $selectedAcademicYear . '%',
                "AcceptedStudent.college_id" => $college_id,
                "AcceptedStudent.Placement_Approved_By_Department is null",
                "OR" => array(
                    "AcceptedStudent.placementtype IS NULL",
                    "AcceptedStudent.placementtype" => CANCELLED_PLACEMENT
                )
            )
        ));
        return $checkStudentIsAvailabeForPlacement;
    }

    //Place students automatically
    public function auto_placement()
    {

        if (!empty($this->request->data) && isset($this->request->data['runautoplacement'])) {
            if (!empty($this->request->data['AcceptedStudent']['academicyear'])) {
                $check_auto_placement_already_run = $this->AcceptedStudent->find('count', array(
                    'conditions' => array(
                        'AcceptedStudent.academicyear' => $this->request->data['AcceptedStudent']['academicyear'],
                        'AcceptedStudent.college_id' => $this->college_id,
                        'AcceptedStudent.placementtype' => AUTO_PLACEMENT
                    )
                ));

                if ($check_auto_placement_already_run == 0) {
                    $selectedAcademicYear = $this->request->data['AcceptedStudent']['academicyear'];
                    //check accepted student is imported into the system
                    if ($this->_eligiblestudentforplacement($selectedAcademicYear, $this->college_id)) {
                        //check placement setting is recorded
                        $checkplacementsetting = $this->AcceptedStudent->checkPlacementSettingIsRecorded(
                            $selectedAcademicYear,
                            $this->college_id
                        );

                        if ($checkplacementsetting) {
                            //check preference deadline is not passed
                            if ($this->AcceptedStudent->isPreferenceDeadlinePassed(
                                $selectedAcademicYear,
                                $this->college_id
                            )) {
                                //select prepartory or freshman result
                                $preference_not_completed_percent = $this->_getListOfAcceptedStudentsWithoutPreference(
                                    $selectedAcademicYear,
                                    $this->college_id
                                );
                                //debug($preference_not_completed_percent);

                                $preference_completed_percent = (100 - $preference_not_completed_percent);
                                //debug($preference_completed_percent);

                                //Check that reseved place is defined for all eligible students
                                $reservedPlaces_c = ClassRegistry::init('ReservedPlace')->find('all', array(
                                    'conditions' => array(
                                        'ReservedPlace.college_id' => $this->college_id,
                                        'ReservedPlace.academicyear' => $selectedAcademicYear
                                    ),
                                    'recursive' => -1
                                ));

                                $participatingDepartments_c = ClassRegistry::init('ParticipatingDepartment')->find(
                                    'all',
                                    array(
                                        'conditions' => array(
                                            'ParticipatingDepartment.college_id' => $this->college_id,
                                            'ParticipatingDepartment.academic_year' => $selectedAcademicYear
                                        ),
                                        'recursive' => -1
                                    )
                                );

                                $pd_computational_capacity_sum = 0;
                                $rp_sum = 0;

                                if (!empty($participatingDepartments_c)) {
                                    foreach ($participatingDepartments_c as $pd_value) {
                                        $pd_computational_capacity_sum += ($pd_value['ParticipatingDepartment']['number'] - ($pd_value['ParticipatingDepartment']['female'] + $pd_value['ParticipatingDepartment']['regions'] + $pd_value['ParticipatingDepartment']['disability']));
                                    }
                                }

                                if (!empty($reservedPlaces_c)) {
                                    foreach ($reservedPlaces_c as $rp_value) {
                                        $rp_sum += $rp_value['ReservedPlace']['number'];
                                    }
                                }

                                if ($rp_sum == $pd_computational_capacity_sum) {
                                    if ($preference_completed_percent > 0) {
                                        $isPrepartory = ClassRegistry::init(
                                            'PlacementsResultsCriteria'
                                        )->isPrepartoryResult(
                                            $selectedAcademicYear,
                                            $this->college_id
                                        ); // not used remove it
                                        // $checkPlacementSettingIsRecord['prepartory_result'] = ClassRegistry::init('PlacementsResultsCriteria')->isPrepartoryResult($selectedAcademicYear, $this->college_id);

                                        $autoplacedstudents = array();
                                        //auto placement start
                                        $placementLock = array();
                                        $this->loadModel('PlacementLock');

                                        $placement_lock_id = $this->PlacementLock->find('first', array(
                                            'conditions' => array(
                                                'PlacementLock.college_id' => $this->college_id,
                                                'PlacementLock.academic_year' => $selectedAcademicYear
                                            )
                                        ));

                                        if (!empty($placement_lock_id)) {
                                            $placementLock['PlacementLock']['id'] = $placement_lock_id['PlacementLock']['id'];
                                        } else {
                                            $placementLock['PlacementLock']['id'] = null;
                                        }


                                        $placementLock['PlacementLock']['college_id'] = $this->college_id;
                                        $placementLock['PlacementLock']['academic_year'] = $selectedAcademicYear;
                                        $placementLock['PlacementLock']['process_start'] = 1;
                                        $placementLock['PlacementLock']['start_time'] = date('Y-m-d H:i:s');

                                        $this->PlacementLock->create();
                                        $this->PlacementLock->save($placementLock);
                                        $placement_lock_id = $this->PlacementLock->id;

                                        if ($this->request->data['AcceptedStudent']['priority'] == "high_proprity_for_high_result") {
                                            $priority_high_result = 1;
                                            $priority_first_consider_first = 0;
                                        } else {
                                            if ($this->request->data['AcceptedStudent']['priority'] == "first_consider_first") {
                                                $priority_high_result = 0;
                                                $priority_first_consider_first = 1;
                                            }
                                        }

                                        debug($this->request->data['AcceptedStudent']);

                                        // if no quota is given run the parallel placement
                                        if ($this->AcceptedStudent->detect_privilaged_qutoa_presence(
                                                $selectedAcademicYear,
                                                $this->college_id
                                            ) == 0) {
                                            $autoplacedstudents = $this->AcceptedStudent->auto_parallel_assignment(
                                                $selectedAcademicYear,
                                                $this->college_id,
                                                $isPrepartory
                                            );
                                        } else {
                                            // if  quota is given run the sequential placement
                                            // run sequential placement if there is quota.
                                            debug($this->request->data['AcceptedStudent']);
                                            $autoplacedstudents = $this->AcceptedStudent->auto_placement_algorithm(
                                                $selectedAcademicYear,
                                                $this->college_id,
                                                $isPrepartory,
                                                $priority_high_result,
                                                $priority_first_consider_first
                                            );
                                            debug($autoplacedstudents);
                                        }
                                        //auto placement end
                                        $select_placement_lock = $this->PlacementLock->read(null, $placement_lock_id);

                                        $select_placement_lock['PlacementLock']['end_time'] = date('Y-m-d H:i:s');
                                        $select_placement_lock['PlacementLock']['process_start'] = 0;
                                        $this->PlacementLock->save($select_placement_lock);

                                        if (!empty($autoplacedstudents)) {
                                            $college_name = $this->AcceptedStudent->College->field(
                                                'College.name',
                                                array('College.id ' => $this->college_id)
                                            );
                                            $this->Flash->success(
                                                'Auto placement result for ' . $selectedAcademicYear . ' academic year of ' . $college_name . '.'
                                            );
                                            //record the auto placement to the lock database
                                            $auto_already_run = true;
                                            $this->set(compact('autoplacedstudents', 'auto_already_run'));
                                            $this->Session->write('autoplacedstudents', $autoplacedstudents);
                                            $this->Session->write('selected_academic_year', $selectedAcademicYear);
                                        }
                                    } else {
                                        $this->Flash->error(
                                            'You can not run auto placement. ' . $preference_not_completed_percent . ' % of students has not completed their preference.'
                                        );
                                        $this->redirect(array('controller' => 'preferences', 'action' => 'add'));
                                    }
                                } else {
                                    //If reseved place is not defined for all available eligible students
                                    $this->Flash->error(
                                        'There is some inconsistency with the department quota and reserved place for each department. Please go to "<u>Add/Edit Reserved Place For Department</u>" section and adjust before you run the auto placement.'
                                    );
                                }
                            } else {
                                $error = $this->AcceptedStudent->invalidFields();
                                if (isset($error['preferencedeadline'])) {
                                    $this->Flash->error($error['preferencedeadline'][0]);
                                }
                                //$this->redirect(array('controller'=>'PreferenceDeadlines','action'=>'index'));
                            }
                        } else {
                            $error = $this->AcceptedStudent->invalidFields();

                            if (isset($error['reserved_place'][0])) {
                                $this->Flash->error($error['reserved_place'][0]);
                                $this->redirect(array('controller' => 'reservedPlaces', 'action' => 'add'));
                            } elseif (isset($error['placement_result_criteria'])) {
                                $this->Flash->error($error['placement_result_criteria'][0]);
                                $this->redirect(array('controller' => 'placementsResultsCriterias', 'action' => 'add'));
                            } elseif (isset($error['participating_department'])) {
                                $this->Flash->setFlash($error['participating_department'][0]);
                                $this->redirect(
                                    array('controller' => 'participatingDepartments', 'action' => 'add_quota')
                                );
                            } else {
                                $this->Flash->error('Please fill the input fields');
                            }
                        }
                    } else {
                        $this->Flash->error(
                            'There is no student for the selected academic year that needs auto placement.This happens if there is no student for the given academic year or all students are auto or directly placed to department.'
                        );
                        $this->redirect(array('controller' => 'placement', 'action' => 'index'));
                    }
                } else {
                    $this->Flash->error(
                        'You have already run an auto placement for ' . $this->request->data['AcceptedStudent']['academicyear'] . ' academic year. In order to run again you have to cancell the previous auto placement first.'
                    );
                    $this->redirect(array('action' => 'cancel_auto_placement'));
                }
            } else {
                $this->Flash->error('Please select academic year to run the auto placement');
            }
        }
    }

    public function __manual_placement()
    {

        if (!empty($this->request->data)) {
            //debug($this->request->data);
            if (!empty($this->request->data['AcceptedStudent']['academicyear'])) {
                $selectedAcademicYear = $this->request->data['AcceptedStudent']['academicyear'];

                $conditions = array(
                    "AcceptedStudent.academicyear LIKE" => $selectedAcademicYear . '%',
                    "AcceptedStudent.college_id" => $this->college_id,
                    'AcceptedStudent.placementtype' => AUTO_PLACEMENT,
                    "AcceptedStudent.Placement_Approved_By_Department is null"
                );

                $acceptedStudents = $this->paginate($conditions);
                $this->set('acceptedStudents', $acceptedStudents);

                if (isset($this->request->data['cancelplacement'])) {
                    foreach ($this->request->data['AcceptedStudent'] as $k => &$v) {
                        $v['department_id'] = 0;
                        $v['placementtype'] = CANCELLED_PLACEMENT;
                    }

                    if ($this->AcceptedStudent->saveAll($this->request->data['AcceptedStudent'])) {
                        $conditions = array(
                            "AcceptedStudent.academicyear LIKE" => $selectedAcademicYear . '%',
                            "AcceptedStudent.college_id" => $this->college_id,
                            'AcceptedStudent.placementtype' => CANCELLED_PLACEMENT
                        );

                        $acceptedStudents = $this->paginate($conditions);

                        $this->set('acceptedStudents', $acceptedStudents);
                        $this->Flash->success(
                            'The auto placement for all students of ' . $this->college_name . ' for ' . $selectedAcademicYear . ' academic year has been cancelled. Please rerun the auto placement to assign students to departments or use direct assignment to assign to department.'
                        );
                    } else {
                        $this->Flash->error('The auto placement couldn\'t be cancelled. Please, try again.');
                    }
                }

                $this->set('selected_academic_year', true);
            } else {
                $this->Flash->error('Please select academic year');
            }
        }
    }

    public function cancel_auto_placement()
    {

        //$this->paginate = array('limit'=>500000);
        if (!empty($this->request->data) && isset($this->request->data['search'])) {
            if (!empty($this->request->data['Search']['academicyear'])) {
                $limit = $this->request->data['Search']['limit'];
                //$this->paginate['Limit']=;
                $selectedAcademicYear = $this->request->data['Search']['academicyear'];

                $conditions = array(
                    "AcceptedStudent.academicyear LIKE" => $selectedAcademicYear . '%',
                    "AcceptedStudent.college_id" => $this->college_id,
                    'AcceptedStudent.placementtype' => AUTO_PLACEMENT,
                    'OR' => array(
                        'AcceptedStudent.program_type_id' => PROGRAM_TYPE_REGULAR,
                        'AcceptedStudent.program_type_id' => PROGRAM_TYPE_ADVANCE_STANDING,
                    ),
                    'AcceptedStudent.program_id' => PROGRAM_UNDEGRADUATE,
                    "AcceptedStudent.Placement_Approved_By_Department is null"
                );

                $this->paginate = array('limit' => $limit, 'maxLimit' => $limit);
                $this->paginate['conditions'] = $conditions;
                $this->Paginator->settings = $this->paginate;

                $acceptedStudents = $this->Paginator->paginate('AcceptedStudent');

                $this->set('acceptedStudents', $acceptedStudents);
                $this->set('selected_academic_year', true);
            } else {
                $this->Flash->error('Please select academic year');
            }
        }

        if (!empty($this->request->data) && isset($this->request->data['cancelplacement'])) {
            $limit = $this->request->data['Search']['limit'];
            $selected_academic_year = $this->request->data['Search']['academicyear'];

            /* foreach ($this->request->data['AcceptedStudent'] as $k => &$v) {
				$v['minute_number'] = NULL;
				$v['department_id'] = NULL;
				$v['placementtype'] = CANCELLED_PLACEMENT;
				$selected_academic_year = $v['academicyear'];
				//The following break is used as the code is replaced by the following code and we need it to be excuted once to get academic year
				break;
			} */

            $conditions = array(
                "AcceptedStudent.academicyear" => $selected_academic_year,
                "AcceptedStudent.college_id" => $this->college_id,
                'AcceptedStudent.placementtype' => AUTO_PLACEMENT,
                'OR' => array(
                    'AcceptedStudent.program_type_id' => PROGRAM_TYPE_REGULAR,
                    'AcceptedStudent.program_type_id' => PROGRAM_TYPE_ADVANCE_STANDING,
                ),
                'AcceptedStudent.program_id' => PROGRAM_UNDEGRADUATE,
                "AcceptedStudent.Placement_Approved_By_Department is null"
            );

            $this->paginate = array('limit' => $limit, 'maxLimit' => $limit);
            $this->paginate['conditions'] = $conditions;
            $this->Paginator->settings = $this->paginate;

            $acceptedStudents = $this->Paginator->paginate('AcceptedStudent');

            $placement_cancelation_list = array();

            if (!empty($acceptedStudents)) {
                foreach ($acceptedStudents as $acceptedStudent) {
                    $index = count($placement_cancelation_list);
                    $placement_cancelation_list[$index]['id'] = $acceptedStudent['AcceptedStudent']['id'];
                    $placement_cancelation_list[$index]['placementtype'] = CANCELLED_PLACEMENT;
                    $placement_cancelation_list[$index]['minute_number'] = null;
                    $placement_cancelation_list[$index]['department_id'] = null;
                }
            }

            if ($this->AcceptedStudent->saveAll($placement_cancelation_list)) {
                //The following code is replaced by the above as a solution for the limitation on the number of post fields
                //if($this->AcceptedStudent->saveAll($this->request->data['AcceptedStudent'])) {

                $conditions = array(
                    "AcceptedStudent.academicyear LIKE" => $selected_academic_year . '%',
                    "AcceptedStudent.college_id" => $this->college_id,
                    'AcceptedStudent.placementtype' => CANCELLED_PLACEMENT
                );

                $acceptedStudents = $this->paginate($conditions);
                $this->set('acceptedStudents', $acceptedStudents);
                $this->set('selected_academic_year', true);
                $this->set('hide_button', true);

                $college_name = $this->AcceptedStudent->College->field(
                    'College.name',
                    array('College.id ' => $this->college_id)
                );
                $this->Flash->success(
                    'The auto placement for all students of ' . $college_name . ' for ' . $selected_academic_year . ' academic year has been cancelled. Please re-run the auto placement to assign students to departments or use direct assignment to department.'
                );
            } else {
                $this->Flash->error('The auto placement couldn\'t be cancelled. Please, try again.');
            }
        }
    }


    public function _transferToDepartment($data = null)
    {

        $transferToDepartment = array();

        if (!empty($data['AcceptedStudent']['department_id'])) {
            $department_id = $data['AcceptedStudent']['department_id'];
            $counter = 0;

            $arraycountvalue = array_count_values($this->request->data['AcceptedStudent']['directplacement']);

            if (isset($arraycountvalue[1]) && !empty($arraycountvalue[1])) {
                foreach ($this->request->data['AcceptedStudent']['directplacement'] as $key => $value) {
                    if ($value) {
                        $student = $this->AcceptedStudent->find('first', array(
                            'conditions' => array('AcceptedStudent.id' => $key),
                            'contain' => array('Student')
                        ));

                        $transferToDepartment['AcceptedStudent'][$counter]['id'] = $key;
                        $transferToDepartment['AcceptedStudent'][$counter]['department_id'] = $department_id;
                        $transferToDepartment['AcceptedStudent'][$counter]['placementtype'] = DIRECT_PLACEMENT;

                        $transferToDepartment['Student'][$counter]['department_id'] = $department_id;
                        $transferToDepartment['Student'][$counter]['id'] = $student['Student']['id'];

                        $counter++;
                    }
                }
                return $transferToDepartment;
            } else {
                return "NOSTUDENT";
            }
        } else {
            return "NODEPARTMENT";
        }
    }

    public function _cancelPlacement($data = null)
    {

        $cancelledplacement = array();
        // $department_id = $data['AcceptedStudent']['department_id'];
        $counter = 0;

        $arraycountvalue = array_count_values($this->request->data['AcceptedStudent']['directplacement']);

        if (isset($arraycountvalue[1]) && !empty($arraycountvalue[1])) {
            foreach ($this->request->data['AcceptedStudent']['directplacement'] as $key => $value) {
                if ($value) {
                    $cancelledplacement['AcceptedStudent'][$counter]['id'] = $key;
                    $cancelledplacement['AcceptedStudent'][$counter]['department_id'] = null;
                    $cancelledplacement['AcceptedStudent'][$counter]['placementtype'] = CANCELLED_PLACEMENT;
                    $counter++;
                }
            }
            return $cancelledplacement;
        } else {
            return "NOSTUDENT";
        }
    }

    public function auto_fill_preference($academicyear = '2009/10')
    {

        $accepted_students = $this->AcceptedStudent->find('all', array(
            'conditions' => array(
                'AcceptedStudent.college_id' => $this->college_id,
                'AcceptedStudent.academicyear LIKE' => $academicyear
            ),
            'recursive' => '-1'
        ));

        $detail_of_participating_department = ClassRegistry::init('ParticipatingDepartment')->find('all', array(
            'conditions' => array(
                'ParticipatingDepartment.college_id' => $this->college_id,
                'ParticipatingDepartment.academic_year' => $academicyear
            ),
            'recursive' => '-1'
        ));

        $number_of_participating_department = count($detail_of_participating_department);
        //debug($detail_of_participating_department);
        //debug($data);
        $departments = array();

        if (!empty($detail_of_participating_department)) {
            foreach ($detail_of_participating_department as $key => $participating_department) {
                //debug($participating_department['ParticipatingDepartment']['department_id']);
                array_push($departments, $participating_department['ParticipatingDepartment']['department_id']);
            }
        }

        //debug($departments);
        $count = 0;
        $preference_selection = array();

        if (!empty($accepted_students)) {
            foreach ($accepted_students as $key => $accepted_student) {
                $filled = $this->AcceptedStudent->Preference->find(
                    'count',
                    array('conditions' => array('Preference.accepted_student_id' => $accepted_student['AcceptedStudent']['id']))
                );

                if ($filled <= 0) {
                    shuffle($departments);
                    for ($i = 1; $i <= count($departments); $i++) {
                        $preference_selection[$count]['accepted_student_id'] = $accepted_student['AcceptedStudent']['id'];
                        $preference_selection[$count]['academicyear'] = $accepted_student['AcceptedStudent']['academicyear'];
                        $preference_selection[$count]['college_id'] = $this->college_id;
                        $preference_selection[$count]['department_id'] = $departments[$i - 1];
                        $preference_selection[$count]['preferences_order'] = $i;
                        $count++;
                    }
                }
            }
        }

        if (!empty($preference_selection)) {
            $this->AcceptedStudent->Preference->saveAll($preference_selection);
            $this->Flash->success('Preference is automatically filled.');
        } else {
            $this->Flash->error('Preference auto-filling failed. Coud\'t find students');
        }

        return $this->redirect(array('controller' => 'preferences', 'action' => 'index'));
        /*
		$data = $this->AcceptedStudent->find('all', array(
			'conditions' => array(
				'AcceptedStudent.college_id' => $this->college_id,
				'AcceptedStudent.academicyear LIKE' => $academicyear
			)
		));

		$detail_of_participating_department = ClassRegistry::init('ParticipatingDepartment')->find('all', array(
			'conditions' => array(
				'ParticipatingDepartment.college_id' => $this->college_id,
				'ParticipatingDepartment.academic_year' => $academicyear
			)
		));

		$number_of_participating_department = count($detail_of_participating_department);
		debug($detail_of_participating_department);
		debug($data);

		exit();
		$preference = array();

		if (!empty($detail_of_participating_department)) {

			$preference_random = array('numberOfVariables' => $count($detail_of_participating_department));
			$preference_order = array();

			// Loop through our range of variables and set a random number for each one.
			foreach (range(1, $preference_random['numberOfVariables']) as $variable) {
				$preference_order[] = rand(1, count($detail_of_participating_department));
				//check uniquiness of each array
			}

			if (!empty($data)) {
				foreach ($data as $key => $value) {
					foreach ($detail_of_participating_department as $k => $v) {
						//accepted_student_id	academicyear	college_id	department_id	preferences_order
						$randompreferenceorder = rand(1, $number_of_participating_department);
						$preference_order = 1;

						if ($key > 0) {
							$preference_order = $preference['Preference'][$key - 1]['preferences_order'] != $randompreferenceorder ? $randompreferenceorder : rand(1, $number_of_participating_department);
						}

						$preference['Preference'][$key]['accepted_student_id'] = $value['AcceptedStudent']['id'];
						$preference['Preference'][$key]['academicyear'] = $academicyear;
						$preference['Preference'][$key]['department_id'] = $v['ParticipatingDepartment']['department_id'];
						$preference['Preference'][$key]['college_id'] = $v['ParticipatingDepartment']['college_id'];
						$preference['Preference'][$key]['preferences_order'] = $preference_order;
					}
				}
			}
		}
		*/
    }

    public function import_newly_students()
    {

        $regions = $this->AcceptedStudent->Region->find('list');

        $colleges = $this->AcceptedStudent->College->find('list', array('conditions' => array('College.active' => 1)));
        $departments = $this->AcceptedStudent->Department->find(
            'list',
            array('conditions' => array('Department.active' => 1))
        );

        $programs = $this->AcceptedStudent->Program->find('list', array('conditions' => array('Program.active' => 1)));
        $program_types = $programTypes = $this->AcceptedStudent->ProgramType->find('list');

        $departments_organized_by_college = $this->AcceptedStudent->College->find('all', array(
            'conditions' => array('College.active' => 1),
            'contain' => array(
                'Department' => array(
                    'conditions' => array('Department.active' => 1),
                    'fields' => array('id', 'name'),
                )
            ),
            'fields' => array('id', 'name'),
        ));

        $return = array();

        if (!empty($departments_organized_by_college)) {
            foreach ($departments_organized_by_college as $dep_id => $dep_name) {
                if (!empty($dep_name['Department'])) {
                    foreach ($dep_name['Department'] as $k => $v) {
                        $return[$dep_name['College']['name']][$v['id']] = $v['name'];
                    }
                } else {
                    $return[$dep_name['College']['name']][$dep_name['College']['id']] = $dep_name['College']['name'];
                }
            }
        }

        $departments_organized_by_college = $return;

        $this->set(
            compact(
                'regions',
                'colleges',
                'departments',
                'programs',
                'programTypes',
                'departments_organized_by_college'
            )
        );

        if (!empty($this->request->data) && is_uploaded_file(
                $this->request->data['AcceptedStudent']['File']['tmp_name']
            )) {
            //check the file type before doing the fucken manipulations.

            if (strcasecmp($this->request->data['AcceptedStudent']['File']['type'], 'application/vnd.ms-excel')) {
                $this->Flash->error(
                    'Importing Error!!. Please  save your excel file as "Excel 97-2003 Workbook" type while you saved the file and import again. Try also to use other 97-2003 file types if you are using office 2010 or recent versions. Current file format is: ' . $this->request->data['AcceptedStudent']['File']['type']
                );
                return;
            }

            $data = new Spreadsheet_Excel_Reader();
            // Set output Encoding.
            $data->setOutputEncoding('CP1251');

            $data->read($this->request->data['AcceptedStudent']['File']['tmp_name']);

            $headings = array();
            $xls_data = array();

            //check without department
            //TODO: Remove studentnumber

            if (INCLUDE_STUDENT_NUMBER_IN_IMPORT_TEMPLATE_FILE) {
                $required_fields = array(
                    'first_name',
                    'middle_name',
                    'last_name',
                    'EHEECE_total_results',
                    'program',
                    'program_type',
                    'college',
                    'region',
                    'department',
                    'sex',
                    'studentnumber',
                    'high_school',
                    'benefit_group',
                    'moeadmissionnumber'
                );
            } else {
                $required_fields = array(
                    'first_name',
                    'middle_name',
                    'last_name',
                    'EHEECE_total_results',
                    'program',
                    'program_type',
                    'college',
                    'region',
                    'department',
                    'sex',
                    'high_school',
                    'benefit_group',
                    'moeadmissionnumber'
                );
            }

            $non_existing_field = array();
            $non_valide_rows = array();
            $deptIdsNotification = array();
            $collIdsNotification = array();

            if (empty($data->sheets[0]['cells'])) {
                $this->Flash->error('Importing Error!!. The excel file you uploaded is empty.');
                return;
            }

            if (empty($data->sheets[0]['cells'][1])) {
                $this->Flash->error(
                    'Importing Error!!. Please insert your filed name (first_name,  middle_name, last_name, EHEECE_total_results, program_type, college, region, sex)  at first row of your excel file.'
                );
                return;
            }

            if (count($required_fields)) {
                for ($k = 0; $k < count($required_fields); $k++) {
                    if (in_array($required_fields[$k], $data->sheets[0]['cells'][1]) === false) {
                        $non_existing_field[] = $required_fields[$k];
                    }
                }
            }

            if (count($non_existing_field) > 0) {
                $field_list = "";
                foreach ($non_existing_field as $k => $v) {
                    $field_list .= ($v . ", ");
                }
                $field_list = substr($field_list, 0, (strlen($field_list) - 2));
                $this->Flash->error(
                    'Importing Error!!. ' . $field_list . ' is/are required in the excel file you imported at first row.'
                );
                return;
            } else {
                if (!empty($colleges)) {
                    foreach ($colleges as $k => $v) {
                        $colleges[$k] = strtoupper(trim($v));
                    }
                }

                if (!empty($program_types)) {
                    foreach ($program_types as $k => $v) {
                        $program_types[$k] = strtoupper(trim($v));
                    }
                }

                if (!empty($regions)) {
                    foreach ($regions as $k => $v) {
                        $regions[$k] = strtoupper(trim($v));
                    }
                }

                if (!empty($programs)) {
                    foreach ($programs as $k => $v) {
                        $programs[$k] = strtoupper(trim($v));
                    }
                }

                if (!empty($departments)) {
                    foreach ($departments as $k => $v) {
                        $departments[$k] = strtoupper(trim($v));
                    }
                }

                $fields_name_acceptedStudents_table = $data->sheets[0]['cells'][1];
                $program_type_is_different_from_regular = 0;
                $check_program_is_different_from_undergraduate = 0;
                $duplicated_student_number = array();

                if ($data->sheets[0]['numRows']) {
                    for ($i = 2; $i <= $data->sheets[0]['numRows']; $i++) {
                        $row_data = array();
                        $name_error_duplicate = false;

                        $program_type_is_different_from_regular = 0;
                        $check_program_is_different_from_undergraduate = 0;
                        $department_against_college = 0;

                        //debug($data->sheets[0]['cells'][$i]);

                        for ($j = 1; $j <= count($fields_name_acceptedStudents_table); $j++) {
                            //column of data
                            //check the possible value of each record, match with the accepted students and inform the user to modify it and import it again
                            //check department belongs to college for each row

                            if ($fields_name_acceptedStudents_table[$j] == "college" && in_array(
                                    trim($data->sheets[0]['cells'][$i][$j]),
                                    $colleges
                                ) == 0) {
                                $department_against_college = array_search(
                                    strtoupper(trim($data->sheets[0]['cells'][$i][$j])),
                                    $colleges
                                );
                            }

                            //check student number is given and populate with value
                            if (INCLUDE_STUDENT_NUMBER_IN_IMPORT_TEMPLATE_FILE) {
                                if ($fields_name_acceptedStudents_table[$j] == "studentnumber" && !empty($data->sheets[0]['cells'][$i][$j])) {
                                    $currentStudentNumber = trim($data->sheets[0]['cells'][$i][$j]);

                                    if (isset($currentStudentNumber) && !empty($currentStudentNumber)) {
                                        $duplicated_student_number[$currentStudentNumber] = isset($duplicated_student_number[$currentStudentNumber]) ? $duplicated_student_number[$currentStudentNumber] : 0 + 1;
                                        if (isset($duplicated_student_number[$currentStudentNumber]) && $duplicated_student_number[$currentStudentNumber] > 1) {
                                            $non_valide_rows[] = "Duplicated student number at row " . $i;
                                            continue;
                                        }
                                    } else {
                                        $duplicated_student_number[$currentStudentNumber] = 0;
                                    }
                                }
                            }

                            //check program type for each row is different from regular and inform user to enter deparment.
                            if ($fields_name_acceptedStudents_table[$j] == "program_type" && (!isset($data->sheets[0]['cells'][$i][$j]) || (strcasecmp(
                                            trim($data->sheets[0]['cells'][$i][$j]),
                                            'Regular'
                                        ) != 0 && strcasecmp(
                                            trim($data->sheets[0]['cells'][$i][$j]),
                                            'Advance Standing'
                                        ) != 0))) {
                                $program_type_is_different_from_regular = $i;
                            }

                            //check program for each row is different from undergraduate inform the user no need to save EHEECE_total_results
                            if (strcasecmp(
                                    $fields_name_acceptedStudents_table[$j],
                                    "program"
                                ) == 0 && (!isset($data->sheets[0]['cells'][$i][$j]) || (strcasecmp(
                                            trim($data->sheets[0]['cells'][$i][$j]),
                                            'Undergraduate'
                                        ) != 0 && strcasecmp(
                                            trim($data->sheets[0]['cells'][$i][$j]),
                                            'Remedial'
                                        ) != 0))) {
                                $check_program_is_different_from_undergraduate = $i;
                            }

                            // Check if the program type is different from regular and make department mandatory field.
                            if ($program_type_is_different_from_regular == $i || $check_program_is_different_from_undergraduate == $i) {
                                if (strcasecmp(
                                        $fields_name_acceptedStudents_table[$j],
                                        "department"
                                    ) == 0 && (!isset($data->sheets[0]['cells'][$i][$j]) || !in_array(
                                            strtoupper(trim($data->sheets[0]['cells'][$i][$j])),
                                            $departments
                                        ))) {
                                    $non_valide_rows[] = "Please enter a valid department at row " . $i;
                                    continue;
                                } else {
                                    if (strcasecmp(
                                            $fields_name_acceptedStudents_table[$j],
                                            "department"
                                        ) == 0 && isset($data->sheets[0]['cells'][$i][$j]) && !empty($data->sheets[0]['cells'][$i][$j])) {
                                        // is department belongs the selected college ?
                                        $department_id = array_search(
                                            strtoupper(trim($data->sheets[0]['cells'][$i][$j])),
                                            $departments
                                        );
                                        $your_college_id = $this->AcceptedStudent->Department->field(
                                            'college_id',
                                            array('Department.id' => $department_id)
                                        );

                                        if ($your_college_id != $department_against_college) {
                                            $non_valide_rows[] = "The department entered does not belong to the college entered at row " . $i;
                                            continue;
                                        }
                                    }
                                }

                                if ($fields_name_acceptedStudents_table[$j] == "EHEECE_total_results" && isset($data->sheets[0]['cells'][$i][$j]) && $data->sheets[0]['cells'][$i][$j] != "" && !is_numeric(
                                        $data->sheets[0]['cells'][$i][$j]
                                    )) {
                                    //TODO: Uncomment the following two lines
                                    $non_valide_rows[] = "Please enter a valid EHEECE result at row " . $i;
                                    continue;
                                }
                            } else {
                                if (strcasecmp(
                                        $fields_name_acceptedStudents_table[$j],
                                        "department"
                                    ) == 0 && isset($data->sheets[0]['cells'][$i][$j]) && !empty($data->sheets[0]['cells'][$i][$j])) {
                                    if (!in_array(strtoupper(trim($data->sheets[0]['cells'][$i][$j])), $departments)) {
                                        $non_valide_rows[] = "Please enter a valid department at row " . $i;
                                        continue;
                                    }

                                    // is department belongs the selected college ?
                                    $department_id = array_search(
                                        strtoupper(trim($data->sheets[0]['cells'][$i][$j])),
                                        $departments
                                    );
                                    $your_college_id = $this->AcceptedStudent->Department->field(
                                        'college_id',
                                        array('Department.id' => $department_id)
                                    );

                                    if ($your_college_id != $department_against_college) {
                                        $non_valide_rows[] = "The department entered does not belong to the college entered at row " . $i;
                                        continue;
                                    }
                                }
                            }

                            if ($program_type_is_different_from_regular != $i && $check_program_is_different_from_undergraduate != $i) {
                                if ($fields_name_acceptedStudents_table[$j] == "EHEECE_total_results" && (!isset($data->sheets[0]['cells'][$i][$j]) || (trim(
                                                $data->sheets[0]['cells'][$i][$j]
                                            ) == "" || !is_numeric($data->sheets[0]['cells'][$i][$j])))) {
                                    $non_valide_rows[] = "Please enter a valid EHEECE result at row " . $i;
                                    continue;
                                }
                            }

                            if (strcasecmp(
                                    $fields_name_acceptedStudents_table[$j],
                                    "sex"
                                ) == 0 && (!isset($data->sheets[0]['cells'][$i][$j]) || !(strcasecmp(
                                            trim($data->sheets[0]['cells'][$i][$j]),
                                            'M'
                                        ) || strcasecmp($data->sheets[0]['cells'][$i][$j], 'F') || strcasecmp(
                                            trim($data->sheets[0]['cells'][$i][$j]),
                                            'Male'
                                        ) || strcasecmp($data->sheets[0]['cells'][$i][$j], 'Female')))) {
                                $non_valide_rows[] = "Invalid sex entry at row " . $i;
                                continue;
                            }


                            if (!$name_error_duplicate && in_array(
                                    $fields_name_acceptedStudents_table[$j],
                                    array('first_name', 'middle_name', 'last_name')
                                ) && (!isset($data->sheets[0]['cells'][$i][$j]) || is_null(
                                        trim($data->sheets[0]['cells'][$i][$j])
                                    ) || empty(trim($data->sheets[0]['cells'][$i][$j])))) {
                                $non_valide_rows[] = "Either one or all of the name field(s) is/are emplty, Please check and correct first, middle or last name at row number " . $i;
                                $name_error_duplicate = true;
                                continue;
                            }

                            if ($fields_name_acceptedStudents_table[$j] == "college" && (!isset($data->sheets[0]['cells'][$i][$j]) || !in_array(
                                        strtoupper(trim($data->sheets[0]['cells'][$i][$j])),
                                        $colleges
                                    ))) {
                                $non_valide_rows[] = "Please enter a valid college name at row " . $i;
                                continue;
                            }

                            if (strcasecmp(
                                    $fields_name_acceptedStudents_table[$j],
                                    "program_type"
                                ) == 0 && (!isset($data->sheets[0]['cells'][$i][$j]) || !in_array(
                                        strtoupper(trim($data->sheets[0]['cells'][$i][$j])),
                                        $program_types
                                    ))) {
                                $non_valide_rows[] = "Please enter a valid program type at row " . $i;
                                continue;
                            }

                            if (strcasecmp(
                                    $fields_name_acceptedStudents_table[$j],
                                    "region"
                                ) == 0 && (!isset($data->sheets[0]['cells'][$i][$j]) || !in_array(
                                        strtoupper(trim($data->sheets[0]['cells'][$i][$j])),
                                        $regions
                                    ))) {
                                $non_valide_rows[] = "Please enter a valid region at row " . $i;
                                continue;
                            }

                            if (strcasecmp(
                                    $fields_name_acceptedStudents_table[$j],
                                    "program"
                                ) == 0 && (!isset($data->sheets[0]['cells'][$i][$j]) || !in_array(
                                        strtoupper(trim($data->sheets[0]['cells'][$i][$j])),
                                        $programs
                                    ))) {
                                $non_valide_rows[] = "Please enter a valid program at row " . $i;
                                continue;
                            }

                            if (in_array($fields_name_acceptedStudents_table[$j], $required_fields)) {
                                if ($fields_name_acceptedStudents_table[$j] == "college") {
                                    $college_id = array_search(
                                        strtoupper(trim($data->sheets[0]['cells'][$i][$j])),
                                        $colleges
                                    );
                                    $clgDetail = $this->AcceptedStudent->College->find(
                                        'first',
                                        array('conditions' => array('College.id' => $college_id), 'recursive' => -1)
                                    );

                                    $row_data['college_id'] = $college_id;
                                    $row_data['original_college_id'] = $college_id;
                                    $row_data['campus_id'] = $clgDetail['College']['campus_id'];

                                    $collIdsNotification[] = $college_id;
                                } else {
                                    if (strcasecmp($fields_name_acceptedStudents_table[$j], "department") == 0) {
                                        if (isset($data->sheets[0]['cells'][$i][$j]) && $data->sheets[0]['cells'][$i][$j] != "") {
                                            $department_id = array_search(
                                                strtoupper(trim($data->sheets[0]['cells'][$i][$j])),
                                                $departments
                                            );
                                            $row_data['department_id'] = $department_id;

                                            $deptIdsNotification[] = $department_id;
                                        }
                                    } else {
                                        if (strcasecmp($fields_name_acceptedStudents_table[$j], "region") == 0) {
                                            $region_id = array_search(
                                                strtoupper(trim($data->sheets[0]['cells'][$i][$j])),
                                                $regions
                                            );
                                            $row_data['region_id'] = $region_id;
                                        } else {
                                            if ($fields_name_acceptedStudents_table[$j] == "program_type") {
                                                $program_type_id = array_search(
                                                    strtoupper(trim($data->sheets[0]['cells'][$i][$j])),
                                                    $program_types
                                                );
                                                $row_data['program_type_id'] = $program_type_id;
                                            } else {
                                                if (strcasecmp(
                                                        $fields_name_acceptedStudents_table[$j],
                                                        "program"
                                                    ) == 0) {
                                                    $program_id = array_search(
                                                        strtoupper(trim($data->sheets[0]['cells'][$i][$j])),
                                                        $programs
                                                    );
                                                    $row_data['program_id'] = $program_id;
                                                } else {
                                                    $row_data[$fields_name_acceptedStudents_table[$j]] = isset($data->sheets[0]['cells'][$i][$j]) ? $data->sheets[0]['cells'][$i][$j] : '';
                                                }
                                            }
                                        }
                                    }
                                }
                            }
                        }

                        $selectedAcademicyear = $this->request->data['AcceptedStudent']['academicyear'];
                        $row_data['academicyear'] = $selectedAcademicyear;

                        if (isset($row_data['sex']) && (strcasecmp(trim($row_data['sex']), 'M') == 0 || strcasecmp(
                                    trim($row_data['sex']),
                                    'Male'
                                ) == 0)) {
                            $row_data['sex'] = 'Male';
                        } else {
                            if (isset($row_data['sex']) && (strcasecmp(trim($row_data['sex']), 'F') == 0 || strcasecmp(
                                        trim($row_data['sex']),
                                        'Female'
                                    ) == 0)) {
                                $row_data['sex'] = 'Female';
                            } else {
                                $row_data['sex'] = trim($row_data['sex']);
                            }
                        }

                        // to prevent possible duplicated names in the excel file itself
                        $studentUniqueFields = array();

                        $studentUniqueFields[0] = $row_data['first_name'] = trim(
                            ucfirst(strtolower($row_data['first_name']))
                        );
                        $studentUniqueFields[1] = $row_data['middle_name'] = trim(
                            ucfirst(strtolower($row_data['middle_name']))
                        );
                        $studentUniqueFields[2] = $row_data['last_name'] = trim(
                            ucfirst(strtolower($row_data['last_name']))
                        );
                        $studentUniqueFields[3] = $row_data['sex'];
                        $studentUniqueFields[4] = $row_data['college_id'];
                        $studentUniqueFields[5] = $row_data['program_id'];
                        $studentUniqueFields[6] = $row_data['program_type_id'];


                        if (INCLUDE_STUDENT_NUMBER_IN_IMPORT_TEMPLATE_FILE) {
                            if (!empty($row_data['studentnumber'])) {
                                $student_number_depulicated = $this->AcceptedStudent->find(
                                    'count',
                                    array(
                                        'conditions' => array(
                                            'AcceptedStudent.studentnumber' => trim(
                                                $row_data['studentnumber']
                                            )
                                        ),
                                        'recursive' => -1
                                    )
                                );
                                if (!empty($student_number_depulicated)) {
                                    $non_valide_rows[] = "The student number '" . $row_data['studentnumber'] . "' at row " . $i . " already imported or existes in the system. Please remove it from the excel.";
                                }
                            }
                        }

                        //debug($row_data);
                        $is_duplicated = $this->AcceptedStudent->find(
                            'count',
                            array('conditions' => $row_data, 'recursive' => -1)
                        );

                        //debug($is_duplicated);
                        if ($is_duplicated > 0) {
                            $non_valide_rows[] = "The data at row " . $i . " is already imported or exists in the system. Please remove it from the excel.";
                        }

                        if (isset($xls_data) && !empty($xls_data)) {
                            $is_duplicated_in_xls = count(
                                array_filter($xls_data, function ($xlsd) use ($studentUniqueFields) {

                                    return ($xlsd['AcceptedStudent']['first_name'] == $studentUniqueFields[0] && $xlsd['AcceptedStudent']['middle_name'] == $studentUniqueFields[1] && $xlsd['AcceptedStudent']['last_name'] == $studentUniqueFields[2] && $xlsd['AcceptedStudent']['sex'] == $studentUniqueFields[3] && $xlsd['AcceptedStudent']['college_id'] == $studentUniqueFields[4] && $xlsd['AcceptedStudent']['program_id'] == $studentUniqueFields[5] && $xlsd['AcceptedStudent']['program_type_id'] == $studentUniqueFields[6]);
                                })
                            );

                            //debug($is_duplicated_in_xls);

                            if ($is_duplicated_in_xls > 0) {
                                $non_valide_rows[] = "Student at row " . $i . " (" . $studentUniqueFields[0] . " " . $studentUniqueFields[1] . " " . $studentUniqueFields[2] . ") is duplicated " . $is_duplicated_in_xls . " in the excel file. Please remove it from the excel.";
                            }
                        }

                        $xls_data[] = array('AcceptedStudent' => $row_data);

                        $data->sheets[0]['cells'][$i] = null;

                        if (count($non_valide_rows) == 19) {
                            $non_valide_rows[] = "Please check other similar errors in the file you imported.";
                            break;
                        }
                    }
                }

                //invalid rows
                if (count($non_valide_rows) > 0) {
                    $row_list = "";
                    $this->Flash->error(
                        'Importing Error!! Please correct the following listed rows in your excel file.'
                    );
                    $this->set('non_valide_rows', $non_valide_rows);
                    return;
                }
            }

            if (!empty($xls_data)) {
                $reformat_for_saveAll = array();

                foreach ($xls_data as $xlk => &$xlv) {
                    if (empty($xlv['AcceptedStudent']['EHEECE_total_results'])) {
                        unset($xlv['AcceptedStudent']['EHEECE_total_results']);
                    }

                    if (!empty($xlv['AcceptedStudent']['department_id'])) {
                        $xlv['AcceptedStudent']['placementtype'] = REGISTRAR_ASSIGNED;
                        $xlv['AcceptedStudent']['placement_type_id'] = $this->AcceptedStudent->PlacementType->field(
                            'PlacementType.id',
                            array('PlacementType.code' => 'REGISTRAR_ASSIGNED')
                        );
                    }

                    $reformat_for_saveAll['AcceptedStudent'][] = $xlv['AcceptedStudent'];
                }

                // debug($reformat_for_saveAll);

                if ($this->AcceptedStudent->saveAll(
                    $reformat_for_saveAll['AcceptedStudent'],
                    array('validate' => 'first')
                )) {
                    $auto_messages = array();

                    if (count($reformat_for_saveAll['AcceptedStudent'])) {
                        $auto_message['AutoMessage']['message'] = (count(
                                $reformat_for_saveAll['AcceptedStudent']
                            ) > 1 ? count($reformat_for_saveAll['AcceptedStudent']) . ' students are' : count(
                                    $reformat_for_saveAll['AcceptedStudent']
                                ) . ' student is') . ' recently imported using your account for ' . $selectedAcademicyear . ' Academic Year. You can use List Accepted Students tool to view list of students or <a style="background-color:white;" href="/accepted_students/index/Search.academicyear:' . str_replace(
                                '/',
                                '-',
                                $selectedAcademicyear
                            ) . '/Search.admitted:0/Search.limit:' . (count(
                                $reformat_for_saveAll['AcceptedStudent']
                            ) < 5000 ? (count(
                                $reformat_for_saveAll['AcceptedStudent']
                            )) : '5000') . '">View Accepted Students here.</a>';
                        $auto_message['AutoMessage']['read'] = 0;
                        $auto_message['AutoMessage']['user_id'] = $this->Session->read('Auth.User')['id'];
                        //debug(ClassRegistry::init('AutoMessage')->save($auto_message));
                        $auto_messages[] = $auto_message;
                    }

                    if ($this->Session->read('Auth.User')['role_id'] == ROLE_REGISTRAR) {
                        $registrar_admin = ClassRegistry::init('User')->find('first', array(
                            'conditions' => array(
                                'User.role_id' => ROLE_REGISTRAR,
                                'User.is_admin' => 1,
                                'User.active' => 1
                            ),
                            'recursive' => -1
                        ));

                        debug($registrar_admin);

                        if (!empty($registrar_admin) && ($this->Session->read(
                                    'Auth.User'
                                )['is_admin'] != 1 || $this->Session->read(
                                    'Auth.User'
                                )['id'] != $registrar_admin['User']['id'])) {
                            if (count($reformat_for_saveAll['AcceptedStudent'])) {
                                $auto_message['AutoMessage']['message'] = (count(
                                        $reformat_for_saveAll['AcceptedStudent']
                                    ) > 1 ? count($reformat_for_saveAll['AcceptedStudent']) . ' students are' : count(
                                            $reformat_for_saveAll['AcceptedStudent']
                                        ) . ' student is') . ' recently imported using other registrar account(with a privilege to import students) for ' . $selectedAcademicyear . ' Academic Year. You can use List Accepted Students tool to view list of students imported or <a style="background-color:white;" href="/accepted_students/index/Search.academicyear:' . str_replace(
                                        '/',
                                        '-',
                                        $selectedAcademicyear
                                    ) . '/Search.admitted:0/Search.limit:' . (count(
                                        $reformat_for_saveAll['AcceptedStudent']
                                    ) < 5000 ? (count(
                                        $reformat_for_saveAll['AcceptedStudent']
                                    )) : '5000') . '">View Accepted Students here.</a>';
                                $auto_message['AutoMessage']['read'] = 0;
                                $auto_message['AutoMessage']['user_id'] = $registrar_admin['User']['id'];
                                $auto_messages[] = $auto_message;
                            }
                        }
                    }

                    if (!empty($collIdsNotification)) {
                        $collIdsNotification = array_values(array_unique($collIdsNotification));
                        $collegessss = $this->AcceptedStudent->College->find(
                            'list',
                            array('conditions' => array('College.id' => $collIdsNotification, 'College.active' => 1))
                        );
                        debug($collegessss);

                        if (!empty($collegessss)) {
                            foreach ($collegessss as $cid => $cvalue) {
                                $freshmanCount = count(
                                    array_filter(
                                        $reformat_for_saveAll['AcceptedStudent'],
                                        function ($accStd) use ($cid) {

                                            return ($accStd['college_id'] == $cid && !isset($accStd['department_id']));
                                        }
                                    )
                                );
                                debug($freshmanCount);

                                if ($freshmanCount) {
                                    $college_admin = ClassRegistry::init('Staff')->find('first', array(
                                        'conditions' => array(
                                            'Staff.college_id' => $cid,
                                            'User.role_id' => ROLE_COLLEGE,
                                            'User.active' => 1,
                                            'User.is_admin' => 1
                                        ),
                                        'contain' => array('User'),
                                        'recursive' => -1
                                    ));

                                    if (!empty($college_admin)) {
                                        $auto_message = array();
                                        $auto_message['AutoMessage']['message'] = ($freshmanCount > 1 ? $freshmanCount . ' freshman students are' : $freshmanCount . ' freshman student is') . ' assigned recently to your college for ' . $selectedAcademicyear . ' Academic Year. You can use List Accepted Students tool to view list of students or <a style="background-color:white;" href="/accepted_students/index/Search.academicyear:' . str_replace(
                                                '/',
                                                '-',
                                                $selectedAcademicyear
                                            ) . '/Search.admitted:0/Search.limit:' . ($freshmanCount < 5000 ? $freshmanCount : '5000') . '">View Accepted Students here.</a>';
                                        $auto_message['AutoMessage']['read'] = 0;
                                        $auto_message['AutoMessage']['user_id'] = $college_admin['User']['id'];
                                        //debug(ClassRegistry::init('AutoMessage')->save($auto_message));
                                        $auto_messages[] = $auto_message;
                                    }
                                }
                            }
                        }
                    }


                    if (!empty($deptIdsNotification)) {
                        $deptIdsNotification = array_values(array_unique($deptIdsNotification));
                        $deptsss = $this->AcceptedStudent->Department->find(
                            'list',
                            array(
                                'conditions' => array(
                                    'Department.id' => $deptIdsNotification,
                                    'Department.active' => 1
                                )
                            )
                        );
                        debug($deptsss);

                        if (!empty($deptsss)) {
                            foreach ($deptsss as $dptid => $deptvalue) {
                                $deptStudentsCount = count(
                                    array_filter(
                                        $reformat_for_saveAll['AcceptedStudent'],
                                        function ($accStd) use ($dptid) {

                                            return (isset($accStd['department_id']) && !is_null(
                                                    $accStd['department_id']
                                                ) && $accStd['department_id'] == $dptid);
                                        }
                                    )
                                );
                                debug($deptStudentsCount);

                                if ($deptStudentsCount) {
                                    $department_admin = ClassRegistry::init('Staff')->find('first', array(
                                        'conditions' => array(
                                            'Staff.department_id' => $dptid,
                                            'User.role_id' => ROLE_DEPARTMENT,
                                            'User.is_admin' => 1,
                                            'User.active' => 1
                                        ),
                                        'contain' => array('User'),
                                        'recursive' => -1
                                    ));

                                    if (!empty($department_admin)) {
                                        $auto_message = array();
                                        $auto_message['AutoMessage']['message'] = ($deptStudentsCount > 1 ? $deptStudentsCount . ' students are' : $deptStudentsCount . ' student is') . ' assigned recently to your department for ' . $selectedAcademicyear . ' Academic Year. You can use List Accepted Students tool to view list of students or <a style="background-color:white;" href="/accepted_students/index/Search.academicyear:' . str_replace(
                                                '/',
                                                '-',
                                                $selectedAcademicyear
                                            ) . '/Search.admitted:0/Search.limit:' . ($deptStudentsCount < 5000 ? $deptStudentsCount : '5000') . '">View Accepted Students here</a>. Don\'t forget to attach them to a curriculum before trying to add them to  a section';
                                        $auto_message['AutoMessage']['read'] = 0;
                                        $auto_message['AutoMessage']['user_id'] = $department_admin['User']['id'];
                                        //debug(ClassRegistry::init('AutoMessage')->save($auto_message));
                                        $auto_messages[] = $auto_message;
                                    }
                                }
                            }
                        }
                    }

                    if (!empty($auto_messages)) {
                        //debug($auto_messages);
                        debug(
                            ClassRegistry::init('AutoMessage')->saveAll($auto_messages, array('validate' => 'first'))
                        );
                    }

                    $this->Flash->success(
                        'Imported ' . (count(
                            $reformat_for_saveAll['AcceptedStudent']
                        )) . ' student record(s) successfully.'
                    );
                    //$this->redirect(array('action'=>'index'));
                    $this->redirect(
                        array(
                            'action' => 'index/Search.academicyear:' . str_replace(
                                    '/',
                                    '-',
                                    $selectedAcademicyear
                                ) . '/Search.admitted:0/Search.limit:' . (count(
                                    $reformat_for_saveAll['AcceptedStudent']
                                ) < 5000 ? (count($reformat_for_saveAll['AcceptedStudent'])) : '5000') . ''
                        )
                    );
                } else {
                    $error = $this->AcceptedStudent->invalidFields();
                    debug($error);
                    $this->Flash->error(
                        'Unable to import records. Please try again.' . (INCLUDE_STUDENT_NUMBER_IN_IMPORT_TEMPLATE_FILE == 1 ? ' Check if you included studentnumber for the students you are importing.' : '')
                    );
                }
            } else {
                $this->Flash->error('Error. Unable to import records. Please try again.');
            }
        } else {
            // $this->Flash->error('Importing Error. Please try again');
        }
    }

    public function _getListOfAcceptedStudentsWithoutPreference($academicyear = null, $college_id = null)
    {

        $acceptedStudents = $this->AcceptedStudent->find('all', array(
            'conditions' => array(
                "OR" => array(
                    'AcceptedStudent.department_id IS NULL',
                    'AcceptedStudent.department_id = ""',
                    'AcceptedStudent.department_id = 0',
                ),
                "AcceptedStudent.academicyear LIKE" => $academicyear . '%',
                "AcceptedStudent.college_id" => $college_id,
                "AcceptedStudent.Placement_Approved_By_Department is null",
                "OR" => array(
                    "AcceptedStudent.placementtype IS NULL",
                    "AcceptedStudent.placementtype" => CANCELLED_PLACEMENT
                )
            )
        ));

        $acceptedStudentscount = 0;

        if (!empty($acceptedStudents)) {
            $acceptedStudentscount = count($acceptedStudents);
        }

        $not_completed_count = 0;
        $preference_not_completed = array();
        $not_completed_preference = 0;

        if (!empty($acceptedStudents)) {
            foreach ($acceptedStudents as $k => $value) {
                $count = count($value['Preference']);
                if (!$count) {
                    $preference_not_completed[] = $value;
                    $not_completed_count++;
                }
            }

            $not_completed_preference = ($not_completed_count / $acceptedStudentscount) * 100;
        }

        return $not_completed_preference;
    }

    // function to view pdf
    public function print_autoplaced_pdf()
    {

        $autoplacedstudents = $this->Session->read('autoplacedstudents');
        $selected_academic_year = $this->Session->read('selected_academic_year');

        /* $placedstudent = $this->AcceptedStudent->find('all', array(
			'conditions' => array(
				'AcceptedStudent.college_id' =>
				$this->college_id, 'AcceptedStudent.academicyear LIKE ' => $selected_academic_year . '%',
				'AcceptedStudent.placementtype' => AUTO_PLACEMENT
			),
			'order' => array(
				'AcceptedStudent.department_id asc',
				'AcceptedStudent.EHEECE_total_results desc',
				'AcceptedStudent.freshman_result desc'
			)
		));

		$departments = ClassRegistry::init('ParticipatingDepartment')->find("all", array(
			'fields' => 'ParticipatingDepartment.department_id',
			"conditions" => array(
				'ParticipatingDepartment.academic_year LIKE' => $selected_academic_year . '%',
				'ParticipatingDepartment.college_id' => $this->college_id
			)
		));

		if (empty($placedstudent)) {
			$this->Flash->info('No auto placement report for  the selected academic year.');
			$this->redirect(array('action' => 'auto_placement_approve_college'));
		}

		$dep_id = array();

		if (!empty($departments)) {
			foreach ($departments as $k => $v) {
				$dep_id[] = $v['ParticipatingDepartment']['department_id'];
			}
		}

		$dep_name = $this->AcceptedStudent->Department->find('list', array('conditions' => array('Department.id' => $dep_id)));
		$newly_placed_student = array();

		if (!empty($dep_name)) {
			foreach ($dep_name as $dk => $dv) {

				if (!empty($placedstudent)) {
					foreach ($placedstudent as $k => $v) {
						if ($dk == $v['Department']['id']) {
							$newly_placed_student[$dv][$k] = $v;
						}
					}
				}

				$newly_placed_student['auto_summery'][$dv]['C'] = $this->AcceptedStudent->find('count', array(
					'conditions' => array(
						'AcceptedStudent.academicyear LIKE ' => $selected_academic_year . '%',
						'AcceptedStudent.department_id' => $dk,
						'AcceptedStudent.college_id' => $this->college_id,
						'AcceptedStudent.placement_based' => 'C'
					)
				));

				$newly_placed_student['auto_summery'][$dv]['Q'] = 0;
			}
		}

		$autoplacedstudents = $newly_placed_student;  */

        $college_name = $this->college_name;
        //debug($college_name);
        $this->set(compact('autoplacedstudents', 'college_name', 'selected_academic_year'));
        $this->layout = 'pdf';
        $this->render();
        // $this->Session->delete('autoplacedstudents');
    }

    // function to export
    public function export_autoplaced_xls()
    {

        $autoplacedstudents = $this->Session->read('autoplacedstudents');
        //$autoplacedstudent s= $newly_placed_student;
        $this->set('autoplacedstudents', $autoplacedstudents);
        //$this->Session->delete('autoplacedstudents');
    }

    // funcation to produce report of autoplacement

    public function auto_report()
    {

        $this->__view_placement_report();
    }


    public function __view_placement_report()
    {

        $options = array();
        $options = array(
            'order' => array(
                'AcceptedStudent.EHEECE_total_results DESC',
                'AcceptedStudent.freshman_result DESC'
            )
        );
        $options['conditions'][] = array('AcceptedStudent.college_id' => $this->college_id);

        if (isset($this->request->data['Search']) && !empty($this->request->data['Search'])) {
            if (isset($this->request->data['Search']['academic_year']) && !empty($this->request->data['Search']['academic_year'])) {
                $options['conditions'][] = array('AcceptedStudent.academicyear' => $this->request->data['Search']['academic_year']);
            }

            if (isset($this->request->data['Search']['department_id']) && !empty($this->request->data['Search']['department_id'])) {
                $options['conditions'][] = array('AcceptedStudent.department_id' => $this->request->data['Search']['department_id']);
            }

            if (isset($this->request->data['Search']['sex']) && !empty($this->request->data['Search']['sex']) && $this->request->data['Search']['sex'] != 'all') {
                $options['conditions'][] = array('AcceptedStudent.sex' => $this->request->data['Search']['sex']);
            }

            if (isset($this->request->data['Search']['placement_based']) && !empty($this->request->data['Search']['placement_based']) && $this->request->data['Search']['placement_based'] != 'all') {
                $options['conditions'][] = array('AcceptedStudent.placement_based' => $this->request->data['Search']['placement_based']);
            }

            if (isset($this->request->data['Search']['placementtype']) && !empty($this->request->data['Search']['placementtype']) && $this->request->data['Search']['placementtype'] != 'all') {
                $options['conditions'][] = array('AcceptedStudent.placementtype' => $this->request->data['Search']['placementtype']);
            }

            if (isset($this->request->data['Search']['result_criteria_id']) && !empty($this->request->data['Search']['result_criteria_id']) && $this->request->data['Search']['result_criteria_id'] != 'all') {
                //find type of result criteria
                $resultCriteriam = ClassRegistry::init('PlacementsResultsCriteria')->find(
                    'first',
                    array('conditions' => array('PlacementsResultsCriteria.id' => $this->request->data['Search']['result_criteria_id']))
                );

                if ($resultCriteriam['PlacementsResultsCriteria']['prepartory_result']) {
                    $options['conditions'][] = array(
                        'AcceptedStudent.EHEECE_total_results >=' => $resultCriteriam['PlacementsResultsCriteria']['result_from'],
                        'AcceptedStudent.EHEECE_total_results <=' => $resultCriteriam['PlacementsResultsCriteria']['result_to']
                    );
                } else {
                    $options['conditions'][] = array(
                        'AcceptedStudent.freshman_result >=' => $resultCriteriam['PlacementsResultsCriteria']['result_from'],
                        'AcceptedStudent.freshman_result <=' => $resultCriteriam['PlacementsResultsCriteria']['result_to']
                    );
                }
            }

            $placedstudent = $this->AcceptedStudent->find(
                'all',
                array(
                    'conditions' => $options['conditions'],
                    'order' => array(
                        'AcceptedStudent.freshman_result DESC',
                        'AcceptedStudent.EHEECE_total_results DESC'
                    )
                )
            );

            if (empty($placedstudent)) {
                $this->Flash->info('There is no report for the selected academic year.');
                //$this->redirect(array('action'=>'auto_placement_approve_college'));
            } else {
                $dep_id = array();

                $departments = ClassRegistry::init('ParticipatingDepartment')->getParticipatingDepartment(
                    $this->college_id,
                    $this->request->data['Search']['academic_year']
                );
                $dep_id = array_keys($departments);
                $dep_name = $this->AcceptedStudent->Department->find(
                    'list',
                    array('conditions' => array('Department.id' => $dep_id))
                );

                $newly_placed_student = array();

                if (!empty($dep_name)) {
                    foreach ($dep_name as $dk => $dv) {
                        if (!empty($placedstudent)) {
                            foreach ($placedstudent as $k => $v) {
                                if ($dk == $v['Department']['id']) {
                                    $newly_placed_student[$dv][$k] = $v;
                                }
                            }
                        }

                        $newly_placed_student['auto_summery'][$dv]['C'] = $this->AcceptedStudent->find('count', array(
                            'conditions' => array(
                                'AcceptedStudent.academicyear' => $this->request->data['Search']['academic_year'],
                                'AcceptedStudent.department_id' => $dk,
                                'AcceptedStudent.college_id' => $this->college_id,
                                'AcceptedStudent.placement_based' => 'C'
                            )
                        ));

                        $newly_placed_student['auto_summery'][$dv]['CF'] = $this->AcceptedStudent->find('count', array(
                            'conditions' => array(
                                'AcceptedStudent.academicyear' => $this->request->data['Search']['academic_year'],
                                'AcceptedStudent.department_id' => $dk,
                                'AcceptedStudent.college_id' => $this->college_id,
                                'AcceptedStudent.placement_based' => 'C',
                                'AcceptedStudent.sex' => 'female'
                            )
                        ));

                        $newly_placed_student['auto_summery'][$dv]['QF'] = $this->AcceptedStudent->find('count', array(
                            'conditions' => array(
                                'AcceptedStudent.academicyear' => $this->request->data['Search']['academic_year'],
                                'AcceptedStudent.department_id' => $dk,
                                'AcceptedStudent.college_id' => $this->college_id,
                                'AcceptedStudent.placement_based' => 'Q',
                                'AcceptedStudent.sex' => 'female'
                            )
                        ));

                        $newly_placed_student['auto_summery'][$dv]['Q'] = $this->AcceptedStudent->find('count', array(
                            'conditions' => array(
                                'AcceptedStudent.academicyear' => $this->request->data['Search']['academic_year'],
                                'AcceptedStudent.department_id' => $dk,
                                'AcceptedStudent.college_id' => $this->college_id,
                                'AcceptedStudent.placement_based' => 'Q'
                            )
                        ));
                    }
                }

                if ((isset($this->request->data['generatePlacedList']) && !empty($this->request->data['generatePlacedList']))) {
                    $selected_academic_year = $this->request->data['Search']['academic_year'];
                    $autoplacedstudents = $newly_placed_student;

                    $university = ClassRegistry::init('University')->find(
                        'first',
                        array('order' => array('University.created DESC'))
                    );

                    $this->set(compact('autoplacedstudents', 'selected_academic_year', 'university'));
                    $this->response->type('application/pdf');
                    $this->layout = '/pdf/default';
                    $this->render('print_autoplaced_pdf');
                    return;
                }

                $this->set('autoplacedstudents', $newly_placed_student);
            }

            $departments = ClassRegistry::init('ParticipatingDepartment')->getParticipatingDepartment(
                $this->college_id,
                $this->request->data['Search']['academic_year']
            );
            $resultCriterias = ClassRegistry::init('PlacementsResultsCriteria')->getPlacementResultCriteria(
                $this->college_id,
                $this->request->data['Search']['academic_year']
            );
            $this->set(compact('departments', 'resultCriterias'));
        }

        $this->render('auto_report');
    }

    public function download($file, $file_name)
    {

        $this->view = 'Media';
        $params = array(
            'id' => $file_name,
            'name' => $file,
            'download' => true, // force the download, don't just open.
            'extension' => 'xls',
            'mimeType' => array('xls' => 'application/application/vnd.ms-excel'),
            'path' => APP . 'webroot/files/template' . DS . $file_name

        );
        //debug($params);
        $this->set($params);
    }


    /*

	function issue_password()
	{

		if (!empty($this->request->data) && isset($this->request->data['issuepasswordtostudent'])) {

			// check password length
			$this->loadModel('Securitysetting');
			$securitysetting = $this->Securitysetting->find('first');

			if (strlen($this->request->data['User']['passwd']) >= $securitysetting['Securitysetting']['minimum_password_length'] && strlen($this->request->data['User']['passwd']) <= $securitysetting['Securitysetting']['maximum_password_length']) {

				$this->request->data['User']['role_id'] = ROLE_STUDENT;
				$this->request->data['User']['password'] = $this->Auth->password($this->request->data['User']['passwd']);
				unset($this->request->data['User']['passwd']);

				$username = $this->AcceptedStudent->User->find('first', array('conditions' => array('User.username' => $this->request->data['User']['username']), 'recursive' => -1));

				if (!empty($username)) {
					$this->request->data['User']['id'] = $username['User']['id'];
				}

				$this->request->data['User']['force_password_change'] = 1;

				if ($this->AcceptedStudent->User->save($this->request->data['User'])) {
					// if the issued is the first time update accepted student field
					if (empty($this->request->data['User']['id'])) {
						$this->request->data['AcceptedStudent']['user_id'] = $this->AcceptedStudent->User->id;
						$this->AcceptedStudent->id = $this->request->data['AcceptedStudent']['id'];
						$this->AcceptedStudent->saveField('user_id', $this->request->data['AcceptedStudent']['user_id']);
					}

					$student = $this->AcceptedStudent->Student->find('first', array('conditions' => array('Student.accepted_student_id' => $this->request->data['AcceptedStudent']['id']), 'recursive' => -1, 'fields' => array('id', 'user_id')));

					if (!empty($student)) {
						if (!empty($this->request->data['User']['id'])) {
							$student['Student']['user_id'] = $this->request->data['User']['id'];
						} else {
							$student['Student']['user_id'] = $this->AcceptedStudent->User->id;
						}

						$this->AcceptedStudent->Student->id = $student['Student']['id'];
						$this->AcceptedStudent->Student->saveField('user_id', $student['Student']['user_id']);
						//$this->Flash->success('The student password has been updated.');
					}

					$this->Flash->success('The student password has been updated.');
					$this->request->data = null;

				} else {
					$this->Flash->error('The student password could not be updated. Please try again.');
				}
			} else {
				$this->Flash->error('Password policy: Your password should be greather than or equal to ' . $securitysetting['Securitysetting']['minimum_password_length'] . ' and less than or equal to ' . $securitysetting['Securitysetting']['maximum_password_length'] . '');
			}
		}

		if (!empty($this->request->data) && isset($this->request->data['issuestudentidsearch'])) {

			if (!empty($this->request->data['AcceptedStudent']['studentnumber'])) {

				$students = array();

				if ($this->role_id == ROLE_DEPARTMENT) {

					$students = $this->AcceptedStudent->find('first', array(
						'conditions' => array(
							'AcceptedStudent.studentnumber LIKE ' => trim($this->request->data['AcceptedStudent']['studentnumber']),
							'AcceptedStudent.department_id' => $this->department_id
						)
					));

					if (!empty($students)) {
						$this->set('students', $students);
						$this->set('hide_search', true);
						$this->set('student_number', $this->request->data['AcceptedStudent']['studentnumber']);
					} else {
						$this->Flash->error('You are not elegible to issue/reset password or The student is not belongs to your department. Please check if you make a typo error or check for space and try again.');
					}

				} else if ($this->role_id == ROLE_COLLEGE) {

					$studentsss = $this->AcceptedStudent->find('first', array('conditions' => array('AcceptedStudent.studentnumber LIKE ' => trim($this->request->data['AcceptedStudent']['studentnumber']) . '%')));

					if (!empty($studentsss)) {

						$students = $this->AcceptedStudent->find('first', array(
							'conditions' => array(
								'AcceptedStudent.studentnumber LIKE' => trim($this->request->data['AcceptedStudent']['studentnumber']),
								'AcceptedStudent.college_id' => $this->college_id,
								'AcceptedStudent.department_id is null',
							))
						);

						if (empty($students)) {
							$this->Flash->info('You are not elegible to issue/reset password. The student has already assigned to department. Department is responsible for  password  issue or reset.');
						} else {
							$this->set('students', $students);
							$this->set('hide_search', true);
							$this->set('student_number', $this->request->data['AcceptedStudent']['studentnumber']);
						}

					} else {
						$this->Flash->error('Please enter a valid Student ID Number. Check for typo errors and spaces.');
					}
				}
			} else {
				$this->Flash->error('Please provide student number');
			}
		}
	}

	*/


    public function deattach_curriculum()
    {

        // deattach curriculum
        if (!empty($this->request->data) && isset($this->request->data['deaattach'])) {
            $selected_count = array_count_values($this->request->data['AcceptedStudent']['approve']);

            if (isset($selected_count[1]) && $selected_count[1] > 0) {
                unset($this->request->data['AcceptedStudent']['SelectAll']);

                $approve_placement = $this->request->data;
                $update_admitted_students_department = array();

                $selected_approved_students = $approve_placement['AcceptedStudent']['approve'];

                unset($approve_placement['AcceptedStudent']['academicyear']);
                unset($approve_placement['AcceptedStudent']['curriculum_id']);
                unset($approve_placement['AcceptedStudent']['approve']);

                if (!empty($approve_placement['AcceptedStudent'])) {
                    foreach ($approve_placement['AcceptedStudent'] as $mk => &$mv) {
                        if (!empty($selected_approved_students)) {
                            foreach ($selected_approved_students as $student_id => $is_selected) {
                                if ($is_selected && $student_id == $mv['id']) {
                                    //$mv['Placement_Approved_By_Department']=1;
                                    $mv['curriculum_id'] = null;
                                    $update_admitted_students_department[] = $mv['id'];
                                    break;
                                }
                            }
                        }
                    }
                }

                if (!empty($approve_placement)) {
                    if ($this->AcceptedStudent->saveAll(
                        $approve_placement['AcceptedStudent'],
                        array('validate' => 'first')
                    )) {
                        $students = $this->AcceptedStudent->Student->find('all', array(
                            'conditions' => array(
                                'Student.accepted_student_id' => $update_admitted_students_department
                            ),
                            'fields' => array('id', 'department_id', 'accepted_student_id', 'curriculum_id'),
                            'recursive' => -1
                        ));

                        $curriculum_idd = 0;

                        if (!empty($students)) {
                            $accepted_students_department_id = $this->AcceptedStudent->field(
                                'AcceptedStudent.department_id',
                                array('AcceptedStudent.id' => $update_admitted_students_department)
                            );
                            $update_students = array();
                            $curriculumHistoryAttachment = array();
                            $count = 0;

                            foreach ($students as $stv) {
                                $curriculum_idd = $stv['Student']['curriculum_id'];

                                $update_students['Student'][$count]['id'] = $stv['Student']['id'];
                                //$update_students['Student'][$count]['department_id'] = $accepted_students_department_id;
                                if ($stv['Student']['department_id'] != $accepted_students_department_id) {
                                    $update_students['Student'][$count]['department_id'] = $accepted_students_department_id;
                                }
                                $update_students['Student'][$count]['curriculum_id'] = null;

                                //check if s/he has already that curriculum attachment
                                $checkAttachment = $this->AcceptedStudent->Student->CurriculumAttachment->find(
                                    'count',
                                    array(
                                        'conditions' => array(
                                            'CurriculumAttachment.student_id' => $stv['Student']['id'],
                                            'CurriculumAttachment.curriculum_id' => $stv['Student']['curriculum_id']
                                        )
                                    )
                                );

                                if (!$checkAttachment) {
                                    $curriculumHistoryAttachment['CurriculumAttachment'][$count]['student_id'] = $stv['Student']['id'];
                                    $curriculumHistoryAttachment['CurriculumAttachment'][$count]['curriculum_id'] = $stv['Student']['curriculum_id'];
                                    //$this->AcceptedStudent->Student->CurriculumAttachment->save($curriculumHistoryAttachment);
                                }

                                $count++;
                            }

                            if (!empty($curriculumHistoryAttachment)) {
                                $this->AcceptedStudent->Student->CurriculumAttachment->saveAll(
                                    $curriculumHistoryAttachment['CurriculumAttachment'],
                                    array('validate' => false)
                                );
                            }

                            if (!empty($update_students['Student'])) {
                                if ($this->AcceptedStudent->Student->saveAll(
                                    $update_students['Student'],
                                    array('validate' => false)
                                )) {
                                } else {
                                    $this->Flash->success(
                                        'Synchronization problem, Unable to save the changes in Students table'
                                    );
                                }
                            }
                        }

                        if ($curriculum_idd) {
                            $selectedCurrculi = $this->AcceptedStudent->Curriculum->field(
                                'Curriculum.name',
                                array('Curriculum.id' => $curriculum_idd)
                            );
                            $this->Flash->success(
                                (count($update_students['Student']) > 1 ? count(
                                        $update_students['Student']
                                    ) . ' students are' : count(
                                        $update_students['Student']
                                    ) . ' student is') . ' detached from "' . $selectedCurrculi . '" curriculum successfully.'
                            );
                        } else {
                            $this->Flash->success('The selected student has been detached from his/her curriculum.');
                        }
                    }
                }
            } else {
                $this->Flash->error('Please select atleast one student to detach from curriculum.');
            }
            // $this->request->data['searchbutton']=true;
        }

        $limit = 400;

        if (!empty($this->request->data) && isset($this->request->data['searchbutton'])) {
            if (isset($this->request->data['AcceptedStudent']['academicyear'])) {
                $selected_academic_year = $this->request->data['AcceptedStudent']['academicyear'];
            } else {
                $selected_academic_year = $this->AcademicYear->current_academicyear();
            }

            if (isset($this->request->data['AcceptedStudent']['limit']) && !empty($this->request->data['AcceptedStudent']['limit'])) {
                $limit = $this->request->data['AcceptedStudent']['limit'];
            }

            //WHERE item_sub_category_id IN (SELECT id FROM item_sub_categories
            $this->set('selected_academicyear', $selected_academic_year);

            //TODO deattach from curriculum should not include those who are graduated
            $placedstudent = $this->AcceptedStudent->find('all', array(
                'conditions' => array(
                    'AcceptedStudent.first_name LIKE' => $this->request->data['AcceptedStudent']['name'] . '%',
                    'AcceptedStudent.academicyear' => $selected_academic_year,
                    'AcceptedStudent.program_id' => $this->request->data['AcceptedStudent']['program_id'],
                    'AcceptedStudent.program_type_id' => $this->request->data['AcceptedStudent']['program_type_id'],
                    'Student.department_id' => $this->department_id,
                    'AcceptedStudent.curriculum_id is not null',
                    //'AcceptedStudent.id IN (select accepted_student_id from students where id NOT IN (select student_id from graduate_lists) ) ',
                    //"NOT" => array("AcceptedStudent.id" => array("select accepted_student_id from students where id  IN (select student_id from graduate_lists where student_id is not null)")),
                    'Student.graduated' => 0,
                ),
                'contain' => array(
                    'Student' => array('id', 'graduated'),
                    'Program' => array('id', 'name'),
                    'Department' => array('id', 'name'),
                    'ProgramType' => array('id', 'name'),
                    'Region' => array('id', 'name'),
                    'Curriculum' => array('id', 'curriculum_detail')
                ),
                'limit' => $limit,
                'maxLimit' => $limit,
                'recursive' => -1
            ));

            if (empty($placedstudent)) {
                $this->Flash->info(
                    'There is no student found for ' . $selected_academic_year . ' admission year and other given criterias with curricullum attachment. Either all students are graduated, no students admitted for ' . $selected_academic_year . ' or no students are attached to any curriculum.'
                );
                //$this->redirect(array('action'=>'approve_auto_placement'));
            } else {
                $this->set('autoplacedstudents', $placedstudent);
                $this->set('selected_academicyear', $selected_academic_year);
                $this->set('auto_approve', true);
            }
        }

        $dis_year = (int)explode('/', $this->AcademicYear->current_academicyear())[0];
        $start_year = ($dis_year - YEARS_BACK_FOR_CURRICULUM_ATTACH_DETACH);
        $end_year = ($dis_year + 1);

        $acyear_list = $this->AcademicYear->academicYearInArray($start_year, $end_year);

        $programs = $this->AcceptedStudent->Program->find('list');
        $programTypes = $this->AcceptedStudent->ProgramType->find('list');
        $this->set(compact('programs', 'programTypes', 'acyear_list', 'limit'));
    }

    public function attach_curriculum()
    {

        if (!empty($this->request->data) && isset($this->request->data['attach'])) {
            debug($this->request->data['AcceptedStudent']['approve']);

            $selected_count = array_count_values($this->request->data['AcceptedStudent']['approve']);

            if (isset($selected_count[1]) && $selected_count[1] > 0) {
                unset($this->request->data['AcceptedStudent']['SelectAll']);

                $approve_placement = $this->request->data;
                $update_admitted_students_department = array();
                $selected_academic_year = $approve_placement['AcceptedStudent']['academicyear'];
                $curriculum_id = $approve_placement['AcceptedStudent']['curriculum_id'];
                $selected_approved_students = $approve_placement['AcceptedStudent']['approve'];

                unset($approve_placement['AcceptedStudent']['academicyear']);
                unset($approve_placement['AcceptedStudent']['curriculum_id']);
                unset($approve_placement['AcceptedStudent']['approve']);

                if (!empty($approve_placement['AcceptedStudent'])) {
                    foreach ($approve_placement['AcceptedStudent'] as $mk => &$mv) {
                        if (!empty($selected_approved_students)) {
                            foreach ($selected_approved_students as $student_id => $is_selected) {
                                if ($is_selected && $student_id == $mv['id']) {
                                    $mv['Placement_Approved_By_Department'] = 1;
                                    $mv['curriculum_id'] = $curriculum_id;
                                    $update_admitted_students_department[] = $mv['id'];
                                    break;
                                }
                            }
                        }
                    }
                }

                if (!empty($approve_placement)) {
                    if ($this->AcceptedStudent->saveAll(
                        $approve_placement['AcceptedStudent'],
                        array('validate' => false)
                    )) {
                        $students = $this->AcceptedStudent->Student->find('all', array(
                            'conditions' => array(
                                'Student.accepted_student_id' => $update_admitted_students_department
                            ),
                            'fields' => array('id', 'department_id', 'accepted_student_id'),
                            'recursive' => -1
                        ));

                        if (!empty($students)) {
                            $accepted_students_department_id = $this->AcceptedStudent->field(
                                'AcceptedStudent.department_id',
                                array('AcceptedStudent.id' => $update_admitted_students_department)
                            );

                            $update_students = array();
                            $count = 0;
                            $curriculumHistoryAttachment = array();

                            $selectedCurrculi = $this->AcceptedStudent->Curriculum->field(
                                'Curriculum.name',
                                array('Curriculum.id' => $curriculum_id)
                            );

                            foreach ($students as $stv) {
                                $update_students['Student'][$count]['id'] = $stv['Student']['id'];
                                $update_students['Student'][$count]['department_id'] = $accepted_students_department_id;
                                $update_students['Student'][$count]['curriculum_id'] = $curriculum_id;

                                //for what we need to archive their section ???
                                //$this->AcceptedStudent->Student->StudentsSection->id = $this->AcceptedStudent->Student->StudentsSection->field('StudentsSection.id', array('StudentsSection.student_id'=>$stv['Student']['id'], 'StudentsSection.archive' => 0));
                                //$this->AcceptedStudent->Student->StudentsSection->saveField('archive','1');

                                //check if s/he has already that curriculum attachment
                                $checkAttachment = $this->AcceptedStudent->Student->CurriculumAttachment->find(
                                    'count',
                                    array(
                                        'conditions' => array(
                                            'CurriculumAttachment.student_id' => $stv['Student']['id'],
                                            'CurriculumAttachment.curriculum_id' => $curriculum_id
                                        )
                                    )
                                );

                                if (!$checkAttachment) {
                                    $curriculumHistoryAttachment['CurriculumAttachment'][$count]['student_id'] = $stv['Student']['id'];
                                    $curriculumHistoryAttachment['CurriculumAttachment'][$count]['curriculum_id'] = $curriculum_id;
                                }
                                $count++;
                            }

                            if (!empty($curriculumHistoryAttachment)) {
                                $this->AcceptedStudent->Student->CurriculumAttachment->saveAll(
                                    $curriculumHistoryAttachment['CurriculumAttachment'],
                                    array('validate' => false)
                                );
                            }

                            if (!empty($update_students['Student'])) {
                                if ($this->AcceptedStudent->Student->saveAll(
                                    $update_students['Student'],
                                    array('validate' => false)
                                )) {
                                } else {
                                    $this->Flash->success(
                                        'Synchronization problem, Unable to save the changes in Students table.'
                                    );
                                }
                            }
                        }
                        $this->Flash->success(
                            (count($update_students['Student']) > 1 ? count(
                                    $update_students['Student']
                                ) . ' students are' : count(
                                    $update_students['Student']
                                ) . ' student is') . ' attached to "' . $selectedCurrculi . '" curriculum successfully.'
                        );
                    } else {
                        $this->Flash->error('Could\'nt attach students to the selected curriculum . Please try again.');
                    }
                }
            } else {
                $this->Flash->error('Please select atleast one student to attach to the selected curriculum.');
                $this->request->data['searchbutton'] = true;
            }
        }

        $limit = 400;

        if (!empty($this->request->data) && isset($this->request->data['searchbutton'])) {
            if (isset($this->request->data['AcceptedStudent']['academicyear']) && !empty($this->request->data['AcceptedStudent']['academicyear'])) {
                $selected_academic_year = $this->request->data['AcceptedStudent']['academicyear'];
            } else {
                $selected_academic_year = $this->AcademicYear->current_academicyear();
            }

            if (isset($this->request->data['AcceptedStudent']['limit']) && !empty($this->request->data['AcceptedStudent']['limit'])) {
                $limit = $this->request->data['AcceptedStudent']['limit'];
            }

            $program_id = $this->request->data['AcceptedStudent']['program_id'];

            $this->set('selected_academicyear', $selected_academic_year);

            $placedstudent = $this->AcceptedStudent->find('all', array(
                'conditions' => array(
                    'AcceptedStudent.first_name LIKE' => $this->request->data['AcceptedStudent']['name'] . '%',
                    'AcceptedStudent.academicyear' => $selected_academic_year,
                    'Student.department_id' => $this->department_id,
                    'Student.studentnumber is not null',
                    'Student.graduated' => 0,
                    //'AcceptedStudent.id in (select accepted_student_id from students where department_id=' . $this->department_id . ')',
                    'AcceptedStudent.curriculum_id is null',
                    'AcceptedStudent.program_id' => $program_id,
                    'AcceptedStudent.program_type_id' => $this->request->data['AcceptedStudent']['program_type_id'],
                ),
                'contain' => array(
                    'Student' => array('id', 'graduated'),
                    'Program' => array('id', 'name'),
                    'Department' => array('id', 'name'),
                    'ProgramType' => array('id', 'name')
                ),
                'limit' => $limit,
                'maxLimit' => $limit,
                'recursive' => -1,
            ));


            if (empty($placedstudent) && !isset($this->request->data['AcceptedStudent']['approve'])) {
                $this->Flash->info(
                    'No student found in your department for ' . $selected_academic_year . ' admission year and other given criterias who needs curriculum attachment.'
                );
                //$this->redirect(array('action'=>'approve_auto_placement'));
            } else {
                if (!empty($placedstudent)) {
                    $this->set('autoplacedstudents', $placedstudent);
                    $this->set('selected_academicyear', $selected_academic_year);
                    $this->set('auto_approve', true);
                }
            }

            $curriculums = ClassRegistry::init('Curriculum')->find('list', array(
                'conditions' => array(
                    'Curriculum.department_id' => $this->department_id,
                    'Curriculum.program_id' => $this->request->data['AcceptedStudent']['program_id'],
                    'Curriculum.registrar_approved' => 1,
                    'Curriculum.active' => 1
                ),
                'fields' => array('Curriculum.id', 'Curriculum.curriculum_detail'),
                'order' => 'Curriculum.created DESC',
            ));
        }


        $dis_year = (int)explode('/', $this->AcademicYear->current_academicyear())[0];
        $start_year = ($dis_year - YEARS_BACK_FOR_CURRICULUM_ATTACH_DETACH);
        $end_year = ($dis_year + 1);

        $acyear_list = $this->AcademicYear->academicYearInArray($start_year, $end_year);

        $programs = $this->AcceptedStudent->Program->find('list');
        $programTypes = $this->AcceptedStudent->ProgramType->find('list');
        $this->set(compact('curriculums', 'programs', 'programTypes', 'acyear_list', 'limit'));
    }

    public function approve_auto_placement()
    {

        if (!empty($this->request->data) && isset($this->request->data['approve'])) {
            $selected_count = array_count_values($this->request->data['AcceptedStudent']['approve']);

            if (isset($selected_count[1]) && $selected_count[1] > 0) {
                if (empty($this->request->data['AcceptedStudent']['curriculum_id'])) {
                    $this->Flash->error('Select the curriculum that student will be attached to the students.');
                    $this->request->data['searchbutton'] = true;
                } else {
                    unset($this->request->data['AcceptedStudent']['SelectAll']);

                    $approve_placement = $this->request->data;
                    $update_admitted_students_department = array();
                    $selected_academic_year = $approve_placement['AcceptedStudent']['academicyear'];
                    $curriculum_id = $approve_placement['AcceptedStudent']['curriculum_id'];
                    $selected_approved_students = $approve_placement['AcceptedStudent']['approve'];

                    unset($approve_placement['AcceptedStudent']['academicyear']);
                    unset($approve_placement['AcceptedStudent']['curriculum_id']);
                    unset($approve_placement['AcceptedStudent']['approve']);

                    if (!empty($approve_placement['AcceptedStudent'])) {
                        foreach ($approve_placement['AcceptedStudent'] as $mk => &$mv) {
                            if (!empty($selected_approved_students)) {
                                foreach ($selected_approved_students as $student_id => $is_selected) {
                                    if ($is_selected && $student_id == $mv['id']) {
                                        $mv['Placement_Approved_By_Department'] = 1;
                                        $mv['curriculum_id'] = $curriculum_id;
                                        $update_admitted_students_department[] = $mv['id'];
                                        break;
                                    }
                                }
                            }
                        }
                    }
                    // debug($approve_placement);

                    if (!empty($approve_placement)) {
                        if ($this->AcceptedStudent->saveAll(
                            $approve_placement['AcceptedStudent'],
                            array('validate' => 'first')
                        )) {
                            $students = $this->AcceptedStudent->Student->find('all', array(
                                'conditions' => array(
                                    'Student.accepted_student_id' => $update_admitted_students_department
                                ),
                                'fields' => array('id', 'department_id', 'accepted_student_id'),
                                'recursive' => -1
                            ));

                            if (!empty($students)) {
                                $accepted_students_department_id = $this->AcceptedStudent->field(
                                    'AcceptedStudent.department_id',
                                    array('AcceptedStudent.id' => $update_admitted_students_department)
                                );

                                $update_students = array();
                                $count = 0;

                                if (!empty($students)) {
                                    foreach ($students as $stv) {
                                        $update_students['Student'][$count]['id'] = $stv['Student']['id'];
                                        $update_students['Student'][$count]['department_id'] = $accepted_students_department_id;
                                        $update_students['Student'][$count]['curriculum_id'] = $curriculum_id;
                                        $count++;
                                    }
                                }

                                if (!empty($update_students['Student'])) {
                                    if ($this->AcceptedStudent->Student->saveAll(
                                        $update_students['Student'],
                                        array('validate' => false)
                                    )) {
                                    } else {
                                        $this->Flash->success(
                                            'Synchronization problem, Unable to save the changes in Students table'
                                        );
                                    }
                                }
                            }

                            $this->Flash->success('The Placement has been approved.');

                            $placedstudent = $this->AcceptedStudent->find('all', array(
                                'conditions' => array(
                                    'AcceptedStudent.academicyear LIKE ' => $selected_academic_year . '%',
                                    'AcceptedStudent.Placement_Approved_By_Department' => 1,
                                    'AcceptedStudent.department_id' => $this->department_id,
                                    'AcceptedStudent.placementtype' => AUTO_PLACEMENT,
                                    'OR' => array(
                                        'AcceptedStudent.program_type_id' => PROGRAM_TYPE_REGULAR,
                                        'AcceptedStudent.program_type_id' => PROGRAM_TYPE_ADVANCE_STANDING,
                                    ),
                                    'AcceptedStudent.program_id' => PROGRAM_UNDEGRADUATE,
                                    'AcceptedStudent.studentnumber is not null',
                                    'AcceptedStudent.minute_number is not null'
                                ),
                                'order' => array(
                                    'AcceptedStudent.department_id asc',
                                    'AcceptedStudent.EHEECE_total_results desc',
                                    'AcceptedStudent.freshman_result desc'
                                )
                            ));

                            $this->set('autoplacedstudents', $placedstudent);
                            $this->set('auto_approve', true);
                            $this->set('turn_of_approve_button', true);
                            $this->set('selected_academicyear', $selected_academic_year);
                            $this->redirect(array('action' => 'approve_auto_placement'));
                        } else {
                            $this->Flash->error('Unable to approve auto placement. Please try again.');
                        }
                    }
                }
            } else {
                $this->Flash->error('Please select atleast one student to approve.');
                $this->request->data['searchbutton'] = true;
            }
        }

        if (!empty($this->request->data) && isset($this->request->data['searchbutton'])) {
            if (isset($this->request->data['AcceptedStudent']['academicyear']) && !empty($this->request->data['AcceptedStudent']['academicyear'])) {
                $selected_academic_year = $this->request->data['AcceptedStudent']['academicyear'];
            } else {
                $selected_academic_year = $this->AcademicYear->current_academicyear();
            }

            $this->set('selected_academicyear', $selected_academic_year);

            $placedstudent = $this->AcceptedStudent->find('all', array(
                'conditions' => array(
                    'AcceptedStudent.academicyear LIKE ' => $selected_academic_year . '%',
                    'AcceptedStudent.Placement_Approved_By_Department is null',
                    'AcceptedStudent.department_id' => $this->department_id,
                    'AcceptedStudent.placementtype' => AUTO_PLACEMENT,
                    'AcceptedStudent.minute_number is not null',
                    'AcceptedStudent.studentnumber is not null'
                ),
                'order' => array(
                    'AcceptedStudent.department_id asc',
                    'AcceptedStudent.EHEECE_total_results desc',
                    'AcceptedStudent.freshman_result desc'
                )
            ));

            $departments = ClassRegistry::init('ParticipatingDepartment')->find("all", array(
                'fields' => 'ParticipatingDepartment.department_id',
                "conditions" => array(
                    'ParticipatingDepartment.academic_year LIKE' => $selected_academic_year . '%',
                    'ParticipatingDepartment.college_id' => $this->college_id
                )
            ));

            if (empty($placedstudent)) {
                $this->Flash->info(
                    'There is no auto placed students that needs acceptance and curriculum attachment by your department for the ' . $selected_academic_year . ' academic year.'
                );
                $this->redirect(array('action' => 'approve_auto_placement'));
            } else {
                $this->set('autoplacedstudents', $placedstudent);
                $this->set('minute_number', $placedstudent[0]['AcceptedStudent']['minute_number']);
                $this->set('selected_academicyear', $selected_academic_year);
                $this->set('auto_approve', true);
            }
        }

        $curriculums = ClassRegistry::init('Curriculum')->find('list', array(
            'fields' => array('Curriculum.id', 'Curriculum.curriculum_detail'),
            'conditions' => array(
                'Curriculum.department_id' => $this->department_id,
                'Curriculum.registrar_approved' => 1,
                'Curriculum.active' => 1,
                'Curriculum.program_id' => PROGRAM_UNDEGRADUATE
            ),
            'order' => 'Curriculum.created DESC',
        ));

        $this->set(compact('curriculums'));
    }

    public function auto_placement_approve_college()
    {

        if (!empty($this->request->data) && isset($this->request->data['approve'])) {
            if (!empty($this->request->data['AcceptedStudent']['minute_number'])) {
                $approve_placement = $this->request->data;
                $admitted_students_ids = array();

                unset($approve_placement['AcceptedStudent']['academicyear']);
                unset($approve_placement['AcceptedStudent']['minute_number']);

                if (!empty($approve_placement['AcceptedStudent'])) {
                    foreach ($approve_placement['AcceptedStudent'] as $mk => &$mv) {
                        $mv['minute_number'] = $this->request->data['AcceptedStudent']['minute_number'];
                        $student_id = $this->AcceptedStudent->Student->field(
                            'id',
                            array('Student.accepted_student_id' => $mv['id'])
                        );
                        if (!empty($student_id)) {
                            $admitted_students_ids[] = $student_id;
                        }
                    }
                }

                if (!empty($approve_placement)) {
                    if ($this->AcceptedStudent->saveAll(
                        $approve_placement['AcceptedStudent'],
                        array('validate' => 'first')
                    )) {
                        if (!empty($admitted_students_ids)) {
                            foreach ($admitted_students_ids as $i => $v) {
                                $student_section_id = $this->AcceptedStudent->Student->StudentsSection->field(
                                    'StudentsSection.id',
                                    array('StudentsSection.student_id' => $v, 'StudentsSection.archive' => 0)
                                );
                                if ($student_section_id) {
                                    $this->AcceptedStudent->Student->StudentsSection->id = $student_section_id;
                                    $this->AcceptedStudent->Student->StudentsSection->saveField('archive', '1');
                                }
                            }
                        }
                        $this->Flash->success('The placement has been approved.');
                        $this->redirect(array('action' => 'index'));
                    } else {
                        $this->Flash->error('Unable to approve auto placement. Please try again.');
                    }
                }
            } else {
                $this->Flash->error('Please provide minute number for the approval.');
            }
        }

        if (!empty($this->request->data)) {
            if (isset($this->request->data['AcceptedStudent']['academicyear']) && !empty($this->request->data['AcceptedStudent']['academicyear'])) {
                $selected_academic_year = $this->request->data['AcceptedStudent']['academicyear'];
            } else {
                $selected_academic_year = $this->AcademicYear->current_academicyear();
            }

            $this->set('selected_academicyear', $selected_academic_year);

            $placedstudent = $this->AcceptedStudent->find('all', array(
                    'conditions' => array(
                        'AcceptedStudent.college_id' => $this->college_id,
                        'AcceptedStudent.academicyear LIKE ' => $selected_academic_year . '%',
                        'AcceptedStudent.placementtype' => AUTO_PLACEMENT,
                        "OR" => array(
                            'AcceptedStudent.minute_number is null',
                            'AcceptedStudent.minute_number' => ''
                        )
                    ),
                    'order' => array(
                        'AcceptedStudent.department_id asc',
                        'AcceptedStudent.EHEECE_total_results desc',
                        'AcceptedStudent.freshman_result desc'
                    )
                )
            );

            $departments = ClassRegistry::init('ParticipatingDepartment')->find("all", array(
                'fields' => 'ParticipatingDepartment.department_id',
                "conditions" => array(
                    'ParticipatingDepartment.academic_year LIKE' => $selected_academic_year . '%',
                    'ParticipatingDepartment.college_id' => $this->college_id
                )
            ));

            if (empty($placedstudent)) {
                $this->Flash->error(
                    'There is no auto placement result that needs approval for the selected academic year.'
                );

                $placedstudent = $this->AcceptedStudent->find('all', array(
                    'conditions' => array(
                        'AcceptedStudent.college_id' => $this->college_id,
                        'AcceptedStudent.academicyear LIKE ' => $selected_academic_year . '%',
                        'AcceptedStudent.placementtype' => AUTO_PLACEMENT
                    )
                ));

                $minute_number = $placedstudent[0]['AcceptedStudent']['minute_number'];
                $this->set(compact('minute_number'));
            }

            $dep_id = array();
            $newly_placed_student = array();

            if (!empty($departments)) {
                foreach ($departments as $k => $v) {
                    $dep_id[] = $v['ParticipatingDepartment']['department_id'];
                }

                if (!empty($dep_id)) {
                    $dep_name = $this->AcceptedStudent->Department->find(
                        'list',
                        array('conditions' => array('Department.id' => $dep_id))
                    );
                }

                $newly_placed_student = array();

                if (!empty($dep_name)) {
                    foreach ($dep_name as $dk => $dv) {
                        if (!empty($placedstudent)) {
                            foreach ($placedstudent as $k => $v) {
                                if ($dk == $v['Department']['id']) {
                                    $newly_placed_student[$dv][$k] = $v;
                                }
                            }
                        }

                        $newly_placed_student['auto_summery'][$dv]['C'] = $this->AcceptedStudent->find('count', array(
                            'conditions' => array(
                                'AcceptedStudent.academicyear LIKE' => $selected_academic_year . '%',
                                'AcceptedStudent.department_id' => $dk,
                                'AcceptedStudent.college_id' => $this->college_id,
                                'AcceptedStudent.placement_based' => 'C'
                            )
                        ));

                        $newly_placed_student['auto_summery'][$dv]['Q'] = $this->AcceptedStudent->find('count', array(
                            'conditions' => array(
                                'AcceptedStudent.academicyear LIKE' => $selected_academic_year . '%',
                                'AcceptedStudent.department_id' => $dk,
                                'AcceptedStudent.college_id' => $this->college_id,
                                'AcceptedStudent.placement_based' => 'Q'
                            )
                        ));
                    }
                }
            }

            $this->set('autoplacedstudents', $newly_placed_student);
            $this->set('selected_academicyear', $selected_academic_year);
            $this->set('auto_approve', true);
        }
    }

    // count_result($result=null,$result_type=null)
    public function count_result()
    {

        $this->layout = 'ajax';
        $field = null;
        //debug($this->request->data);

        if (!empty($this->request->data['PlacementsResultsCriteria']['prepartory_result'])) {
            $field = 'AcceptedStudent.EHEECE_total_results';
        } else {
            $field = 'AcceptedStudent.freshman_result';
        }

        $result_count = $this->AcceptedStudent->find('count', array(
            'conditions' => array(
                'AcceptedStudent.academicyear LIKE' => $this->request->data['PlacementsResultsCriteria']['admissionyear'] . '%',
                'AcceptedStudent.college_id' => $this->college_id,
                $field . ' >= ' => $this->request->data['PlacementsResultsCriteria']['result_from'],
                $field . ' <= ' => $this->request->data['PlacementsResultsCriteria']['result_to'],
                "OR" => array(
                    'AcceptedStudent.department_id is null',
                    'AcceptedStudent.department_id = ""',
                    'AcceptedStudent.department_id = 0',
                )
            )
        ));

        if (!empty($this->request->data['PlacementsResultsCriteria']['result_from']) && !empty($this->request->data['PlacementsResultsCriteria']['result_from'])) {
            $from = $this->request->data['PlacementsResultsCriteria']['result_from'];
            $to = $this->request->data['PlacementsResultsCriteria']['result_to'];
            $this->set(compact('from', 'to'));
        }

        $this->set('result_count', $result_count);
    }

    public function print_student_identification()
    {

        $this->layout = 'pdf';
        $this->set('doc_id', '1234567');
        $this->render('print_student_identification');
    }

}
