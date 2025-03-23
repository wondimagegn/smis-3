<?php

namespace App\Controller;

use Cake\Event\Event;


class CourseSubstitutionRequestsController extends AppController
{

    public $name = 'CourseSubstitutionRequests';

    public $menuOptions = array(
        //'parent' => 'courseRegistrations',
        'parent' => 'registrations',
        'exclude' => array(
            'index',
            'search',
            'search2',
            'approve_substitution',
            'add'
        ),
        'alias' => array(
            'list_approved' => 'View Approved Substitution Requests',
            //'add' => 'Add Course Substitution Request',
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
        $this->Auth->allow(
            'search',
            'search2',
            'list_approved'
        );
    }

    public function beforeRender(Event $event)
    {

        parent::beforeRender($event);
        //$acyear_array_data = $this->AcademicYear->acyear_array();

        ///////////////////// DONOT EDIT /////////////////////

        $defaultacademicyear = $this->AcademicYear->currentAcademicYear();

        if (is_numeric(ACY_BACK_FOR_ALL) && ACY_BACK_FOR_ALL) {
            $acyear_array_data = $this->AcademicYear->academicYearInArray(
                ((explode('/', $defaultacademicyear)[0]) - ACY_BACK_FOR_ALL),
                (explode('/', $defaultacademicyear)[0])
            );

            $acYearMinuSeparated = $this->AcademicYear->acYearMinuSeparated(
                ((explode('/', $defaultacademicyear)[0]) - ACY_BACK_FOR_ALL),
                (explode('/', $defaultacademicyear)[0])
            );
            $defaultacademicyearMinusSeparted = str_replace('/', '-', $defaultacademicyear);
        } else {
            $acyear_array_data[$defaultacademicyear] = $defaultacademicyear;

            $defaultacademicyearMinusSeparted = str_replace('/', '-', $defaultacademicyear);
            $acYearMinuSeparated[$defaultacademicyearMinusSeparted] = $defaultacademicyearMinusSeparted;
        }

        ///////////////////// END DONOT EDIT /////////////////////

        $programs = $this->CourseSubstitutionRequest->Student->Program->find(
            'list',
            array('conditions' => array('Program.id' => $this->program_ids))
        );
        $program_types = $programTypes = $this->CourseSubstitutionRequest->Student->ProgramType->find(
            'list',
            array('conditions' => array('ProgramType.id' => $this->program_type_ids))
        );

        $departments = array();
        $colleges = array();

        if ($this->Session->read('Auth.User')['role_id'] == ROLE_DEPARTMENT) {
            $departments = $this->CourseSubstitutionRequest->Student->Department->find(
                'list',
                array('conditions' => array('Department.id' => $this->department_id, 'Department.active' => 1))
            );
        } else {
            if ($this->Session->read('Auth.User')['role_id'] == ROLE_COLLEGE) {
                $departments = $this->CourseSubstitutionRequest->Student->Department->find(
                    'list',
                    array('conditions' => array('Department.college_id' => $this->college_id, 'Department.active' => 1))
                );
            } else {
                if ($this->Session->read('Auth.User')['role_id'] == ROLE_REGISTRAR) {
                    if (!empty($this->department_ids)) {
                        $colleges = array();
                        $departments = $this->CourseSubstitutionRequest->Student->Department->find(
                            'list',
                            array(
                                'conditions' => array(
                                    'Department.id' => $this->department_ids,
                                    'Department.active' => 1
                                )
                            )
                        );
                    } else {
                        if (!empty($this->college_ids)) {
                            $departments = array();
                            $colleges = $this->CourseSubstitutionRequest->Student->College->find(
                                'list',
                                array('conditions' => array('College.id' => $this->college_ids, 'College.active' => 1))
                            );
                        }
                    }
                }
            }
        }

        $this->set(
            compact(
                'defaultacademicyearMinusSeparted',
                'acYearMinuSeparated',
                'defaultacademicyear',
                'acyear_array_data',
                'program_types',
                'programs',
                'programTypes',
                'colleges',
                'departments'
            )
        );

        unset($this->request->data['User']['password']);
    }


    public function search()
    {

        // the page we will redirect to
        $url['action'] = 'index';

        if (!empty($this->request->data)) {
            if (isset($this->request->data) && !empty($this->request->data)) {
                foreach ($this->request->data as $k => $v) {
                    foreach ($v as $kk => $vv) {
                        $url[$k . '.' . $kk] = str_replace('/', '-', trim($vv));
                    }
                }
            }
        }

        return $this->redirect($url, null, true);
    }

    public function search2()
    {

        // the page we will redirect to
        $url['action'] = 'list_approved';

        if (!empty($this->request->data)) {
            if (isset($this->request->data) && !empty($this->request->data)) {
                foreach ($this->request->data as $k => $v) {
                    foreach ($v as $kk => $vv) {
                        $url[$k . '.' . $kk] = str_replace('/', '-', trim($vv));
                    }
                }
            }
        }

        return $this->redirect($url, null, true);
    }

    public function index()
    {

        return $this->redirect(array('action' => 'list_approved'));
        /* $limit = 100;
        $name = '';
        $studentnumber = '';
        $default_department_id =  '';
        $default_college_id =  '';

        $options = array();

        if (isset($this->passedArgs) && !empty($this->passedArgs)) {

            debug($this->passedArgs);

            if (!empty($this->passedArgs['Search.limit'])) {
                $limit = $this->request->data['Search']['limit'] = $this->passedArgs['Search.limit'];
            }

            if (!empty($this->passedArgs['Search.name'])) {
                $name = $this->request->data['Search']['name'] = $this->passedArgs['Search.name'];
            }

            if (!empty($this->passedArgs['Search.department_id'])) {
                $default_department_id = $this->request->data['Search']['department_id'] = $this->passedArgs['Search.department_id'];
            }

            if (!empty($this->passedArgs['Search.college_id'])) {
                $default_college_id = $this->request->data['Search']['college_id'] = $this->passedArgs['Search.college_id'];
            }

            if (isset($this->passedArgs['Search.academic_year'])) {
                $selected_academic_year = str_replace('-', '/', $this->passedArgs['Search.academic_year']);
                $this->request->data['Search']['academic_year'] = $this->passedArgs['Search.academic_year'];
            } else {
                $selected_academic_year = '';
            }

            if (isset($this->passedArgs['Search.semester'])) {
                $this->request->data['Search']['semester'] = $this->passedArgs['Search.semester'];
            }

            if (isset($this->passedArgs['Search.program_id'])) {
                $this->request->data['Search']['program_id'] = $this->passedArgs['Search.program_id'];
            }

            if (isset($this->passedArgs['Search.program_type_id'])) {
                $this->request->data['Search']['program_type_id'] = $this->passedArgs['Search.program_type_id'];
            }

            if (isset($this->passedArgs['Search.graduated'])) {
                $this->request->data['Search']['graduated'] = $this->passedArgs['Search.graduated'];
            }

            if (isset($this->passedArgs['Search.studentnumber'])) {
                $studentnumber = $this->request->data['Search']['studentnumber'] = str_replace('-', '/', trim($this->passedArgs['Search.studentnumber']));
            }

            if (isset($this->passedArgs['Search.status'])) {
                $this->request->data['Search']['status'] = $this->passedArgs['Search.status'];
            }

        }

        if (isset($this->request->data) && !empty($this->request->data)) {

            debug($this->request->data);

            if ($this->Session->read('Auth.User')['role_id'] == ROLE_DEPARTMENT) {
                $options[] = array('Student.department_id' => $this->department_id);

                if ($this->request->data['Search']['status'] == 'notprocessed') {
                    $options[] = array('CourseSubstitutionRequest.department_approve is null');
                } else if ($this->request->data['Search']['status'] == 'accepted') {
                    $options[] = array('CourseSubstitutionRequest.department_approve' => 1);
                } else if ($this->request->data['Search']['status'] == 'rejected') {
                    $options[] = array('CourseSubstitutionRequest.department_approve' => 0);
                }
                $this->request->data['Search']['department_id'] = $this->department_id;

            } else if ($this->Session->read('Auth.User')['role_id'] == ROLE_COLLEGE) {
                if (!empty($this->request->data['Search']['department_id'])) {
                    $options[] = array('Student.department_id' => $this->request->data['Search']['department_id']);
                } else {
                    $options[] = array('Student.college_id' => $this->college_id);
                }

                if ($this->request->data['Search']['status'] == 'notprocessed') {
                    $options[] = array('CourseSubstitutionRequest.department_approve is null');
                } else if ($this->request->data['Search']['status'] == 'accepted') {
                    $options[] = array('CourseSubstitutionRequest.department_approve' => 1);
                } else if ($this->request->data['Search']['status'] == 'rejected') {
                    $options[] = array('CourseSubstitutionRequest.department_approve' => 0);
                }

                $default_college_id = $this->college_id;

            } else if ($this->Session->read('Auth.User')['role_id'] == ROLE_REGISTRAR) {

                if (!empty($this->department_ids)) {
                    if (!empty($this->request->data['Search']['department_id'])) {
                        $options[] = array('Student.department_id' => $this->request->data['Search']['department_id']);
                    } else {
                        $options[] = array('Student.department_id' => array_keys($this->department_ids));
                    }
                } else if (!empty($this->college_ids)) {
                    $departments = array();
                    if (!empty($this->request->data['Search']['college_id'])) {
                        $options[] = array('Student.college_id' => $this->request->data['Search']['college_id'], 'Student.department_id IS NULL');
                    } else {
                        $options[] = array('Student.college_id' => array_keys($this->college_ids) , 'Student.department_id IS NULL');
                    }
                }

                if ($this->request->data['Search']['status'] == 'notprocessed') {
                    $options[] = array('CourseSubstitutionRequest.department_approve is null');
                } else if ($this->request->data['Search']['status'] == 'accepted') {
                    $options[] = array('CourseSubstitutionRequest.department_approve' => 1);
                } else if ($this->request->data['Search']['status'] == 'rejected') {
                    $options[] = array('CourseSubstitutionRequest.department_approve' => 0);
                }

            } else  if ($this->Session->read('Auth.User')['role_id'] == ROLE_STUDENT) {
                unset($this->passedArgs);
                unset($this->request->data);
                return $this->redirect(array('action' => 'index'));
            } else {

                $departments = $this->CourseSubstitutionRequest->Student->Department->find('list', array(
                    'conditions' => array(
                        'Department.active' => 1
                    )
                ));

                $colleges = $this->CourseSubstitutionRequest->Student->College->find('list', array(
                    'conditions' => array(
                        'College.active' => 1
                    )
                ));

                if (!empty($this->request->data['Search']['department_id'])) {
                    $options[] = array('Student.department_id' => $this->request->data['Search']['department_id']);
                } else if (empty($this->request->data['Search']['department_id']) && !empty($this->request->data['Search']['college_id'])) {
                    $departments = $this->CourseSubstitutionRequest->Student->Department->find('list', array(
                        'conditions' => array(
                            'Department.college_id' => $this->request->data['Search']['college_id'],
                            'Department.active' => 1
                        )
                    ));
                    $options[] = array(
                        'OR' => array(
                            'Student.college_id' => $this->request->data['Search']['college_id'],
                            'Student.department_id' => array_keys($departments)
                        )
                    );
                } else {
                    $options[] = array(
                        'OR' => array(
                            'Student.college_id' => array_keys($colleges),
                            'Student.department_id' => array_keys($departments)
                        )
                    );
                }

                if ($this->request->data['Search']['status'] == 'notprocessed') {
                    $options[] = array('CourseSubstitutionRequest.department_approve is null');
                } else if ($this->request->data['Search']['status'] == 'accepted') {
                    $options[] = array('CourseSubstitutionRequest.department_approve' => 1);
                } else if ($this->request->data['Search']['status'] == 'rejected') {
                    $options[] = array('CourseSubstitutionRequest.department_approve' => 0);
                }
            }

            // if (!empty($selected_academic_year)) {
            // 	$options[] = array('CourseSubstitutionRequest.academic_year' => $selected_academic_year);
            // }

            if (!empty($this->request->data['Search']['program_id'])) {
                $options[] = array('Student.program_id' => $this->request->data['Search']['program_id']);
            } else if (empty($this->request->data['Search']['program_id']) && $this->Session->read('Auth.User')['role_id'] == ROLE_REGISTRAR) {
                $options[] = array('Student.program_id' => $this->program_id);
            }

            if (!empty($this->request->data['Search']['program_type_id'])) {
                $options[] = array('Student.program_type_id' => $this->request->data['Search']['program_type_id']);
            } else if (empty($this->request->data['Search']['program_type_id']) && $this->Session->read('Auth.User')['role_id'] == ROLE_REGISTRAR) {
                $options[] = array('Student.program_type_id' => $this->program_type_id);
            }

            if (isset($name) && !empty($name)) {
                $options[] = array(
                    'OR' => array(
                        'Student.first_name LIKE ' => '%' . $name . '%',
                        'Student.middle_name LIKE ' =>  '%' . $name . '%',
                        'Student.last_name LIKE' =>  '%' . $name . '%',
                    )
                );
            }

            if (isset($this->request->data['Search']['college_id']) && !empty($this->request->data['Search']['college_id']) && $this->Session->read('Auth.User')['role_id'] != ROLE_REGISTRAR && $this->Session->read('Auth.User')['role_id'] != ROLE_DEPARTMENT) {
                $departments = $this->CourseSubstitutionRequest->Student->Department->find('list', array(
                    'conditions' => array(
                        'Department.college_id' => $this->request->data['Search']['college_id'],
                        'Department.active' => 1
                    )
                ));
            }

            if ($this->request->data['Search']['graduated'] == 0) {
                $options[] = array('Student.graduated = 0');
            } else if ($this->request->data['Search']['graduated'] == 1) {
                $options[] = array('Student.graduated = 1');
            }

            if (isset($studentnumber) && !empty($studentnumber)) {
                $options[] = array('Student.studentnumber' => trim($studentnumber));
            }

        } else {

            if ($this->Session->read('Auth.User')['role_id'] == ROLE_COLLEGE) {
                $options[] = array(
                    'OR' => array(
                        'Student.college_id' => $this->college_id,
                        'Student.department_id' => array_keys($this->department_ids)
                    )
                );
                $options[] = array('CourseSubstitutionRequest.department_approve is null');
            } else if ($this->Session->read('Auth.User')['role_id'] == ROLE_DEPARTMENT) {
                $options[] = array('Student.department_id' => $this->department_id);
                $options[] = array('CourseSubstitutionRequest.department_approve is null');
                $default_department_id = $this->department_id;
            } else if ($this->Session->read('Auth.User')['role_id'] == ROLE_REGISTRAR) {
                if (!empty($this->department_ids)) {
                    $options[] = array('Student.department_id' => $this->department_ids, 'Student.program_id' => $this->program_id, 'Student.program_type_id' => $this->program_type_id);
                } else if (!empty($this->college_ids)) {
                    $options[] = array('Student.college_id' => array_keys($this->college_ids), 'Student.department_id IS NULL', 'Student.program_id' => $this->program_id, 'Student.program_type_id' => $this->program_type_id);
                }
                $options[] = array('CourseSubstitutionRequest.department_approve is null');
            } else if ($this->Session->read('Auth.User')['role_id'] == ROLE_STUDENT) {
                $options[] = array('Student.id' => $this->student_id);
            } else {
                $departments = $this->CourseSubstitutionRequest->Student->Department->find('list', array(
                    'conditions' => array(
                        'Department.active' => 1
                    )
                ));
                $colleges = $this->CourseSubstitutionRequest->Student->College->find('list', array(
                    'conditions' => array(
                        'College.active' => 1
                    )
                ));
                $options[] = array(
                    'OR' => array(
                        'Student.department_id' => array_keys($departments),
                        'Student.college_id' => array_keys($colleges)
                    )
                );
                $options[] = array('CourseSubstitutionRequest.department_approve is null');
            }
            $options[] = array('Student.graduated = 0');
        }


        $this->Paginator->settings = array(
            'contain' => array(
                'Student' => array(
                    'fields' => array('id', 'full_name', 'gender', 'studentnumber', 'graduated'),
                    'Department' => array('id', 'name'),
                ),
                'CourseForSubstitued' => array(
                    'Department' => array('id', 'name'),
                    'Curriculum' => array('id', 'name', 'year_introduced', 'type_credit'),
                ),
                'CourseBeSubstitued' => array(
                    'Department' => array('id', 'name'),
                    'Curriculum' => array('id', 'name', 'year_introduced', 'type_credit'),
                )
            ),
            'limit' => $limit,
            'maxLimit' => $limit,
            'recursive' => -1
        );

        debug($options);

        $courseSubstitutionRequests = $this->paginate($options);

        if (empty($courseSubstitutionRequests)) {
            $this->Flash->info('There is no course substitution request in the system in the given criteria.');
            $turn_off_search = false;
        } else {
            //debug($courseSubstitutionRequests[0]);
            $turn_off_search = false;
        }

        $this->set(compact('courseSubstitutionRequests', 'programs', 'programTypes', 'departments', 'colleges', 'limit', 'name', 'studentnumber', 'turn_off_search', 'default_department_id', 'default_college_id')); */
    }

    public function view($id = null)
    {

        if (!$id) {
            $this->Flash->error('Invalid course substitution request.');
            return $this->redirect(array('action' => 'index'));
        }

        $this->CourseSubstitutionRequest->id = $id;

        if (!$this->CourseSubstitutionRequest->exists()) {
            $this->Flash->error('Invalid course substitution request.');
            return $this->redirect(array('action' => 'index'));
        }

        $courseSubstitutionRequest = $this->CourseSubstitutionRequest->find('first', array(
            'conditions' => array(
                'CourseSubstitutionRequest.id' => $id
            ),
            'contain' => array(
                'Student' => array('id', 'full_name'),
                'CourseForSubstitued',
                'CourseBeSubstitued'
            )
        ));

        $this->set('courseSubstitutionRequest', $courseSubstitutionRequest);
    }

    public function add()
    {

        if (!empty($this->request->data)) {
            //check duplicate entry
            $compare_with_master_equivalencey = ClassRegistry::init('EquivalentCourse')->find('count', array(
                'conditions' => array(
                    'EquivalentCourse.course_for_substitued_id' => $this->request->data['CourseSubstitutionRequest']['course_for_substitued_id'],
                    'EquivalentCourse.course_be_substitued_id' => $this->request->data['CourseSubstitutionRequest']['course_be_substitued_id']
                )
            ));

            if ($compare_with_master_equivalencey == 0) {
                //$this->request->data['CourseSubstitutionRequest']=$this->request->data['EquivalentCourse'];
                $this->request->data['CourseSubstitutionRequest']['student_id'] = $this->request->data['EquivalentCourse']['student_id'];

                $duplicated = $this->CourseSubstitutionRequest->find('count', array(
                    'conditions' => array(
                        'CourseSubstitutionRequest.course_be_substitued_id' => $this->request->data['CourseSubstitutionRequest']['course_be_substitued_id'],
                        'CourseSubstitutionRequest.course_for_substitued_id' => $this->request->data['CourseSubstitutionRequest']['course_for_substitued_id'],
                        'CourseSubstitutionRequest.student_id' => $this->request->data['CourseSubstitutionRequest']['student_id']
                    )
                ));

                if ($duplicated == 0) {
                    $this->CourseSubstitutionRequest->create();
                    $this->request->data['CourseSubstitutionRequest']['request_date'] = date('Y-m-d');

                    if ($this->CourseSubstitutionRequest->isSimilarCurriculum($this->request->data)) {
                        if ($this->CourseSubstitutionRequest->save($this->request->data)) {
                            $this->Flash->success('The course substitution request has been saved');
                            $this->redirect(array('action' => 'index'));
                        } else {
                            $this->Flash->error(
                                'The course substitution request could not be saved. Please, try again.'
                            );
                            // $this->request->data=$this->__reformat($this->request->data);
                        }
                    } else {
                        $error = $this->CourseSubstitutionRequest->invalidFields();
                        if (isset($error['error'])) {
                            $this->Flash->error($error['error'][0]);
                        }
                    }
                } else {
                    $this->Flash->error(
                        'The course substitution request could not be saved. You have already requested course exemptions for the selected courses.'
                    );
                    $this->redirect(array('action' => 'index'));
                }
            } else {
                $this->Flash->error(
                    'You dont need to request subtitution for the selected courses, it will be automatically substitued since the department has already mapped the courses you selected as equivalent.'
                );
            }
        }

        $current_academic_year = $this->AcademicYear->current_academicyear();
        $student_section_exam_status = $this->CourseSubstitutionRequest->Student->get_student_section(
            $this->student_id,
            $current_academic_year
        );

        $courseForSubstitueds = $this->CourseSubstitutionRequest->CourseForSubstitued->find('list', array(
            'conditions' => array(
                'CourseForSubstitued.curriculum_id' => $student_section_exam_status['StudentBasicInfo']['curriculum_id']
            ),
            'fields' => array('id', 'course_code', 'course_title')
        ));

        $previous_substitution_accepted = $this->CourseSubstitutionRequest->find('all', array(
            'conditions' => array(
                'CourseSubstitutionRequest.student_id' => $student_section_exam_status['StudentBasicInfo']['id'],
                'CourseSubstitutionRequest.department_approve' => 1
            )
        ));

        //$courseBeSubstitueds = $this->EquivalentCourse->CourseBeSubstitued->find('list');
        $departments = $this->CourseSubstitutionRequest->CourseBeSubstitued->Department->find('all', array(
            'conditions' => array('Department.active' => 1),
            'contain' => array('College' => array('id', 'name')),
            'fields' => array('id', 'name'),
        ));

        $return = array();

        if (!empty($departments)) {
            foreach ($departments as $dep_id => $dep_name) {
                $return[$dep_name['College']['name']][$dep_name['Department']['id']] = $dep_name['Department']['name'];
            }
        }

        $departments = $return;

        $curriculums = $this->CourseSubstitutionRequest->CourseBeSubstitued->Curriculum->find('list', array(
            'conditions' => array(
                'Curriculum.department_id' => $this->department_id,
                'Curriculum.active' => 1,
                'Curriculum.registrar_approved' => 1
            ),
            'fields' => array('id', 'curriculum_detail'),
        ));


        if (empty($this->request->data)) {
            $courseBeSubstitueds = array();
            $otherCurriculums = array();
        }

        if (empty($this->request->data['CourseSubstitutionRequest']['other_curriculum_id'])) {
            $otherCurriculums = array();
        }

        if (!empty($this->request->data['CourseSubstitutionRequest']['other_curriculum_id'])) {
            $other_department_id = $this->CourseSubstitutionRequest->CourseBeSubstitued->Curriculum->field(
                'department_id',
                array('Curriculum.id' => $this->request->data['CourseSubstitutionRequest']['other_curriculum_id'])
            );
            $otherCurriculums = $this->CourseSubstitutionRequest->CourseBeSubstitued->Curriculum->find('list', array(
                'conditions' => array(
                    'Curriculum.department_id' => $other_department_id,
                    'Curriculum.active' => 1,
                    'Curriculum.registrar_approved' => 1
                ),
                'fields' => array('id', 'curriculum_detail'),
            ));
        }

        if (!empty($this->request->data['CourseSubstitutionRequest']['course_be_substitued_id'])) {
            $curriculum_id = $this->CourseSubstitutionRequest->CourseBeSubstitued->field(
                'curriculum_id',
                array('CourseBeSubstitued.id' => $this->request->data['CourseSubstitutionRequest']['course_be_substitued_id'])
            );

            $courseBeSubstitueds = $this->CourseSubstitutionRequest->CourseBeSubstitued->find('list', array(
                'conditions' => array('CourseBeSubstitued.curriculum_id' => $curriculum_id),
                'fields' => array('id', 'course_code', 'course_title')
            ));
        } else {
            if (!empty($this->request->data['CourseSubstitutionRequest']['other_curriculum_id'])) {
                $courseBeSubstitueds = $this->CourseSubstitutionRequest->CourseBeSubstitued->find('list', array(
                    'conditions' => array('CourseBeSubstitued.curriculum_id' => $this->request->data['CourseSubstitutionRequest']['other_curriculum_id']),
                    'fields' => array('id', 'course_code', 'course_title')
                ));
            }
        }

        $this->set(
            compact(
                'courseForSubstitueds',
                'courseBeSubstitueds',
                'departments',
                'curriculums',
                'student_section_exam_status',
                'otherCurriculums',
                'previous_substitution_accepted'
            )
        );
    }

    public function __reformat($data = null)
    {

        /*
        foreach ($data['ReturnedItemsList'] as $key => &$returneditem) {
            $itemSubCategories = $this->ReturnedItem->ReturnedItemsList->Item->ItemSubCategory->getListOfSubCategories($issueditem['item_main_category_id']);
            $returneditem['itemSubCategories'] = $itemSubCategories;

            if (isset($returneditem['item_sub_category_id'])) {
                $items = $this->ReturnedItem->ReturnedItemsList->Item->getListOfItems($returneditem['item_sub_category_id']);
            } else {
                $items = array();
            }

            if (!empty($itemSubCategories)) {
                $returneditem['items'] = $items;
            } else {
                $returneditem['items'] = array();
            }
        }
        return $data;
        */

        $curriculums = $this->CourseSubstitutionRequest->CourseBeSubstitued->Curriculum->find('list', array(
            'conditions' => array('Curriculum.department_id' => $data['CourseSubstitutionRequest']['department_id']),
            'fields' => array('id', 'curriculum_detail'),
        ));

        $courseBeSubstitueds = $this->CourseSubstitutionRequest->CourseBeSubstitued->find('list', array(
            'conditions' => array(
                'CourseBeSubstitued.curriculum_id' => $data['CourseSubstitutionRequest']['other_curriculum_id']
            ),
            'fields' => array('id', 'course_title')
        ));

        $data['curriculum'] = $curriculums;
        $data['courseBeSubstitued'] = $courseBeSubstitueds;

        return $data;
    }

    public function edit($id = null)
    {
        /* if (!$id && empty($this->request->data)) {
            $this->Flash->error('Invalid course substitution request');
            return $this->redirect(array('action' => 'index'));
        }

        if (!empty($this->request->data)) {
            if ($this->CourseSubstitutionRequest->save($this->request->data)) {
                $this->Flash->success('The course substitution request has been saved.');
                return $this->redirect(array('action' => 'index'));
            } else {
                $this->Flash->error('The course substitution request could not be saved. Please, try again.');
            }
        }

        if (empty($this->request->data)) {
            $this->request->data = $this->CourseSubstitutionRequest->read(null, $id);
        }

        $courseForSubstitueds = $this->CourseSubstitutionRequest->CourseForSubstitued->find('list', array(
            'conditions' => array(
                'CourseForSubstitued.curriculum_id' => $student_section_exam_status['StudentBasicInfo']['curriculum_id']
            ),
            'fields' => array('id', 'course_title')
        ));

        $courseBeSubstitueds = $this->CourseSubstitutionRequest->CourseBeSubstitued->find('list', array(
            'fields' => array('id', 'course_title')
        ));

        $this->set(compact(
            'students',
            'courseForSubstitueds',
            'courseBeSubstitueds',
            'student_section_exam_status'
        )); */
    }

    public function delete($id = null)
    {

        if (!$id) {
            $this->Flash->error('Invalid id for course substitution request.');
            return $this->redirect(array('action' => 'index'));
        }

        if ($this->CourseSubstitutionRequest->delete($id)) {
            $this->Flash->success('Course substitution request deleted.');
            return $this->redirect(array('action' => 'index'));
        }

        $this->Flash->error('Course substitution request was not deleted');
        return $this->redirect(array('action' => 'index'));
    }

    public function approve_substitution($id = null)
    {

        if ($id) {
            $this->CourseSubstitutionRequest->id = $id;

            if (!$this->CourseSubstitutionRequest->exists()) {
                $this->Flash->error('Invalid course substitution request.');
                $this->redirect(array('action' => 'index'));
            }

            $check = $this->CourseSubstitutionRequest->find('count', array(
                'conditions' => array(
                    'CourseSubstitutionRequest.id' => $id,
                    'Student.department_id' => $this->department_id
                )
            ));

            $check2 = $this->CourseSubstitutionRequest->find('count', array(
                'conditions' => array(
                    'CourseSubstitutionRequest.id' => $id,
                    'OR' => array(
                        'CourseSubstitutionRequest.department_approve = 1',
                        'CourseSubstitutionRequest.department_approve = 0',
                    )
                )
            ));

            if ($check == 0) {
                $this->Flash->error(
                    'You are not elegible to approve the selected student course substitution request.'
                );
                $this->redirect(array('action' => 'index'));
            }

            if ($check2) {
                $this->Flash->error('The selected course substitution request is already processed.');
                $this->redirect(array('action' => 'index'));
            }

            $student_detail = $this->CourseSubstitutionRequest->find('first', array(
                'conditions' => array(
                    'CourseSubstitutionRequest.id' => $id,
                ),
                'contain' => array(
                    'Student' => array('id', 'full_name', 'studentnumber', 'graduated'),
                ),
                'recursive' => -1
            ));

            //debug($student_detail);

            if ($student_detail['Student']['graduated']) {
                $this->Flash->error(
                    $student_detail['Student']['full_name'] . ' (' . $student_detail['Student']['studentnumber'] . ') is graduated. Course substitution request is already expired.'
                );
                $this->redirect(array('action' => 'index'));
            }
        }

        if (!empty($this->request->data) && isset($id) && isset($this->request->data['approveRejectSubstitutionRequest'])) {
            $this->CourseSubstitutionRequest->id = $id;

            if (!$this->CourseSubstitutionRequest->exists()) {
                $this->Flash->error('Invalid course substitution request.');
                $this->redirect(array('action' => 'index'));
            }

            debug($this->request->data);

            $this->request->data['CourseSubstitutionRequest']['department_approve_by'] = $this->Auth->user('id');

            if ($this->CourseSubstitutionRequest->save($this->request->data)) {
                // if accepted course subtitution  and approved postitive, save it on master course mapping table.
                if ($this->request->data['CourseSubstitutionRequest']['department_approve'] == 1 && !empty($this->request->data['CourseSubstitutionRequest']['department_approve_by'])) {
                    $existing_mapping = ClassRegistry::init('EquivalentCourse')->find('count', array(
                        'conditions' => array(
                            'EquivalentCourse.course_for_substitued_id' => $this->request->data['CourseSubstitutionRequest']['course_for_substitued_id'],
                            'EquivalentCourse.course_be_substitued_id' => $this->request->data['CourseSubstitutionRequest']['course_be_substitued_id']
                        )
                    ));

                    //debug($existing_mapping);

                    if (!$existing_mapping) {
                        $equivalentCourse = array();
                        $equivalentCourse['EquivalentCourse']['course_for_substitued_id'] = $this->request->data['CourseSubstitutionRequest']['course_for_substitued_id'];
                        $equivalentCourse['EquivalentCourse']['course_be_substitued_id'] = $this->request->data['CourseSubstitutionRequest']['course_be_substitued_id'];

                        if (ClassRegistry::init('EquivalentCourse')->save($equivalentCourse)) {
                            $this->Flash->success(
                                'The course substitution request has been approved and the course mapping is also updated for all students who have same course substitution request.'
                            );
                        } else {
                            $this->Flash->success(
                                'The course substitution request has been approved but couldn\t save course mapping.'
                            );
                        }
                    } else {
                        $this->Flash->success('The course substitution request has been approved.');
                    }
                } else {
                    $this->Flash->warning('The course substitution request has been rejected');
                }
                $this->redirect(array('action' => 'index'));
            } else {
                $this->Flash->error('The course substitution request could not be saved. Please, try again.');
            }
        }

        if (empty($this->request->data) && isset($id)) {
            $this->CourseSubstitutionRequest->id = $id;

            if (!$this->CourseSubstitutionRequest->exists()) {
                $this->Flash->error('Invalid course substitution request.');
                $this->redirect(array('action' => 'index'));
            }

            //$this->request->data = $this->CourseSubstitutionRequest->read(null, $id);

            $this->request->data = $this->CourseSubstitutionRequest->find('first', array(
                'conditions' => array(
                    'CourseSubstitutionRequest.id' => $id,
                ),
                'contain' => array(
                    'Student' => array(
                        'fields' => array('id', 'full_name', 'gender', 'studentnumber', 'graduated'),
                        'Department' => array('id', 'name'),
                    ),
                    'CourseForSubstitued' => array(
                        'Department' => array('id', 'name'),
                        'Curriculum' => array('id', 'name', 'year_introduced', 'type_credit'),
                    ),
                    'CourseBeSubstitued' => array(
                        'Department' => array('id', 'name'),
                        'Curriculum' => array('id', 'name', 'year_introduced', 'type_credit'),
                    )
                )
            ));

            debug($this->request->data);
        } else {
            if (empty($this->request->data) && !isset($id)) {
                $conditions = array();

                $conditions[] = array(
                    'Student.graduated' => 0,
                    'CourseSubstitutionRequest.department_approve is null',
                    'CourseSubstitutionRequest.request_date >= ' => date(
                        "Y-m-d",
                        strtotime("-" . DAYS_BACK_COURSE_SUBSTITUTION . " day")
                    ),
                );

                if ($this->Session->read('Auth.User')['role_id'] == ROLE_COLLEGE) {
                    $departments = $this->CourseSubstitutionRequest->Student->Department->find('list', array(
                        'conditions' => array(
                            'Department.college_id' => $this->college_id,
                            'Department.active' => 1
                        ),
                    ));
                    $conditions[] = array(
                        'OR' => array(
                            'Student.college_id' => $this->college_id,
                            'Student.department_id' => array_keys($departments)
                        )
                    );
                } else {
                    if ($this->Session->read('Auth.User')['role_id'] == ROLE_DEPARTMENT) {
                        $conditions[] = array('Student.department_id' => $this->department_id);
                    } else {
                        if ($this->Session->read('Auth.User')['role_id'] == ROLE_REGISTRAR) {
                            if (!empty($this->department_ids)) {
                                $conditions[] = array(
                                    'Student.department_id' => $this->department_ids,
                                    'Student.program_id' => $this->program_id,
                                    'Student.program_type_id' => $this->program_type_id
                                );
                            } else {
                                if (!empty($this->college_ids)) {
                                    $colleges = $this->CourseSubstitutionRequest->Student->College->find('list', array(
                                        'conditions' => array(
                                            'College.id' => $this->college_ids,
                                            'College.active' => 1
                                        )
                                    ));
                                    $conditions[] = array(
                                        'Student.college_id' => array_keys($colleges),
                                        'Student.department_id IS NULL',
                                        'Student.program_id' => $this->program_id,
                                        'Student.program_type_id' => $this->program_type_id
                                    );
                                }
                            }
                        }
                    }
                }

                $courseSubstitutionRequests = $this->CourseSubstitutionRequest->find('all', array(
                    'conditions' => $conditions,
                    'contain' => array(
                        'Student' => array(
                            'fields' => array('id', 'full_name', 'gender', 'studentnumber', 'graduated'),
                            'Department' => array('id', 'name'),
                        ),
                        'CourseForSubstitued' => array(
                            'Department' => array('id', 'name'),
                            'Curriculum' => array('id', 'name', 'year_introduced', 'type_credit'),
                        ),
                        'CourseBeSubstitued' => array(
                            'Department' => array('id', 'name'),
                            'Curriculum' => array('id', 'name', 'year_introduced', 'type_credit'),
                        )
                    ),
                    'recusive' => -1
                ));

                if (count($courseSubstitutionRequests)) {
                    $this->set(compact('courseSubstitutionRequests'));
                } else {
                    //$this->redirect(array('action' => 'index'));
                }
            }
        }

        if (isset($this->request->data['CourseSubstitutionRequest']) && !empty($this->request->data['CourseSubstitutionRequest'])) {
            //$current_academic_year = $this->AcademicYear->current_academicyear();
            //$student_section_exam_status = $this->CourseSubstitutionRequest->Student->get_student_section($this->request->data['CourseSubstitutionRequest']['student_id'], $current_academic_year);

            $student_section_exam_status = ClassRegistry::init('Student')->get_student_section(
                $this->request->data['CourseSubstitutionRequest']['student_id'],
                null,
                null
            );

            /* $alreadyGeneratedStatus = ClassRegistry::init('StudentExamStatus')->find('all', array(
                'conditions' => array(
                    'StudentExamStatus.student_id' => $this->request->data['CourseSubstitutionRequest']['student_id'],
                ),
                'contain' => array(
                    'Student' => array(
                        'College' => array('id', 'name'),
                        'Department' => array('id', 'name'),
                        'Program' => array('id', 'name'),
                        'ProgramType' => array('id', 'name'),
                        'fields' => array('id', 'full_name', 'gender', 'studentnumber', 'curriculum_id', 'graduated','academicyear'),
                    ),
                    'AcademicStatus'=> array('id', 'name', 'computable'),
                )
            )); */

            $previous_substitution_accepted = $this->CourseSubstitutionRequest->find('all', array(
                'conditions' => array(
                    'CourseSubstitutionRequest.student_id' => $this->request->data['CourseSubstitutionRequest']['student_id'],
                    'CourseSubstitutionRequest.department_approve' => 1
                ),
                'contain' => array(
                    'Student' => array(
                        'fields' => array('id', 'full_name', 'gender', 'studentnumber', 'graduated'),
                        'Department' => array('id', 'name'),
                    ),
                    'CourseForSubstitued' => array(
                        'Department' => array('id', 'name'),
                        'Curriculum' => array('id', 'name', 'year_introduced', 'type_credit'),
                    ),
                    'CourseBeSubstitued' => array(
                        'Department' => array('id', 'name'),
                        'Curriculum' => array('id', 'name', 'year_introduced', 'type_credit'),
                    )
                ),
                'recusive' => -1
            ));

            $this->set(
                compact(/* 'alreadyGeneratedStatus',  */ 'student_section_exam_status',
                    'previous_substitution_accepted'
                )
            );
        }
    }

    public function list_approved()
    {

        $limit = 200;
        $name = '';
        $studentnumber = '';
        $default_department_id = '';
        $default_college_id = '';
        $default_program_id = '';
        $default_curriculum_id = '';


        if (isset($this->passedArgs) && !empty($this->passedArgs)) {
            debug($this->passedArgs);

            if (!empty($this->passedArgs['Search2.limit'])) {
                $limit = $this->request->data['Search2']['limit'] = $this->passedArgs['Search2.limit'];
            }

            if (!empty($this->passedArgs['Search2.name'])) {
                $name = $this->request->data['Search2']['name'] = $this->passedArgs['Search2.name'];
            }

            if (!empty($this->passedArgs['Search2.department_id'])) {
                $default_department_id = $this->request->data['Search2']['department_id'] = $this->passedArgs['Search2.department_id'];
            }

            if (!empty($this->passedArgs['Search2.college_id'])) {
                $default_college_id = $this->request->data['Search2']['college_id'] = $this->passedArgs['Search2.college_id'];
            }

            if (isset($this->passedArgs['Search2.curriculum_id'])) {
                $default_curriculum_id = $this->request->data['Search2']['curriculum_id'] = $this->passedArgs['Search2.curriculum_id'];
            }

            if (isset($this->passedArgs['Search2.program_id'])) {
                $default_program_id = $this->request->data['Search2']['program_id'] = $this->passedArgs['Search2.program_id'];
            }


            if (isset($this->passedArgs['Search2.studentnumber'])) {
                $studentnumber = $this->request->data['Search2']['studentnumber'] = str_replace(
                    '-',
                    '/',
                    trim($this->passedArgs['Search2.studentnumber'])
                );
            }

            if (isset($this->passedArgs['Search2.status'])) {
                $this->request->data['Search2']['status'] = $this->passedArgs['Search2.status'];
            }
        }

        $programs = $this->CourseSubstitutionRequest->Student->Curriculum->Program->find('list');

        if ($this->Session->read('Auth.User')['role_id'] == ROLE_DEPARTMENT) {
            $departments = $this->CourseSubstitutionRequest->Student->Department->find('list', array(
                'conditions' => array(
                    'Department.id' => $this->department_id,
                    'Department.active' => 1
                ),
            ));

            $this->request->data['Search2']['department_id'] = $default_department_id = $this->department_id;
        } else {
            if ($this->Session->read('Auth.User')['role_id'] == ROLE_COLLEGE) {
                $departments = $this->CourseSubstitutionRequest->Student->Department->find('list', array(
                    'conditions' => array(
                        'Department.college_id' => $this->college_id,
                        'Department.active' => 1
                    ),
                ));

                $default_college_id = $this->college_id;
            } else {
                if ($this->Session->read('Auth.User')['role_id'] == ROLE_REGISTRAR) {
                    $programs = $this->CourseSubstitutionRequest->Student->Curriculum->Program->find(
                        'list',
                        array('conditions' => array('Program.id' => $this->program_id))
                    );

                    if (!empty($this->department_ids)) {
                        $colleges = array();

                        $departments = $this->CourseSubstitutionRequest->Student->Department->find('list', array(
                            'conditions' => array(
                                'Department.id' => $this->department_ids,
                                'Department.active' => 1
                            )
                        ));

                        $default_department_id = array_keys($departments)[0];
                    } else {
                        if (!empty($this->college_ids)) {
                            $departments = array();

                            $colleges = $this->CourseSubstitutionRequest->Student->College->find('list', array(
                                'conditions' => array(
                                    'College.id' => $this->college_ids,
                                    'College.active' => 1
                                )
                            ));

                            $default_college_id = array_keys($colleges)[0];
                        }
                    }
                } else {
                    $departments = $this->CourseSubstitutionRequest->Student->Department->find('list', array(
                        'conditions' => array(
                            'Department.active' => 1
                        )
                    ));

                    $colleges = $this->CourseSubstitutionRequest->Student->College->find('list', array(
                        'conditions' => array(
                            'College.active' => 1
                        )
                    ));

                    $default_college_id = array_keys($colleges)[0];
                }
            }
        }

        $availableProgramsInCurriculums = ClassRegistry::init('Curriculum')->find('list', array(
            'fields' => array('program_id', 'program_id'),
            'conditions' => array(
                'Curriculum.department_id' => array_keys($departments),
                'Curriculum.program_id' => array_keys($programs)
            ),
            'group' => 'Curriculum.program_id'
        ));

        debug($availableProgramsInCurriculums);

        if (!empty($availableProgramsInCurriculums)) {
            $programs = ClassRegistry::init('Curriculum')->Program->find(
                'list',
                array('conditions' => array('Program.id' => $availableProgramsInCurriculums))
            );
        }

        debug($programs);

        $curriculums = array();
        $default_program_id = array_keys($programs)[0];

        if (!empty($default_program_id) && !empty($departments)) {
            if (isset($departments)) {
                $curriculums = $this->CourseSubstitutionRequest->Student->Curriculum->find('list', array(
                    'fields' => array('id', 'curriculum_detail'),
                    'conditions' => array(
                        'Curriculum.department_id' => array_keys($departments)[0],
                        'Curriculum.program_id' => $default_program_id
                    )
                ));
            }
        }

        if (!empty($this->request->data) /* && isset($this->request->data['viewSubstitution']) */) {
            $options = array();

            if ($this->Session->read('Auth.User')['role_id'] == ROLE_DEPARTMENT) {
                $options[] = array('Student.department_id' => $this->department_id);
            } else {
                if ($this->Session->read('Auth.User')['role_id'] == ROLE_COLLEGE) {
                    if (!empty($this->request->data['Search2']['department_id'])) {
                        $options[] = array('Student.department_id' => $this->request->data['Search2']['department_id']);
                    } else {
                        $options[] = array('Student.college_id' => $this->college_id);
                    }
                } else {
                    if ($this->Session->read('Auth.User')['role_id'] == ROLE_REGISTRAR) {
                        if (!empty($this->department_ids)) {
                            if (!empty($this->request->data['Search2']['department_id'])) {
                                $options[] = array('Student.department_id' => $this->request->data['Search2']['department_id']);
                            } else {
                                $options[] = array('Student.department_id' => array_keys($departments));
                            }
                        } else {
                            if (!empty($this->college_ids)) {
                                if (!empty($this->request->data['Search2']['college_id'])) {
                                    $options[] = array(
                                        'Student.college_id' => $this->request->data['Search2']['college_id'],
                                        'Student.department_id IS NULL'
                                    );
                                } else {
                                    $options[] = array(
                                        'Student.college_id' => array_keys($colleges),
                                        'Student.department_id IS NULL'
                                    );
                                }
                            }
                        }
                    } else {
                        if (!empty($this->request->data['Search2']['department_id'])) {
                            $options[] = array('Student.department_id' => $this->request->data['Search2']['department_id']);
                        } else {
                            if (empty($this->request->data['Search2']['department_id']) && !empty($this->request->data['Search2']['college_id'])) {
                                $options[] = array(
                                    'OR' => array(
                                        'Student.college_id' => $this->request->data['Search2']['college_id'],
                                        'Student.department_id' => array_keys($departments)
                                    )
                                );
                            } else {
                                $options[] = array(
                                    'OR' => array(
                                        'Student.college_id' => array_keys($colleges),
                                        'Student.department_id' => array_keys($departments)
                                    )
                                );
                            }
                        }
                    }
                }
            }


            if (!empty($this->request->data['Search2']['program_id'])) {
                if (isset($departments)) {
                    $curriculums = $this->CourseSubstitutionRequest->Student->Curriculum->find('list', array(
                        'fields' => array('id', 'curriculum_detail'),
                        'conditions' => array(
                            'Curriculum.department_id' => array_keys($departments),
                            'Curriculum.program_id' => $this->request->data['Search2']['program_id']
                        )
                    ));
                }
            }

            if (!empty($this->request->data['Search2']['curriculum_id'])) {
                $options[] = array('CourseForSubstitued.curriculum_id' => $this->request->data['Search2']['curriculum_id']);
            } else {
                if (isset($departments) && isset($programs)) {
                    $curriculumss = $this->CourseSubstitutionRequest->Student->Curriculum->find('list', array(
                        'fields' => array('id', 'curriculum_detail'),
                        'conditions' => array(
                            'Curriculum.department_id' => array_keys($departments),
                            'Curriculum.program_id' => array_keys($programs),
                        )
                    ));
                }

                if (!empty($options)) {
                } else {
                    $options[] = array('CourseForSubstitued.curriculum_id' => array_keys($curriculumss));
                }
            }


            if (isset($this->request->data['Search2']['status']) && $this->request->data['Search2']['status'] == 'notprocessed') {
                $options[] = array('CourseSubstitutionRequest.department_approve is null');
            } else {
                if (isset($this->request->data['Search2']['status']) && $this->request->data['Search2']['status'] == 'accepted') {
                    $options[] = array('CourseSubstitutionRequest.department_approve' => 1);
                } else {
                    if (isset($this->request->data['Search2']['status']) && $this->request->data['Search2']['status'] == 'rejected') {
                        $options[] = array('CourseSubstitutionRequest.department_approve' => 0);
                    }
                }
            }


            if (isset($name) && !empty($name)) {
                $options[] = array(
                    'OR' => array(
                        'Student.first_name LIKE ' => '%' . $name . '%',
                        'Student.middle_name LIKE ' => '%' . $name . '%',
                        'Student.last_name LIKE ' => '%' . $name . '%',
                    )
                );
            }

            if (isset($studentnumber) && !empty($studentnumber)) {
                $options[] = array('Student.studentnumber LILE ' => (trim($studentnumber)) . '%');
            }


            $this->Paginator->settings = array(
                'contain' => array(
                    'Student' => array(
                        'fields' => array('id', 'full_name', 'gender', 'studentnumber', 'graduated'),
                        'Department' => array('id', 'name'),
                    ),
                    'CourseForSubstitued' => array(
                        'Department' => array('id', 'name'),
                        'Curriculum' => array('id', 'name', 'year_introduced', 'type_credit'),
                    ),
                    'CourseBeSubstitued' => array(
                        'Department' => array('id', 'name'),
                        'Curriculum' => array('id', 'name', 'year_introduced', 'type_credit'),
                    )
                ),
                'limit' => $limit,
                'maxLimit' => $limit,
                'recursive' => -1
            );

            debug($options);

            $courseSubstitutionRequests = array();


            if (!empty($options)) {
                $courseSubstitutionRequests = $this->paginate($options);
            }

            if (empty($courseSubstitutionRequests)) {
                $this->Flash->info('There is no course substitution request in the given criteria.');
            } else {
                debug($courseSubstitutionRequests[0]);
                $turn_off_search = false;
            }
        } else {
            if ($this->role_id == ROLE_STUDENT) {
                $conditions = array('CourseSubstitutionRequest.student_id' => $this->student_id);
                $courseSubstitutionRequests = $this->paginate($conditions);
            }
        }

        $this->set(
            compact(
                'courseSubstitutionRequests',
                'curriculums',
                'programs',
                'departments',
                'colleges',
                'limit',
                'name',
                'studentnumber',
                'turn_off_search',
                'default_department_id',
                'default_college_id',
                'default_curriculum_id',
                'default_program_id'
            )
        );
    }
}
