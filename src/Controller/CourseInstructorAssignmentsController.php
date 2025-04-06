<?php

namespace App\Controller;

use Cake\Event\Event;
use Cake\ORM\TableRegistry;
use Cake\Core\Configure;

class CourseInstructorAssignmentsController extends AppController
{

    public $name = 'CourseInstructorAssignments';

    public $menuOptions = array(
        'parent' => 'curriculums',
        'exclude' => array(
            'add',
            'getDepartment',
            'assignInstructorUpdate',
            'resetDepartment',
            'assignInstructor',
            'getAssignedFxForInstructor',
            'getAssignedCoursesOfInstructorBySectionForCombo',
            'getAssignedGradeEntryForInstructor',
            'assign',
            'getInstructorCombo',
            'search'
        ),
        'alias' => array(
            'index' => 'View Instructors Assignment',
            //'assign' => 'Assign Instructors',
            'changeCourseDepartment' => 'Dispatch to other Department',
            'assignCourseInstructor' => 'Assign Instructor to Courses'
        )
    );

    public $paginate = array();
    public $components = array('AcademicYear');

    public function beforeFilter(Event $event)
    {

        parent::beforeFilter($event);
        $this->Auth->allow(
            'getAssignedCoursesOfInstructorBySectionForCombo',
            'getDepartment',
            'getAssignedFxForInstructor',
            'assignInstructor',
            'assignInstructorUpdate',
            'getCourseInstructorDetail',
            'getAssignedGradeEntryForInstructor',
            'resetDepartment',
            'getInstructorCombo',
            'search'
        );
    }

    public function beforeRender(Event $event)
    {

        parent::beforeRender($event);

        $defaultacademicyear = $this->AcademicYear->currentAcademicYear();

        if (is_numeric(ACY_BACK_FOR_ALL) && ACY_BACK_FOR_ALL) {
            $acyear_array_data = $this->AcademicYear->academicYearInArray(
                ((explode('/', $defaultacademicyear)[0]) - ACY_BACK_FOR_ALL),
                (explode('/', $defaultacademicyear)[0])
            );
        } else {
            $acyear_array_data[$defaultacademicyear] = $defaultacademicyear;
        }

        $this->set(compact('acyear_array_data', 'defaultacademicyear'));
        //////////////////////////////////// END BLOCK ///////////////////////////////////////////////////
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

    public function __init_clear_session_filters($data = null)
    {

        if ($this->Session->check('search_data_index')) {
            $this->Session->delete('search_data_index');
        }
        //return $this->redirect(array('action' => 'index', $data));
    }

    public function index()
    {

        $defaultacademicyear = $this->AcademicYear->current_academicyear();
        $conditions_text = array();

        $limit = '';
        $instructor_name = '';
        $course_name = '';
        $selected_academic_year = '';
        $page = '';
        //$sort = 'AcceptedStudent.created';
        //$direction = 'desc';

        if (!empty($this->passedArgs)) {
            debug($this->passedArgs);

            if (!empty($this->passedArgs['Search.limit'])) {
                $limit = $this->request->data['Search']['limit'] = $this->passedArgs['Search.limit'];
            }

            if (!empty($this->passedArgs['Search.course_name'])) {
                $name = $this->request->data['Search']['course_name'] = str_replace(
                    '-',
                    '/',
                    $this->passedArgs['Search.course_name']
                );
            }

            if (!empty($this->passedArgs['Search.instructor_name'])) {
                $name = $this->request->data['Search']['instructor_name'] = str_replace(
                    '-',
                    '/',
                    $this->passedArgs['Search.instructor_name']
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

        debug($this->request->data);

        if (isset($data) && !empty($data['Search'])) {
            $this->request->data['Search'] = $data['Search'];
            $this->__init_search_index();
        }


        if (isset($this->request->data['search'])) {
            unset($this->passedArgs);
            $this->__init_search_index();
            $this->__init_clear_session_filters($this->request->data);
        }

        if (empty($this->request->data['Search']['department_id']) && empty($this->request->data['Search']['instructor_name']) && empty($this->request->data['Search']['course_name']) && empty($this->request->data['Search']['academicyear']) && empty($this->request->data['Search']['semester'])) {
            unset($this->request->data['CourseInstructorAssignment']);
        }

        if (!empty($this->request->data)) {
            if (!empty($page) && !isset($this->request->data['search'])) {
                $this->request->data['Search']['page'] = $page;
            }
        }

        if ($this->role_id == ROLE_COLLEGE) {
            $departments = array();

            if ($this->onlyPre) {
                $departments[1000] = 'Pre/Freshman/Remedial';
            } else {
                if (!empty($this->department_ids)) {
                    $departments = array(1000 => 'Pre/Freshman/Remedial') + $this->CourseInstructorAssignment->PublishedCourse->Department->find(
                            'list',
                            array('conditions' => array('Department.id' => $this->department_ids))
                        );
                } else {
                    if (!empty($this->college_id)) {
                        $departments = array(1000 => 'Pre/Freshman/Remedial') + $this->CourseInstructorAssignment->PublishedCourse->Department->find(
                                'list',
                                array('conditions' => array('Department.college_id' => $this->college_id))
                            );
                    }
                }
            }

            if (empty($this->request->data['Search']['department_id'])) {
                $this->department_id = array_keys($departments)[0];
            } else {
                $this->department_id = $this->request->data['Search']['department_id'];
            }
        } else {
            if ($this->Session->read('Auth.User')['role_id'] == ROLE_DEPARTMENT) {
                $departments = $this->CourseInstructorAssignment->PublishedCourse->Department->find(
                    'list',
                    array('conditions' => array('Department.id' => $this->department_id))
                );
            } else {
                if ($this->Session->read('Auth.User')['role_id'] == ROLE_REGISTRAR || $this->Session->read(
                        'Auth.User'
                    )['role_id'] == ROLE_SYSADMIN) {
                    if ($this->onlyPre) {
                        $departments = array(1000 => 'Pre/Freshman/Remedial');
                    } else {
                        if (!empty($this->department_ids)) {
                            $departments = $this->CourseInstructorAssignment->PublishedCourse->Department->find(
                                'list',
                                array(
                                    'conditions' => array(
                                        'Department.id' => $this->department_ids,
                                        'Department.active' => 1
                                    )
                                )
                            );
                        }
                    }
                } else {
                    $departments = array(0 => 'NO PRIVILAGE');
                }
            }
        }

        if (!empty($this->request->data['Search']['instructor_name'])) {
            $section_id_array = $this->CourseInstructorAssignment->Staff->find('list', array(
                'conditions' => array(
                    'Staff.first_name LIKE' => trim(
                            $this->request->data['Search']['instructor_name']
                        ) . '%'
                ),
            ));

            $conditions_text['CourseInstructorAssignment.staff_id'] = $section_id_array;
        }


        if (!empty($this->request->data['Search']['course_name'])) {
            $course_id_array = $this->CourseInstructorAssignment->PublishedCourse->Course->find('list', array(
                'conditions' => array(
                    'Course.course_title LIKE' => '%' . trim(
                            $this->request->data['Search']['course_name']
                        ) . '%'
                )
            ));

            if ($this->role_id == ROLE_COLLEGE) {
                if ($this->department_id == 1000 || (empty($this->request->data['Search']['department_id'])) || $this->onlyPre) {
                    $published_course_id_array = $this->CourseInstructorAssignment->PublishedCourse->find('list', array(
                        'conditions' => array(
                            'PublishedCourse.college_id' => $this->college_id,
                            'PublishedCourse.course_id' => $course_id_array,
                            'PublishedCourse.drop' => 0
                        )
                    ));
                } else {
                    if (!empty($this->department_ids)) {
                        $published_course_id_array = $this->CourseInstructorAssignment->PublishedCourse->find(
                            'list',
                            array(
                                'conditions' => array(
                                    'OR' => array(
                                        'PublishedCourse.college_id' => $this->college_id,
                                        'PublishedCourse.department_id' => $this->department_ids,
                                        'PublishedCourse.given_by_department_id' => $this->department_ids,
                                    ),
                                    'PublishedCourse.course_id' => $course_id_array,
                                    'PublishedCourse.drop' => 0
                                )
                            )
                        );
                    }
                }
            } else {
                if ($this->role_id == ROLE_DEPARTMENT) {
                    $published_course_id_array = $this->CourseInstructorAssignment->PublishedCourse->find('list', array(
                        'conditions' => array(
                            'OR' => array(
                                'PublishedCourse.department_id' => $this->department_id,
                                'PublishedCourse.given_by_department_id' => $this->department_id,
                            ),
                            'PublishedCourse.course_id' => $course_id_array,
                            'PublishedCourse.drop' => 0
                        )
                    ));
                }
            }

            $conditions_text['CourseInstructorAssignment.published_course_id'] = $published_course_id_array;
        }

        if (!empty($this->request->data['Search']['academicyear'])) {
            $conditions_text['CourseInstructorAssignment.academic_year'] = $this->request->data['Search']['academicyear'];
        }

        if (!empty($this->request->data['Search']['semester'])) {
            $conditions_text['CourseInstructorAssignment.semester'] = $this->request->data['Search']['semester'];
        }

        if (!empty($conditions_text)) {
            if (!isset($conditions_text['CourseInstructorAssignment.published_course_id'])) {
                /* if ($this->department_id == 1000 || (empty($this->request->data['CourseInstructorAssignment']['department_id']))) {
                    $published_course_id_array = $this->CourseInstructorAssignment->PublishedCourse->find('list', array('conditions' => array("OR" => array('PublishedCourse.college_id' => $this->college_id, 'PublishedCourse.department_id' => $this->department_ids))));
                } else  */

                if ($this->department_id == 1000 || $this->onlyPre) {
                    if (!empty($this->college_ids)) {
                        $published_course_id_array = $this->CourseInstructorAssignment->PublishedCourse->find(
                            'list',
                            array(
                                'conditions' => array(
                                    'PublishedCourse.college_id' => $this->college_ids,
                                    'PublishedCourse.department_id IS NULL',
                                    'PublishedCourse.drop' => 0
                                )
                            )
                        );
                    } else {
                        if (isset($this->college_id) && $this->college_id) {
                            $published_course_id_array = $this->CourseInstructorAssignment->PublishedCourse->find(
                                'list',
                                array(
                                    'conditions' => array(
                                        'PublishedCourse.college_id' => $this->college_id,
                                        'PublishedCourse.department_id IS NULL',
                                        'PublishedCourse.drop' => 0
                                    )
                                )
                            );
                        }
                    }
                } else {
                    if ($this->department_id || !empty($this->request->data['CourseInstructorAssignment']['department_id'])) {
                        if ($this->request->data['Search']['department_id']) {
                            $published_course_id_array = $this->CourseInstructorAssignment->PublishedCourse->find(
                                'list',
                                array(
                                    'conditions' => array(
                                        "OR" => array(
                                            'PublishedCourse.department_id' => $this->request->data['Search']['department_id'],
                                            'PublishedCourse.given_by_department_id' => $this->request->data['Search']['department_id']
                                        ),
                                        'PublishedCourse.drop' => 0
                                    )
                                )
                            );
                        } else {
                            $published_course_id_array = $this->CourseInstructorAssignment->PublishedCourse->find(
                                'list',
                                array(
                                    'conditions' => array(
                                        "OR" => array(
                                            'PublishedCourse.department_id' => $this->department_id,
                                            'PublishedCourse.given_by_department_id' => $this->department_id
                                        ),
                                        'PublishedCourse.drop' => 0
                                    )
                                )
                            );
                        }
                    } else {
                        if (empty($this->request->data['Search']['department_id']) && !empty($this->department_ids) && $this->role_id != ROLE_DEPARTMENT) {
                            if ($this->role_id == ROLE_COLLEGE) {
                                $published_course_id_array = $this->CourseInstructorAssignment->PublishedCourse->find(
                                    'list',
                                    array(
                                        'conditions' => array(
                                            "OR" => array(
                                                'PublishedCourse.college_id' => $this->college_id,
                                                'PublishedCourse.department_id' => $this->department_ids,
                                                'PublishedCourse.given_by_department_id' => $this->department_ids
                                            ),
                                            'PublishedCourse.drop' => 0
                                        )
                                    )
                                );
                            } else {
                                $published_course_id_array = $this->CourseInstructorAssignment->PublishedCourse->find(
                                    'list',
                                    array(
                                        'conditions' => array(
                                            "OR" => array(
                                                'PublishedCourse.department_id' => $this->department_ids,
                                                'PublishedCourse.given_by_department_id' => $this->department_ids
                                            ),
                                            'PublishedCourse.drop' => 0
                                        )
                                    )
                                );
                            }
                        } else {
                            if ($this->role_id == ROLE_DEPARTMENT) {
                                $published_course_id_array = $this->CourseInstructorAssignment->PublishedCourse->find(
                                    'list',
                                    array(
                                        'conditions' => array(
                                            "OR" => array(
                                                'PublishedCourse.department_id' => $this->department_id,
                                                'PublishedCourse.given_by_department_id' => $this->department_id
                                            ),
                                            'PublishedCourse.drop' => 0
                                        )
                                    )
                                );
                            } else {
                                if (($this->role_id == ROLE_REGISTRAR || $this->role_id == ROLE_SYSADMIN) && !empty($this->department_ids)) {
                                    $published_course_id_array = $this->CourseInstructorAssignment->PublishedCourse->find(
                                        'list',
                                        array(
                                            'conditions' => array(
                                                "OR" => array(
                                                    'PublishedCourse.department_id' => $this->department_ids,
                                                    'PublishedCourse.given_by_department_id' => $this->department_ids
                                                ),
                                                'PublishedCourse.drop' => 0
                                            )
                                        )
                                    );
                                }
                            }
                        }
                    }
                }

                $conditions_text['CourseInstructorAssignment.published_course_id'] = $published_course_id_array;
            }

            $conditions = array($conditions_text);
        } else {
            /* if ($this->department_id == 1000) {
                $published_course_id_array = $this->CourseInstructorAssignment->PublishedCourse->find('list', array('conditions' => array("OR" => array('PublishedCourse.college_id' => $this->college_id, 'PublishedCourse.given_by_department_id' => $this->department_id, 'PublishedCourse.drop' => 0))));
            } */

            if ($this->department_id == 1000 || $this->onlyPre) {
                if (!empty($this->college_ids)) {
                    $published_course_id_array = $this->CourseInstructorAssignment->PublishedCourse->find(
                        'list',
                        array(
                            'conditions' => array(
                                'PublishedCourse.college_id' => $this->college_ids,
                                /* 'PublishedCourse.department_id IS NULL', */
                                'PublishedCourse.drop' => 0
                            )
                        )
                    );
                } else {
                    if (isset($this->college_id) && $this->college_id) {
                        $published_course_id_array = $this->CourseInstructorAssignment->PublishedCourse->find(
                            'list',
                            array(
                                'conditions' => array(
                                    'PublishedCourse.college_id' => $this->college_id,
                                    /* 'PublishedCourse.department_id IS NULL', */
                                    'PublishedCourse.drop' => 0
                                )
                            )
                        );
                    }
                }
            } else {
                if ($this->department_id || !empty($this->request->data['Search']['department_id'])) {
                    if (isset($this->request->data['Search']['department_id']) && $this->request->data['Search']['department_id']) {
                        $published_course_id_array = $this->CourseInstructorAssignment->PublishedCourse->find(
                            'list',
                            array(
                                'conditions' => array(
                                    "OR" => array(
                                        'PublishedCourse.department_id' => $this->request->data['Search']['department_id'],
                                        'PublishedCourse.given_by_department_id' => $this->request->data['Search']['department_id']
                                    ),
                                    'PublishedCourse.drop' => 0
                                )
                            )
                        );
                    } else {
                        $published_course_id_array = $this->CourseInstructorAssignment->PublishedCourse->find(
                            'list',
                            array(
                                'conditions' => array(
                                    "OR" => array(
                                        'PublishedCourse.department_id' => $this->department_id,
                                        'PublishedCourse.given_by_department_id' => $this->department_id
                                    ),
                                    'PublishedCourse.drop' => 0
                                )
                            )
                        );
                    }
                } else {
                    if ($this->role_id == ROLE_COLLEGE && !empty($this->department_ids) && !$this->onlyPre) {
                        $published_course_id_array = $this->CourseInstructorAssignment->PublishedCourse->find(
                            'list',
                            array(
                                'conditions' => array(
                                    "OR" => array(
                                        'PublishedCourse.college_id' => $this->college_id,
                                        'PublishedCourse.department_id' => $this->department_ids,
                                        'PublishedCourse.given_by_department_id' => $this->department_ids
                                    ),
                                    'PublishedCourse.drop' => 0
                                )
                            )
                        );
                    } else {
                        if ($this->role_id == ROLE_DEPARTMENT) {
                            $published_course_id_array = $this->CourseInstructorAssignment->PublishedCourse->find(
                                'list',
                                array(
                                    'conditions' => array(
                                        "OR" => array(
                                            'PublishedCourse.department_id' => $this->department_id,
                                            'PublishedCourse.given_by_department_id' => $this->department_id
                                        ),
                                        'PublishedCourse.drop' => 0
                                    )
                                )
                            );
                        } else {
                            if (($this->role_id == ROLE_REGISTRAR || $this->role_id == ROLE_SYSADMIN) && !empty($this->department_ids)) {
                                $published_course_id_array = $this->CourseInstructorAssignment->PublishedCourse->find(
                                    'list',
                                    array(
                                        'conditions' => array(
                                            "OR" => array(
                                                'PublishedCourse.department_id' => $this->department_ids,
                                                'PublishedCourse.given_by_department_id' => $this->department_ids
                                            ),
                                            'PublishedCourse.drop' => 0
                                        )
                                    )
                                );
                            }
                        }
                    }
                }
            }

            $conditions = array('CourseInstructorAssignment.published_course_id' => $published_course_id_array);
        }

        $courseInstructorAssignments = array();

        if (!empty($conditions)) {
            //debug($conditions);
            $limit_query = array();

            if (!empty($limit) && $limit) {
                $this->Paginator->settings = array(
                    'conditions' => $conditions,
                    'contain' => array(
                        'Section' => array('fields' => array('Section.name')),
                        'CourseSplitSection',
                        'Staff' => array(
                            'fields' => array('Staff.full_name', 'Staff.user_id'),
                            'conditions' => array('Staff.active' => 1),
                            'Title' => array('fields' => array('Title.title')),
                            'Position' => array('fields' => array('Position.position')),
                            'Department' => array('fields' => array('Department.name'))
                        ),
                        'PublishedCourse' => array(
                            'fields' => array('PublishedCourse.id'),
                            'Course' => array(
                                'fields' => array(
                                    'Course.course_code_title',
                                    'Course.credit',
                                    'Course.course_detail_hours'
                                )
                            )
                        )
                    ),
                    'order' => array(
                        'CourseInstructorAssignment.academic_year' => 'DESC',
                        'CourseInstructorAssignment.semester' => 'DESC',
                        'CourseInstructorAssignment.section_id' => 'ASC',
                        'CourseInstructorAssignment.published_course_id' => 'ASC',
                        //'CourseInstructorAssignment.staff_id' => 'ASC',
                        'CourseInstructorAssignment.created' => 'DESC',
                    ),
                    'limit' => $limit,
                    'maxLimit' => $limit,
                );
            } else {
                $this->Paginator->settings = array(
                    'conditions' => $conditions,
                    'contain' => array(
                        'Section' => array('fields' => array('Section.name')),
                        'CourseSplitSection',
                        'Staff' => array(
                            'fields' => array('Staff.full_name', 'Staff.user_id'),
                            'conditions' => array('Staff.active' => 1),
                            'Title' => array('fields' => array('Title.title')),
                            'Position' => array('fields' => array('Position.position')),
                            'Department' => array('fields' => array('Department.name'))
                        ),
                        'PublishedCourse' => array(
                            'fields' => array('PublishedCourse.id'),
                            'Course' => array(
                                'fields' => array(
                                    'Course.course_code_title',
                                    'Course.credit',
                                    'Course.course_detail_hours'
                                )
                            )
                        )
                    ),
                    'order' => array(
                        'CourseInstructorAssignment.academic_year' => 'DESC',
                        'CourseInstructorAssignment.semester' => 'DESC',
                        'CourseInstructorAssignment.section_id' => 'ASC',
                        'CourseInstructorAssignment.published_course_id' => 'ASC',
                        //'CourseInstructorAssignment.staff_id' => 'ASC',
                        'CourseInstructorAssignment.created' => 'DESC',
                    )
                );
            }

            try {
                $courseInstructorAssignments = $this->Paginator->paginate($this->modelClass);
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


            if (empty($courseInstructorAssignments) && !empty($conditions)) {
                $this->Flash->info('No Course Instructor Assignamet is found with the given search criteria.');
                $turn_off_search = false;
            } else {
                $turn_off_search = false;
            }
        }

        $this->__init_search_index();

        $this->set('courseInstructorAssignments', $courseInstructorAssignments);
        $this->set(compact('departments', 'turn_off_search', 'limit'));
    }

    public function view($id = null)
    {

        if (!$id) {
            $this->Session->setFlash(__('Invalid course instructor assignment'));
            return $this->redirect(array('action' => 'index'));
        }
        $courseInstructorAssignment = $this->CourseInstructorAssignment->find(
            'first',
            array(
                'conditions' => array('CourseInstructorAssignment.id' => $id),
                'contain' => array(
                    'Section' => array('fields' => array('Section.id', 'Section.name')),
                    'CourseSplitSection',
                    'Staff' => array('fields' => array('Staff.id', 'Staff.full_name')),
                    'PublishedCourse' => array(
                        'fields' => array('PublishedCourse.id'),
                        'Course' => array('fields' => array('Course.id', 'Course.course_code_title'))
                    )
                )
            )
        );
        $this->set(compact('courseInstructorAssignment'));
        //$this->set('courseInstructorAssignment', $this->CourseInstructorAssignment->read(null, $id));

    }

    public function add()
    {

        if (!empty($this->request->data)) {
            $this->CourseInstructorAssignment->create();
            if ($this->CourseInstructorAssignment->save($this->request->data)) {
                $this->Session->setFlash(__('The course instructor assignment has been saved'));
                return $this->redirect(array('action' => 'index'));
            } else {
                $this->Session->setFlash(__('The course instructor assignment could not be saved. Please, try again.'));
            }
        }

        $publishedCourses = $this->CourseInstructorAssignment->PublishedCourse->find(
            'all',
            array('contain' => array('Course'))
        );

        $sections = $this->CourseInstructorAssignment->Section->find('list');
        $staffs = $this->CourseInstructorAssignment->Staff->find('list', array(
            'fields' => 'full_name',
            'conditions' => array('Staff.college_id' => $this->college_id)
        ));
        //$courses = $this->CourseInstructorAssignment->Course->find('list');
        $this->set(compact('sections', 'staffs', 'publishedCourses'));
    }

    public function edit($id = null)
    {

        if (!$id && empty($this->request->data)) {
            $this->Session->setFlash(__('Invalid course instructor assignment'));
            return $this->redirect(array('action' => 'index'));
        }
        if (!empty($this->request->data)) {
            if ($this->CourseInstructorAssignment->save($this->request->data)) {
                $this->Session->setFlash(__('The course instructor assignment has been saved'));
                return $this->redirect(array('action' => 'index'));
            } else {
                $this->Session->setFlash(__('The course instructor assignment could not be saved. Please, try again.'));
            }
        }
        if (empty($this->request->data)) {
            $this->request->data = $this->CourseInstructorAssignment->read(null, $id);
        }
        $sections = $this->CourseInstructorAssignment->Section->find('list');
        $staffs = $this->CourseInstructorAssignment->Staff->find('list');
        $courses = $this->CourseInstructorAssignment->Course->find('list');
        $this->set(compact('sections', 'staffs', 'courses'));
    }

    public function delete($id = null, $published_course_id = null)
    {

        if (!empty($published_course_id)) {
            $course_instructor_assignment_data = $this->CourseInstructorAssignment->PublishedCourse->find(
                'first',
                array(
                    'fields' => array(
                        'PublishedCourse.academic_year',
                        'PublishedCourse.semester',
                        'PublishedCourse.program_id',
                        'PublishedCourse.program_type_id',
                        'PublishedCourse.year_level_id',
                    ),
                    'conditions' => array('PublishedCourse.id' => $published_course_id, 'PublishedCourse.drop' => 0),
                    'contain' => array()
                )
            );

            $this->Session->write(
                'selected_academicyear',
                $course_instructor_assignment_data['PublishedCourse']['academic_year']
            );
            $this->Session->write(
                'selected_program_id',
                $course_instructor_assignment_data['PublishedCourse']['program_id']
            );
            $this->Session->write(
                'selected_program_type_id',
                $course_instructor_assignment_data['PublishedCourse']['program_type_id']
            );
            $this->Session->write(
                'selected_semester',
                $course_instructor_assignment_data['PublishedCourse']['semester']
            );

            if (ROLE_COLLEGE != $this->role_id) {
                $this->Session->write(
                    'selected_year_level_id',
                    $course_instructor_assignment_data['PublishedCourse']['year_level_id']
                );
            }
        }

        if (!$id) {
            $this->Flash->error(__('Invalid id for course instructor assignment.'));
            if (!empty($published_course_id)) {
                return $this->redirect(array('action' => 'assign_course_instructor', $published_course_id));
            } else {
                return $this->redirect(array('action' => 'index'));
            }
        }

        $id_exists = $this->CourseInstructorAssignment->find(
            'count',
            array('conditions' => array('CourseInstructorAssignment.id' => $id))
        );

        if ($id_exists == 0) {
            $this->Flash->error(__('Invalid ID for course instructor assignment.', true));
            // $this->redirect(array('action' => 'assign',$published_course_id));
            $this->redirect(array('action' => 'assign_course_instructor', $published_course_id));
        }

        $is_delete_allowed = false;

        $deletion_allowed = $this->CourseInstructorAssignment->find(
            'first',
            array('conditions' => array('CourseInstructorAssignment.id' => $id), 'recursive' => -1)
        );

        if (!empty($deletion_allowed)) {
            $published_course_ID = $deletion_allowed['CourseInstructorAssignment']['published_course_id'];

            $get_list_registred_courses = $this->CourseInstructorAssignment->PublishedCourse->CourseRegistration->find(
                'all',
                array(
                    'conditions' => array('CourseRegistration.published_course_id' => $published_course_ID),
                    'recursive' => -1
                )
            );

            if (!empty($get_list_registred_courses)) {
                $list_registred_courses = array();

                foreach ($get_list_registred_courses as $grk => $grv) {
                    $list_registred_courses[] = $grv['CourseRegistration']['id'];
                }

                $count_allowed = $this->CourseInstructorAssignment->PublishedCourse->CourseRegistration->ExamGrade->find(
                    'count',
                    array('conditions' => array('ExamGrade.course_registration_id' => $list_registred_courses))
                );

                if ($count_allowed > 0) {
                    $is_delete_allowed = false;
                } else {
                    $is_delete_allowed = true;
                }
            } else {
                $is_delete_allowed = true;
            }
        }


        $instructor_name = '';
        $course_title = '';
        $is_primary_instructor = '';

        if ($is_delete_allowed) {
            $pub_cours_id = $this->CourseInstructorAssignment->find('first', array(
                'conditions' => array('CourseInstructorAssignment.id' => $id),
                'contain' => array(
                    'PublishedCourse' => array(
                        'Course' => array('id', 'course_code_title'),
                        'Section' => array('id', 'name')
                    ),
                    'Staff' => array('id', 'full_name')
                ),
                'recursive' => -1
            ));

            //debug($pub_cours_id);

            $instructor_name = (!empty($pub_cours_id['Staff']['full_name']) ? $pub_cours_id['Staff']['full_name'] : '');
            $course_title = (!empty($pub_cours_id['PublishedCourse']['Course']['course_code_title']) ? $pub_cours_id['PublishedCourse']['Course']['course_code_title'] : '');
            $is_primary_instructor = (isset($pub_cours_id['CourseInstructorAssignment']['isprimary']) && $pub_cours_id['CourseInstructorAssignment']['isprimary'] == 1 ? true : false);
            $assigned_section_name = (!empty($pub_cours_id['PublishedCourse']['Section']['name']) ? $pub_cours_id['PublishedCourse']['Section']['name'] : '');

            if (ROLE_COLLEGE == $this->role_id) {
                $isBelongsUrDepartment = $this->CourseInstructorAssignment->PublishedCourse->find('count', array(
                    'conditions' => array(
                        'PublishedCourse.college_id' => $this->college_id,
                        'PublishedCourse.year_level_id' => 0,
                        'PublishedCourse.id' => $pub_cours_id['CourseInstructorAssignment']['published_course_id']
                    )
                ));
            } else {
                $isBelongsUrDepartment = $this->CourseInstructorAssignment->PublishedCourse->find('count', array(
                    'conditions' => array(
                        'PublishedCourse.given_by_department_id' => $this->department_id,
                        'PublishedCourse.id' => $pub_cours_id['CourseInstructorAssignment']['published_course_id']
                    )
                ));
            }

            if ($isBelongsUrDepartment > 0) {
                if ($this->CourseInstructorAssignment->delete($id)) {
                    $this->Flash->info(
                        __(
                            (!empty($instructor_name) ? $instructor_name . ' is now removed from ' . $course_title . ' course assignment as ' . ($is_primary_instructor ? 'primary instructor' : 'secondary instructor') . ' for ' . $assigned_section_name . ' section.' : 'Course instructor assignment is deleted.')
                        )
                    );
                    if (!empty($published_course_id)) {
                        $this->redirect(array('action' => 'assign_course_instructor', $published_course_id));
                    } else {
                        $this->redirect(array('action' => 'index'));
                    }
                }
            } else {
                $this->Flash->error(
                    __(
                        (!empty($instructor_name) ? 'You are not elegible to delete ' . $instructor_name . '\'s  ' . $course_title . ' course assignment as ' . ($is_primary_instructor ? 'primary instructor' : 'secondary instructor') . ' for ' . $assigned_section_name . ' section.' : 'You are not elegible to delete this instructor assignment.')
                    )
                );
                $this->redirect(array('action' => 'assign_course_instructor', $published_course_id));
            }
        }

        $this->Flash->error(
            __(
                (!empty($instructor_name) ? 'Course instructor assignment is not deleted. ' . (!empty($instructor_name) ? $instructor_name . ' has already submitted grade for  ' . $course_title . ' course for ' . $assigned_section_name . ' section.' : ' The instructor has already submitted grade.') : 'Course instructor assignment was not deleted. The instructor has already submitted grade.')
            )
        );

        if (!empty($published_course_id)) {
            return $this->redirect(array('action' => 'assign_course_instructor', $published_course_id));
        } else {
            return $this->redirect(array('action' => 'index'));
        }
    }

    //old for instructor assignment
    public function assign($for_assign_instructor = null)
    {

        if (!empty($for_assign_instructor)) {
            debug($for_assign_instructor);

            $this->request->data['CourseInstructorAssignment']['academicyear'] = $this->Session->read(
                'selected_academicyear'
            );
            $this->request->data['CourseInstructorAssignment']['program_id'] = $this->Session->read(
                'selected_program_id'
            );
            $this->request->data['CourseInstructorAssignment']['program_type_id'] = $this->Session->read(
                'selected_program_type_id'
            );

            if (ROLE_COLLEGE != $this->role_id) {
                $this->request->data['CourseInstructorAssignment']['year_level_id'] = $this->Session->read(
                    'selected_year_level_id'
                );
            }

            $this->request->data['CourseInstructorAssignment']['semester'] = $this->Session->read('selected_semester');
            $this->request->data['search'] = true;
        }

        $programs = $this->CourseInstructorAssignment->Section->Program->find('list');
        $programTypes = $this->CourseInstructorAssignment->Section->ProgramType->find('list');
        $yearLevels = $this->CourseInstructorAssignment->Section->YearLevel->find(
            'list',
            array('conditions' => array('YearLevel.department_id' => $this->department_id))
        );
        $isbeforesearch = 1;

        $this->set(compact('programs', 'programTypes', 'isbeforesearch', 'yearLevels'));

        if (!empty($this->request->data) && isset($this->request->data['search'])) {
            $everythingfine = false;

            switch ($this->request->data) {
                case empty($this->request->data['CourseInstructorAssignment']['academicyear']):
                    $this->Flash->error(
                        __('Please select the academic year you want to assign instructor for published courses.')
                    );
                    break;
                case empty($this->request->data['CourseInstructorAssignment']['semester']):
                    $this->Flash->error(
                        __('Please select the semester you want to assign instructor for published courses. ')
                    );
                    break;
                case empty($this->request->data['CourseInstructorAssignment']['program_id']):
                    $this->Flash->error(
                        __('Please select the program you want to assign instructor for published courses. ')
                    );
                    break;
                case empty($this->request->data['CourseInstructorAssignment']['program_type_id']):
                    $this->Flash->error(
                        __('Please select the program type you want to assign instructor for published courses. ')
                    );
                    break;
                default:
                    $everythingfine = true;
            }

            if ($this->role_id != ROLE_COLLEGE) {
                if (empty($this->request->data['CourseInstructorAssignment']['year_level_id'])) {
                    $this->Flash->error(
                        __('Please select the year level you want to assign instructor for published courses.')
                    );
                    $everythingfine = false;
                } else {
                    $everythingfine = true;
                }
            }

            if ($everythingfine) {
                $selected_academic_year = $this->request->data['CourseInstructorAssignment']['academicyear'];
                $selected_program = $this->request->data['CourseInstructorAssignment']['program_id'];
                $selected_program_type = $this->request->data['CourseInstructorAssignment']['program_type_id'];
                $selected_semester = $this->request->data['CourseInstructorAssignment']['semester'];
                $program_type_id = $selected_program_type;

                $find_the_equvilaent_program_type = unserialize(
                    $this->CourseInstructorAssignment->Section->ProgramType->field(
                        'ProgramType.equivalent_to_id',
                        array('ProgramType.id' => $selected_program_type)
                    )
                );

                if (!empty($find_the_equvilaent_program_type)) {
                    $selected_program_type_array = array();
                    $selected_program_type_array[] = $selected_program_type;
                    $program_type_id = array_merge($selected_program_type_array, $find_the_equvilaent_program_type);
                }

                if ($this->role_id != ROLE_COLLEGE) {
                    $selected_year_level = $this->request->data['CourseInstructorAssignment']['year_level_id'];
                    $conditions = array(
                        'PublishedCourse.academic_year' => $selected_academic_year,
                        'PublishedCourse.department_id' => $this->department_id,
                        'PublishedCourse.program_id' => $selected_program,
                        'PublishedCourse.program_type_id' => $program_type_id,
                        'PublishedCourse.year_level_id' => $selected_year_level,
                        'PublishedCourse.semester' => $selected_semester,
                        'PublishedCourse.drop' => 0
                    );
                } else {
                    $conditions = array(
                        'PublishedCourse.academic_year' => $selected_academic_year,
                        'PublishedCourse.college_id' => $this->college_id,
                        'PublishedCourse.program_id' => $selected_program,
                        'PublishedCourse.program_type_id' => $program_type_id,
                        'PublishedCourse.semester' => $selected_semester,
                        'PublishedCourse.drop' => 0,
                        "OR" => array(
                            "PublishedCourse.department_id is null",
                            "PublishedCourse.department_id" => array(0, '')
                        )
                    );
                }

                $publishedcourses = $this->CourseInstructorAssignment->PublishedCourse->find('all', array(
                    'conditions' => $conditions,
                    'fields' => array('PublishedCourse.section_id'),
                    'contain' => array(
                        'Section' => array(
                            'fields' => array('Section.id', 'Section.name')
                        ),
                        'Course' => array(
                            'fields' => array(
                                'Course.id',
                                'Course.course_title',
                                'Course.course_code',
                                'Course.credit',
                                'Course.lecture_hours',
                                'Course.tutorial_hours',
                                'Course.laboratory_hours'
                            )
                        ),
                        'SectionSplitForPublishedCourse' => array('CourseSplitSection'),
                        'CourseInstructorAssignment' => array(
                            'fields' => array(
                                'CourseInstructorAssignment.id',
                                'CourseInstructorAssignment.staff_id',
                                'CourseInstructorAssignment.type',
                                'CourseInstructorAssignment.isprimary',
                                'CourseInstructorAssignment.course_split_section_id'
                            ),
                            'Staff' => array(
                                'fields' => array('Staff.full_name'),
                                'conditions' => array('Staff.active' => 1),
                                'Title' => array('fields' => array('title')),
                                'Position' => array('fields' => array('position'))
                            )
                        )
                    )
                ));

                $sections_array = array();
                $course_type_array = array();

                if (!empty($publishedcourses)) {
                    foreach ($publishedcourses as $key => $publishedcourse) {
                        if (!empty($publishedcourse['SectionSplitForPublishedCourse'])) {
                            foreach ($publishedcourse['SectionSplitForPublishedCourse'][0]['CourseSplitSection'] as $split_section_for_course) {
                                $sections_array[$publishedcourse['Section']['name']][$split_section_for_course['section_name']]['course_title'] = $publishedcourse['Course']['course_title'];
                                $sections_array[$publishedcourse['Section']['name']][$split_section_for_course['section_name']]['course_id'] = $publishedcourse['Course']['id'];
                                $sections_array[$publishedcourse['Section']['name']][$split_section_for_course['section_name']]['course_code'] = $publishedcourse['Course']['course_code'];
                                $sections_array[$publishedcourse['Section']['name']][$split_section_for_course['section_name']]['credit'] = $publishedcourse['Course']['credit'];
                                $sections_array[$publishedcourse['Section']['name']][$split_section_for_course['section_name']]['credit_detail'] = $publishedcourse['Course']['lecture_hours'] . ' ' . $publishedcourse['Course']['tutorial_hours'] . ' ' . $publishedcourse['Course']['laboratory_hours'];
                                $sections_array[$publishedcourse['Section']['name']][$split_section_for_course['section_name']]['course_split_section_id'] = $split_section_for_course['id'];
                                $sections_array[$publishedcourse['Section']['name']][$split_section_for_course['section_name']]['section_id'] = $publishedcourse['PublishedCourse']['section_id'];
                                $sections_array[$publishedcourse['Section']['name']][$split_section_for_course['section_name']]['published_course_id'] = $publishedcourse['PublishedCourse']['id'];
                                $sections_array[$publishedcourse['Section']['name']][$split_section_for_course['section_name']]['grade_submitted'] = $this->CourseInstructorAssignment->PublishedCourse->CourseRegistration->ExamGrade->is_grade_submitted(
                                    $publishedcourse['PublishedCourse']['id']
                                );

                                if (!empty($publishedcourse['CourseInstructorAssignment'])) {
                                    foreach ($publishedcourse['CourseInstructorAssignment'] as $askey => $assign_instructor) {
                                        if ($split_section_for_course['id'] == $assign_instructor['course_split_section_id']) {
                                            $sections_array[$publishedcourse['Section']['name']][$split_section_for_course['section_name']]['assign_instructor'][$assign_instructor['isprimary']][$askey]['full_name'] = $assign_instructor['Staff']['Title']['title'] . '. ' . $assign_instructor['Staff']['full_name'];
                                            $sections_array[$publishedcourse['Section']['name']][$split_section_for_course['section_name']]['assign_instructor'][$assign_instructor['isprimary']][$askey]['position'] = $assign_instructor['Staff']['Position']['position'];
                                            $sections_array[$publishedcourse['Section']['name']][$split_section_for_course['section_name']]['assign_instructor'][$assign_instructor['isprimary']][$askey]['course_type'] = $assign_instructor['type'];
                                            $sections_array[$publishedcourse['Section']['name']][$split_section_for_course['section_name']]['assign_instructor'][$assign_instructor['isprimary']][$askey]['CourseInstructorAssignment_id'] = $assign_instructor['id'];
                                        }
                                    }
                                }

                                //$course_type_array[$split_section_for_course['section_name']][-1] = "---Select---";
                                if ($publishedcourse['Course']['lecture_hours'] > 0) {
                                    $course_type_array[$split_section_for_course['section_name']]["Lecture"] = "Lecture";
                                    if ($publishedcourse['Course']['tutorial_hours'] > 0 && $publishedcourse['Course']['laboratory_hours'] > 0) {
                                        $course_type_array[$split_section_for_course['section_name']]["Lecture+Tutorial+Lab"] = "Lect.+Tut.+Lab";
                                    }
                                }

                                if ($publishedcourse['Course']['tutorial_hours'] > 0) {
                                    $course_type_array[$split_section_for_course['section_name']]["tutorial"] = "Tutorial";
                                    if ($publishedcourse['Course']['lecture_hours'] > 0) {
                                        $course_type_array[$split_section_for_course['section_name']]["Lecture+Tutorial"] = "Lect.+Tut.";
                                    }
                                } else {
                                    if ($publishedcourse['Course']['laboratory_hours'] > 0) {
                                        $course_type_array[$split_section_for_course['section_name']]["Lab"] = "Lab";
                                        if ($publishedcourse['Course']['lecture_hours'] > 0) {
                                            $course_type_array[$split_section_for_course['section_name']]["Lecture+Lab"] = "Lec.+Lab";
                                        }
                                    }
                                }
                            }
                        } else {
                            $sections_array[$publishedcourse['Section']['name']][$key]['course_title'] = $publishedcourse['Course']['course_title'];
                            $sections_array[$publishedcourse['Section']['name']][$key]['course_id'] = $publishedcourse['Course']['id'];
                            $sections_array[$publishedcourse['Section']['name']][$key]['course_code'] = $publishedcourse['Course']['course_code'];
                            $sections_array[$publishedcourse['Section']['name']][$key]['credit'] = $publishedcourse['Course']['credit'];
                            $sections_array[$publishedcourse['Section']['name']][$key]['credit_detail'] = $publishedcourse['Course']['lecture_hours'] . ' ' . $publishedcourse['Course']['tutorial_hours'] . ' ' . $publishedcourse['Course']['laboratory_hours'];
                            $sections_array[$publishedcourse['Section']['name']][$key]['section_id'] = $publishedcourse['PublishedCourse']['section_id'];
                            $sections_array[$publishedcourse['Section']['name']][$key]['published_course_id'] = $publishedcourse['PublishedCourse']['id'];
                            $sections_array[$publishedcourse['Section']['name']][$key]['grade_submitted'] = $this->CourseInstructorAssignment->PublishedCourse->CourseRegistration->ExamGrade->is_grade_submitted(
                                $publishedcourse['PublishedCourse']['id']
                            );

                            if (!empty($publishedcourse['CourseInstructorAssignment'])) {
                                foreach ($publishedcourse['CourseInstructorAssignment'] as $askey => $assign_instructor) {
                                    $sections_array[$publishedcourse['Section']['name']][$key]['assign_instructor'][$assign_instructor['isprimary']][$askey]['full_name'] = $assign_instructor['Staff']['Title']['title'] . ' ' . $assign_instructor['Staff']['full_name'];
                                    $sections_array[$publishedcourse['Section']['name']][$key]['assign_instructor'][$assign_instructor['isprimary']][$askey]['position'] = $assign_instructor['Staff']['Position']['position'];
                                    $sections_array[$publishedcourse['Section']['name']][$key]['assign_instructor'][$assign_instructor['isprimary']][$askey]['course_type'] = $assign_instructor['type'];
                                    $sections_array[$publishedcourse['Section']['name']][$key]['assign_instructor'][$assign_instructor['isprimary']][$askey]['CourseInstructorAssignment_id'] = $assign_instructor['id'];
                                }
                            }

                            //$course_type_array[$key][-1] = "---Select---";
                            if ($publishedcourse['Course']['lecture_hours'] > 0) {
                                $course_type_array[$key]["Lecture"] = "Lecture";
                                if ($publishedcourse['Course']['tutorial_hours'] > 0 && $publishedcourse['Course']['laboratory_hours'] > 0) {
                                    $course_type_array[$key]["Lecture+Tutorial+Lab"] = "Lect.+Tut.+Lab";
                                }
                            }

                            if ($publishedcourse['Course']['tutorial_hours'] > 0) {
                                $course_type_array[$key]["tutorial"] = "Tutorial";
                                if ($publishedcourse['Course']['lecture_hours'] > 0) {
                                    $course_type_array[$key]["Lecture+Tutorial"] = "Lect.+Tut.";
                                }
                            }

                            if ($publishedcourse['Course']['laboratory_hours'] > 0) {
                                $course_type_array[$key]["Lab"] = "Lab";
                                if ($publishedcourse['Course']['lecture_hours'] > 0) {
                                    $course_type_array[$key]["Lecture+Lab"] = "Lect.+Lab";
                                }
                            }
                        }
                    }
                }

                if (empty($sections_array)) {
                    $this->Flash->info(
                        __('There is no published courses to assign instructor in the selected criteria.')
                    );
                } else {
                    if ($this->role_id == ROLE_COLLEGE) {
                        $thiscollege = $this->college_id;
                    } else {
                        $thiscollege = $this->CourseInstructorAssignment->PublishedCourse->Department->field(
                            'Department.college_id',
                            array('Department.id' => $this->department_id)
                        );
                        $thisdepartment = $this->department_id;
                        $this->set(compact('thisdepartment'));
                    }

                    $colleges = $this->CourseInstructorAssignment->PublishedCourse->College->find(
                        'list',
                        array('fields' => 'College.shortname')
                    );
                    $departments = $this->CourseInstructorAssignment->PublishedCourse->Department->find(
                        'list',
                        array(
                            'fields' => 'Department.shortname',
                            'conditions' => array('Department.college_id' => $thiscollege)
                        )
                    );
                    //unset($this->request->data);
                    $this->set(
                        compact('sections_array', 'colleges', 'departments', 'course_type_array', 'thiscollege')
                    );
                }
            }
        }
    }

    public function getDepartment($college_id = null)
    {

        $this->layout = 'ajax';
        $departments = null;
        $departments = $this->CourseInstructorAssignment->Section->Department->find(
            'list',
            array('fields' => 'Department.shortname', 'conditions' => array('Department.college_id' => $college_id))
        );
        $this->set(compact('departments'));
    }

    public function assignInstructor($data = null)
    {

        $this->layout = 'ajax';

        $explode_data = explode("~", $data);
        $department_id = $explode_data[0];

        if ($department_id == "pre") {
            $departments = $this->CourseInstructorAssignment->Section->Department->find(
                'list',
                array('fields' => 'Department.shortname')
            );
            foreach ($departments as $dep_id => $dep_name) {
                $department_id = $dep_id;
                break;
            }
        }

        $selected_course_type = $explode_data[1];

        if (strcmp($selected_course_type, "Lecture Tutorial") == 0) {
            $selected_course_type = "Lecture+Tutorial";
        }

        if (strcmp($selected_course_type, "Lecture Lab") == 0) {
            $selected_course_type = "Lecture+Lab";
        }

        $selected_published_course_id = $explode_data[2];
        $isprimary = $explode_data[3];
        $selected_course_split_section_id = $explode_data[4];

        if ($selected_course_split_section_id == 0) {
            $selected_course_split_section_id = null;
        }

        $this->CourseInstructorAssignment->PublishedCourse->recursive = -1;

        $published_course_detail = $this->CourseInstructorAssignment->PublishedCourse->find('first', array(
            'conditions' => array(
                'PublishedCourse.id' => $selected_published_course_id,
                'PublishedCourse.drop' => 0
            ),
            'fields' => array(
                'PublishedCourse.academic_year',
                'PublishedCourse.program_id',
                'PublishedCourse.program_type_id',
                'PublishedCourse.year_level_id',
                'PublishedCourse.section_id',
                'PublishedCourse.course_id',
                'PublishedCourse.semester'
            )
        ));

        $selected_academicyear = $published_course_detail['PublishedCourse']['academic_year'];
        $selected_program_id = $published_course_detail['PublishedCourse']['program_id'];
        $selected_program_type_id = $published_course_detail['PublishedCourse']['program_type_id'];
        $selected_year_level_id = $published_course_detail['PublishedCourse']['year_level_id'];
        $selected_semester = $published_course_detail['PublishedCourse']['semester'];
        $selected_department_id = $department_id;
        $selected_section_id = $published_course_detail['PublishedCourse']['section_id'];
        $course_id = $published_course_detail['PublishedCourse']['course_id'];


        $check_for_already_assigned_staff_list = $this->CourseInstructorAssignment->find('list', array(
            'conditions' => array(
                'CourseInstructorAssignment.published_course_id' => $selected_published_course_id,
                'CourseInstructorAssignment.section_id' => $published_course_detail['PublishedCourse']['section_id'],
            ),
            'fields' => array('CourseInstructorAssignment.staff_id')
        ));


        $selected_course_title = $this->CourseInstructorAssignment->PublishedCourse->Course->field(
            'Course.course_title',
            array('Course.id' => $course_id)
        );
        $course_code_title = $this->CourseInstructorAssignment->PublishedCourse->Course->field(
            'Course.course_code_title',
            array('Course.id' => $course_id)
        );

        if (!empty($department_id) && is_numeric($department_id)) {
            $equivalent_courses = array_values(
                ClassRegistry::init('EquivalentCourse')->find('list', array(
                    'conditions' => array('EquivalentCourse.course_for_substitued_id' => $course_id),
                    'fields' => array('course_be_substitued_id')
                ))
            );

            $equivalent_courses[] = $course_id;

            $instructors_detail = $this->CourseInstructorAssignment->Staff->find('all', array(
                'conditions' => array(
                    'Staff.department_id' => $selected_department_id,
                    'NOT' => array('Staff.id' => $check_for_already_assigned_staff_list),
                    'Staff.active' => 1
                ),
                'fields' => array('Staff.id', 'Staff.full_name'),
                'contain' => array(
                    'Title' => array('fields' => array('Title.title')),
                    'User' => array(
                        'fields' => array('id', 'role_id', 'username', 'active'),
                        'conditions' => array(
                            'User.role_id' => ROLE_INSTRUCTOR,
                            'User.active' => 1
                        )
                    ),
                    'CourseInstructorAssignment' => array(
                        'fields' => array('id', 'academic_year'),
                        'PublishedCourse' => array(
                            'fields' => array('id', 'course_id'),
                            'Course' => array('fields' => array('id', 'course_code_title')),
                            'conditions' => array('PublishedCourse.course_id' => $equivalent_courses)
                        )
                    )
                )
            ));

            if (!empty($instructors_detail)) {
                foreach ($instructors_detail as $sd => &$sv) {
                    $experiance = 0;
                    if (!empty($sv['CourseInstructorAssignment'])) {
                        foreach ($sv['CourseInstructorAssignment'] as $k => $v) {
                            if (!empty($v['PublishedCourse'])) {
                                $experiance++;
                            }
                        }
                    }

                    $sv['Experiance'] = $experiance;
                    $sv['course_code_title'] = $course_code_title;
                    unset($sv['CourseInstructorAssignment']);
                }

                //debug($instructors_detail);

                usort($instructors_detail, function ($a, $b) {

                    if ($a['Experiance'] < $b['Experiance']) {
                        return 1;
                    } elseif ($a['Experiance'] > $b['Experiance']) {
                        return -1;
                    }
                    return 0;
                });
            }

            $instructors_list = array();

            if (!empty($instructors_detail)) {
                foreach ($instructors_detail as $ik => $iv) {
                    if ($iv['User']['active'] == 1) {
                        $instructors_list[$iv['Staff']['id']] = $iv['Title']['title'] . ' ' . $iv['Staff']['full_name'] . ' gave ' . $iv['Experiance'] . ' times';
                    }
                }
            }

            $this->set(
                compact(
                    'instructors_list',
                    'selected_department_id',
                    'selected_course_type',
                    'selected_section_id',
                    'selected_published_course_id',
                    'selected_course_split_section_id',
                    'selected_academicyear',
                    'selected_course_title',
                    'selected_program_id',
                    'selected_program_type_id',
                    'selected_year_level_id',
                    'selected_semester',
                    'isprimary',
                    'instructors',
                    'instructors_detail',
                    'courses',
                    'course_code_title'
                )
            );
        } else {
            $instructors_detail = null;
            $instructors_list = null;
            $this->set(
                compact(
                    'instructors_list',
                    'selected_department_id',
                    'selected_course_type',
                    'selected_section_id',
                    'selected_published_course_id',
                    'selected_course_split_section_id',
                    'selected_academicyear',
                    'selected_course_title',
                    'selected_program_id',
                    'selected_program_type_id',
                    'selected_year_level_id',
                    'selected_semester',
                    'isprimary',
                    'instructors',
                    'instructors_detail',
                    'courses',
                    'course_code_title'
                )
            );
        }
    }

    public function assignInstructorUpdate()
    {

        //debug($this->request->data);

        $selected_academicyear = $this->request->data['CourseInstructorAssignment']['academic_year'];
        $this->Session->write('selected_academicyear', $selected_academicyear);
        $selected_program_id = $this->request->data['CourseInstructorAssignment']['selected_program_id'];
        $this->Session->write('selected_program_id', $selected_program_id);
        $selected_program_type_id = $this->request->data['CourseInstructorAssignment']['selected_program_type_id'];
        $this->Session->write('selected_program_type_id', $selected_program_type_id);

        if (ROLE_COLLEGE != $this->role_id) {
            $selected_year_level_id = $this->request->data['CourseInstructorAssignment']['selected_year_level_id'];
            $this->Session->write('selected_year_level_id', $selected_year_level_id);
        }

        $selected_semester = $this->request->data['CourseInstructorAssignment']['semester'];
        $this->Session->write('selected_semester', $selected_semester);

        $count_assign_instructor_for_lecture = 0;

        if (/* $this->request->data['CourseInstructorAssignment']['isprimary'] == 1 */ 1) {
            $explode_data = explode("+", $this->request->data['CourseInstructorAssignment']['type']);
            if ((isset($explode_data[0]) && strcasecmp($explode_data[0], 'Lecture') == 0) || strcasecmp(
                    trim($this->request->data['CourseInstructorAssignment']['type']),
                    'Lecture'
                ) == 0) {
                $count_assign_instructor_for_lecture = $this->CourseInstructorAssignment->find('count', array(
                    'conditions' => array(
                        'CourseInstructorAssignment.published_course_id' => $this->request->data['CourseInstructorAssignment']['published_course_id'],
                        'CourseInstructorAssignment.section_id' => $this->request->data['CourseInstructorAssignment']['section_id'],
                        'CourseInstructorAssignment.isprimary' => 1,
                        'CourseInstructorAssignment.type LIKE ' => 'Lecture%',
                        'CourseInstructorAssignment.academic_year' => $this->request->data['CourseInstructorAssignment']['academic_year'],
                        'CourseInstructorAssignment.semester' => $this->request->data['CourseInstructorAssignment']['semester'],
                        "OR" => array(
                            "CourseInstructorAssignment.course_split_section_id" => $this->request->data['CourseInstructorAssignment']['course_split_section_id'],
                            "CourseInstructorAssignment.course_split_section_id is null",
                            "CourseInstructorAssignment.course_split_section_id = 0",
                            "CourseInstructorAssignment.course_split_section_id = ''",
                        )
                    )
                ));
            } else {
                $count_assign_instructor_for_lecture = $this->CourseInstructorAssignment->find('count', array(
                    'conditions' => array(
                        'CourseInstructorAssignment.published_course_id' => $this->request->data['CourseInstructorAssignment']['published_course_id'],
                        'CourseInstructorAssignment.section_id' => $this->request->data['CourseInstructorAssignment']['section_id'],
                        //'CourseInstructorAssignment.isprimary' => 1,
                        'CourseInstructorAssignment.type LIKE ' => $this->request->data['CourseInstructorAssignment']['type'] . '%',
                        'CourseInstructorAssignment.academic_year' => $this->request->data['CourseInstructorAssignment']['academic_year'],
                        'CourseInstructorAssignment.semester' => $this->request->data['CourseInstructorAssignment']['semester'],
                        "OR" => array(
                            "CourseInstructorAssignment.course_split_section_id" => $this->request->data['CourseInstructorAssignment']['course_split_section_id'],
                            "CourseInstructorAssignment.course_split_section_id is null",
                            "CourseInstructorAssignment.course_split_section_id = 0",
                            "CourseInstructorAssignment.course_split_section_id = ''",
                        )
                    )
                ));
            }
        }

        $count_assign_instructor = 0;

        $count_assign_instructor = $this->CourseInstructorAssignment->find('count', array(
            'conditions' => array(
                'CourseInstructorAssignment.published_course_id' => $this->request->data['CourseInstructorAssignment']['published_course_id'],
                'CourseInstructorAssignment.staff_id' => $this->request->data['CourseInstructorAssignment']['staff_id'],
                'CourseInstructorAssignment.section_id' => $this->request->data['CourseInstructorAssignment']['section_id'],
                'CourseInstructorAssignment.isprimary' => $this->request->data['CourseInstructorAssignment']['isprimary'],
                'CourseInstructorAssignment.academic_year' => $this->request->data['CourseInstructorAssignment']['academic_year'],
                'CourseInstructorAssignment.semester' => $this->request->data['CourseInstructorAssignment']['semester'],
                "OR" => array(
                    "CourseInstructorAssignment.course_split_section_id" => $this->request->data['CourseInstructorAssignment']['course_split_section_id'],
                    "CourseInstructorAssignment.course_split_section_id is null",
                    "CourseInstructorAssignment.course_split_section_id = 0",
                    "CourseInstructorAssignment.course_split_section_id = ''",
                )
            )
        ));

        $check_for_duplicate_instructor_assignment = $this->CourseInstructorAssignment->find('count', array(
            'conditions' => array(
                'CourseInstructorAssignment.published_course_id' => $this->request->data['CourseInstructorAssignment']['published_course_id'],
                'CourseInstructorAssignment.staff_id' => $this->request->data['CourseInstructorAssignment']['staff_id'],
                'CourseInstructorAssignment.section_id' => $this->request->data['CourseInstructorAssignment']['section_id'],
                //'CourseInstructorAssignment.isprimary' => $this->request->data['CourseInstructorAssignment']['isprimary'],
                'CourseInstructorAssignment.academic_year' => $this->request->data['CourseInstructorAssignment']['academic_year'],
                'CourseInstructorAssignment.semester' => $this->request->data['CourseInstructorAssignment']['semester'],
                "OR" => array(
                    "CourseInstructorAssignment.course_split_section_id" => $this->request->data['CourseInstructorAssignment']['course_split_section_id'],
                    "CourseInstructorAssignment.course_split_section_id is null",
                    "CourseInstructorAssignment.course_split_section_id = 0",
                    "CourseInstructorAssignment.course_split_section_id = ''",
                )
            )
        ));

        $instructor_name = $this->CourseInstructorAssignment->Staff->field(
            'Staff.full_name',
            array('Staff.id' => $this->request->data['CourseInstructorAssignment']['staff_id'])
        ); //only for display

        if (empty($this->request->data['CourseInstructorAssignment']['course_split_section_id'])) {
            unset($this->request->data['CourseInstructorAssignment']['course_split_section_id']);
        }

        $is_primary_instructor = ($this->request->data['CourseInstructorAssignment']['isprimary'] == 1 ? true : false);
        $assignment_type = $this->request->data['CourseInstructorAssignment']['type'];

        $pub_cours_id = $this->CourseInstructorAssignment->find('first', array(
            'conditions' => array(
                'CourseInstructorAssignment.published_course_id' => $this->request->data['CourseInstructorAssignment']['published_course_id'],
            ),
            'contain' => array(
                'PublishedCourse' => array(
                    'Course' => array('id', 'course_code_title'),
                    'Section' => array('id', 'name')
                ),
                'Staff' => array('id', 'full_name')
            ),
            'order' => array('CourseInstructorAssignment.isprimary' => 'DESC'),
            'recursive' => -1
        ));

        //debug($pub_cours_id);

        $instructor_name2 = (isset($pub_cours_id['Staff']['id']) && !empty($pub_cours_id['Staff']['full_name']) ? $pub_cours_id['Staff']['full_name'] : '');

        if ($count_assign_instructor_for_lecture && !empty($instructor_name2) && !empty($instructor_name) && $instructor_name2 !== $instructor_name) {
            $instructor_name = $instructor_name2;
            $is_primary_instructor = (isset($pub_cours_id['CourseInstructorAssignment']['isprimary']) && $pub_cours_id['CourseInstructorAssignment']['isprimary'] == 1 ? true : false);
            $assignment_type = (isset($pub_cours_id['CourseInstructorAssignment']['type']) && !empty($pub_cours_id['CourseInstructorAssignment']['type']) ? $pub_cours_id['CourseInstructorAssignment']['type'] : false);
        }
        //$course_title_code = (isset($pub_cours_id['PublishedCourse']['Course']['id']) && !empty($pub_cours_id['PublishedCourse']['Course']['course_code_title']) ? $pub_cours_id['PublishedCourse']['Course']['course_code_title'] : '');
        $assigned_section_name = (isset($pub_cours_id['PublishedCourse']['Section']['id']) && !empty($pub_cours_id['PublishedCourse']['Section']['name']) ? $pub_cours_id['PublishedCourse']['Section']['name'] : '');

        $program_id_of_published_course = (isset($pub_cours_id['PublishedCourse']['program_id']) && !empty($pub_cours_id['PublishedCourse']['program_id']) ? $pub_cours_id['PublishedCourse']['program_id'] : '');
        $pre_freshman_published_course = (isset($pub_cours_id['PublishedCourse']['year_level_id']) && !empty($pub_cours_id['PublishedCourse']['year_level_id']) ? false : true);

        if (empty($assigned_section_name)) {
            $assigned_section_name = $this->CourseInstructorAssignment->Section->field(
                'Section.name',
                array('Section.id' => $this->request->data['CourseInstructorAssignment']['section_id'])
            );
        }

        $for_assign_instructor = 1;

        if (isset($this->request->data['CourseInstructorAssignment']['published_course_id']) && !empty($this->request->data['CourseInstructorAssignment']['published_course_id'])) {
            if ((!empty($program_id_of_published_course) && $program_id_of_published_course == PROGRAM_REMEDIAL) || $pre_freshman_published_course) {
                // do not use published course id, it will, need to set search paramenters now and then, irritative
            } else {
                $for_assign_instructor = $this->request->data['CourseInstructorAssignment']['published_course_id'];
            }
        }

        if ((/* $this->request->data['CourseInstructorAssignment']['isprimary'] == 1 */ 1) && ($count_assign_instructor_for_lecture > 0)) {
            $this->Flash->warning(
                __(
                    $instructor_name . ' is already assigned for ' . (!empty($assignment_type) ? $assignment_type : 'Lecture or Lecture.+Lab') . ' for the course ' . $this->request->data['CourseInstructorAssignment']['course_code_title'] . ' for ' . $assigned_section_name . ' as ' . ($is_primary_instructor ? 'primary instructor' : 'secondary instructor') . '. To change assignment to different instructor, please discard the previous assignment first or change the assignment type different from ' . $this->request->data['CourseInstructorAssignment']['type'] . '.'
                )
            );
            return $this->redirect(array('action' => 'assign_course_instructor', $for_assign_instructor));
        } else {
            if ($check_for_duplicate_instructor_assignment > 0) {
                $this->Flash->warning(
                    __(
                        ' You don\'t need to reassign ' . $instructor_name . ' again for ' . (!empty($assignment_type) ? $assignment_type : 'Lecture or Lecture.+Lab') . ' for the course ' . $this->request->data['CourseInstructorAssignment']['course_code_title'] . ' for ' . $assigned_section_name . ' as ' . ($is_primary_instructor ? 'primary instructor' : 'secondary instructor') . '.  The instructor is already assigned previously.'
                    )
                );
                return $this->redirect(array('action' => 'assign_course_instructor', $for_assign_instructor));
            } else {
                if ($count_assign_instructor == 0) {
                    //$this->CourseInstructorAssignment->create();
                    if ($this->CourseInstructorAssignment->save($this->request->data)) {
                        //Notifications
                        ClassRegistry::init('AutoMessage')->sendNotificationOnInstructorAssignment(
                            $this->request->data['CourseInstructorAssignment']['published_course_id']
                        );
                        $this->Flash->success(
                            __(
                                $instructor_name . ' is assigned for ' . (!empty($assignment_type) ? $assignment_type . ' for ' : '') . ' the course ' . $this->request->data['CourseInstructorAssignment']['course_code_title'] . ' for ' . $assigned_section_name . ' as ' . ($is_primary_instructor ? 'primary instructor' : 'secondary instructor') . '.'
                            )
                        );
                        $this->redirect(array('action' => 'assign_course_instructor', $for_assign_instructor));
                    } else {
                        $this->Flash->error(
                            __(
                                $instructor_name . ' couldn\'t be assigned for ' . (!empty($assignment_type) ? $assignment_type . ' for ' : '') . ' the course ' . $this->request->data['CourseInstructorAssignment']['course_code_title'] . ' for ' . $assigned_section_name . ' as ' . ($is_primary_instructor ? 'primary instructor' : 'secondary instructor') . '. Please try again.'
                            )
                        );
                        $this->redirect(array('action' => 'assign_course_instructor', $for_assign_instructor));
                    }
                } else {
                    $this->Flash->error(
                        __(
                            $instructor_name . ' is already assigned for ' . (!empty($assignment_type) ? $assignment_type . ' for ' : '') . ' the course ' . $this->request->data['CourseInstructorAssignment']['course_code_title'] . ' for ' . $assigned_section_name . ' as ' . ($is_primary_instructor ? 'primary instructor' : 'secondary instructor') . '. To change assignment to different instructor, please discard the previous assignment first or change course type of the assignment.'
                        )
                    );
                    return $this->redirect(array('action' => 'assign_course_instructor', $for_assign_instructor));
                }
            }
        }
    }

    public function getAssignedCoursesOfInstructorBySectionForCombo(
        $acadamic_year1 = null,
        $acadamic_year2 = null,
        $semester = null,
        $instructor_id = null
    ) {

        $this->layout = 'ajax';
        //debug($acadamic_year1.'/'.$acadamic_year2);
        //debug($semester);

        if (empty($instructor_id)) {
            $instructor_id = $this->Auth->user('id');
        }

        $instructor_id = $this->CourseInstructorAssignment->Staff->field(
            'id',
            array('user_id' => $this->Auth->user('id'))
        );
        $publishedCourses = $this->CourseInstructorAssignment->listOfCoursesInstructorAssignedBySection(
            $acadamic_year1 . '/' . $acadamic_year2,
            $semester,
            $instructor_id
        );
        //debug($publishedCourses);
        $this->set(compact('publishedCourses'));
    }

    public function getAssignedFxForInstructor(
        $acadamic_year1 = null,
        $acadamic_year2 = null,
        $semester = null,
        $instructor_id = null
    ) {

        $this->layout = 'ajax';

        if (empty($instructor_id)) {
            $instructor_id = $this->Auth->user('id');
        }

        $instructor_id = $this->CourseInstructorAssignment->Staff->field(
            'id',
            array('user_id' => $this->Auth->user('id'))
        );
        $publishedCourses = $this->CourseInstructorAssignment->listOfFxCoursesInstructorAssignedBySection(
            $acadamic_year1 . '/' . $acadamic_year2,
            $semester,
            $instructor_id
        );

        $this->set(compact('publishedCourses'));
    }

    public function getAssignedGradeEntryForInstructor(
        $acadamic_year1 = null,
        $acadamic_year2 = null,
        $semester = null,
        $instructor_id = null
    ) {

        $this->layout = 'ajax';
        $publishedCourses = array();

        if ($this->Session->read('Auth.User')['role_id'] == ROLE_INSTRUCTOR) {
            if (empty($instructor_id)) {
                $instructor_id = $this->Auth->user('id');
            }
            $instructor_id = $this->CourseInstructorAssignment->Staff->field(
                'id',
                array('user_id' => $this->Auth->user('id'))
            );
            $publishedCourses = $this->CourseInstructorAssignment->listOfAssignedGradeEntryAssignedBySection(
                $acadamic_year1 . '/' . $acadamic_year2,
                $semester,
                $instructor_id
            );
            debug($publishedCourses);
        }

        $this->set(compact('publishedCourses'));
    }

    public function resetDepartment($college_id = null)
    {

        $this->layout = 'ajax';
        $departments = null;
        $departments = $this->CourseInstructorAssignment->Section->Department->find(
            'list',
            array('fields' => 'Department.shortname', 'conditions' => array('Department.college_id' => $college_id))
        );
        $this->set(compact('departments'));
    }

    public function viewInstructorCourseLoad()
    {

        /*
            1. We need to have a tool that will display staffs current assignment of courses from  all department so that the hosting department
            (if possible other departments also) can get instructor load.
            Filtering criteria will be by Academic Year, Semester,  Department, and Instructor.  Out of this instructor will be mandatory and the other will be optional (--- All ---). (High)
        */

        $current_acy_and_semester = $this->AcademicYear->currentAcyAndSemester();

        $current_acy_and_semester = $this->AcademicYear->currentAcyAndSemester();

        $curr_acy = '"' . $current_acy_and_semester['academic_year'] . '"';
        $curr_sem = '"' . $current_acy_and_semester['semester'] . '"';

        if (!empty($this->request->data['Search'])) {
            $this->__init_search();
            $curr_acy = '"' . $this->request->data['Search']['academic_year'] . '"';
            $curr_sem = '"' . $this->request->data['Search']['semester'] . '"';
        }

        $options = array();

        // search
        if (!empty($this->request->data) && isset($this->request->data['viewInstructorLoad'])) {
            $this->__init_search();

            if (!empty($this->request->data['Search']['department_id'])) {
                $options[] = array('Staff.department_id' => $this->request->data['Search']['department_id']);
            }

            if (!empty($this->request->data['Search']['staff_id'])) {
                $options[] = array('CourseInstructorAssignment.staff_id' => $this->request->data['Search']['staff_id']);
            }

            if (!empty($this->request->data['Search']['semester'])) {
                $options[] = array('CourseInstructorAssignment.semester' => $this->request->data['Search']['semester']);
            }

            if (!empty($this->request->data['Search']['academic_year'])) {
                $options[] = array('CourseInstructorAssignment.academic_year' => $this->request->data['Search']['academic_year']);
            }

            $instructor_loads = array();

            if (!empty($options)) {
                // $this->paginate['conditions'] = $options;
                // $this->Paginator->settings = $this->paginate;

                $this->Paginator->settings = array(
                    'conditions' => $options,
                    'contain' => array(
                        'Section' => array(
                            'fields' => array('Section.name')
                        ),
                        'CourseSplitSection',
                        'Staff' => array(
                            'conditions' => array('Staff.active' => 1),
                            'Title' => array('fields' => array('Title.title')),
                            'Position' => array('fields' => array('Position.position'))
                        ),
                        'PublishedCourse' => array(
                            'fields' => array(
                                'PublishedCourse.id',
                                'PublishedCourse.academic_year',
                                'PublishedCourse.semester'
                            ),
                            'conditions' => array(
                                'PublishedCourse.academic_year' => (isset($this->request->data['Search']['academic_year']) ? $this->request->data['Search']['academic_year'] : $current_acy_and_semester['academic_year']),
                                'PublishedCourse.semester' => (isset($this->request->data['Search']['semester']) ? $this->request->data['Search']['semester'] : $current_acy_and_semester['semester']),
                            ),
                            'Course' => array(
                                'fields' => array(
                                    'Course.course_code_title',
                                    'Course.credit',
                                    'Course.course_code',
                                    'Course.course_title',
                                    'Course.lecture_hours',
                                    'Course.tutorial_hours',
                                    'Course.laboratory_hours',
                                    'Course.course_detail_hours'
                                ),
                                'Curriculum' => array(
                                    'fields' => array(
                                        'Curriculum.type_credit',
                                        'Curriculum.year_introduced',
                                        'Curriculum.registrar_approved',
                                        'Curriculum.lock',
                                        'Curriculum.active',
                                    )
                                )
                            ),
                            'CourseRegistration' => array(
                                'limit' => 1
                            )
                        )
                    ),
                    'order' => array('CourseInstructorAssignment.created' => 'DESC')
                );

                //$instructor_loads = $this->Paginator->paginate('CourseInstructorAssignment');

                try {
                    $instructor_loads = $this->Paginator->paginate($this->modelClass);
                    //$this->set(compact('instructor_loads'));
                } catch (NotFoundException $e) {
                    /* if (!empty($this->request->data['Search'])) {
                        unset($this->request->data['Search']['page']);
                        unset($this->request->data['Search']['sort']);
                        unset($this->request->data['Search']['direction']);
                    }
                    unset($this->passedArgs); */
                    return $this->redirect(array('action' => 'view_instructor_course_load'));
                } catch (Exception $e) {
                    /* if (!empty($this->request->data['Search'])) {
                        unset($this->request->data['Search']['page']);
                        unset($this->request->data['Search']['sort']);
                        unset($this->request->data['Search']['direction']);
                    }
                    unset($this->passedArgs); */
                    return $this->redirect(array('action' => 'view_instructor_course_load'));
                }
            }


            if (empty($instructor_loads)) {
                $this->Flash->error('There is no course load in the system with in the given criteria.');
            } else {
                $instructor_loads = $this->CourseInstructorAssignment->instructorLoadOrganizedByAcademicYearAndSemester(
                    $instructor_loads
                );

                $staff_details = $this->CourseInstructorAssignment->Staff->find(
                    'first',
                    array(
                        'conditions' => array('Staff.id' => $this->request->data['Search']['staff_id']),
                        'contain' => array('Position', 'Title')
                    )
                );

                $this->set(compact('instructor_loads', 'staff_details'));
            }
        }


        if (!empty($this->request->data['Search']['department_id'])) {
            $staffs = array();
            $selected_department_id = $this->request->data['Search']['department_id'];

            $instructors = $this->CourseInstructorAssignment->Staff->find('all', array(
                'conditions' => array(
                    'Staff.department_id' => $selected_department_id,
                    'Staff.active' => 1,
                    //'Staff.user_id  IN (SELECT id FROM users WHERE role_id =' . ROLE_INSTRUCTOR . ' OR (is_admin = 1 and role_id =' . ROLE_DEPARTMENT . '))',
                    'Staff.user_id IN (SELECT id FROM users WHERE active = 1 AND role_id =' . ROLE_INSTRUCTOR . ')',
                    "Staff.id IN (SELECT `cia`.`staff_id` FROM `course_instructor_assignments` `cia` JOIN `published_courses` `pc` ON `cia`.`published_course_id` = `pc`.`id` WHERE `pc`.`academic_year` = $curr_acy AND `pc`.`semester` = $curr_sem )"
                ),
                'contain' => array('Position', 'Title')
            ));


            if (!empty($instructors)) {
                foreach ($instructors as $in => $value) {
                    $staffs[$value['Position']['position']][$value['Staff']['id']] = $value['Title']['title'] . ' ' . $value['Staff']['full_name'];
                }
            }

            $selected_college_id = $this->CourseInstructorAssignment->Staff->Department->field(
                'Department.college_id',
                array('Department.id' => $selected_department_id)
            );
            debug($selected_college_id);
        } else {
            $staffs = array();

            if ($this->role_id == ROLE_DEPARTMENT) {
                $selected_department_id = $this->department_id;
                $selected_college_id = $this->college_id; //$this->CourseInstructorAssignment->Staff->Department->field('Department.college_id', array('Department.id' => $selected_department_id));
            } else {
                $departmentsss = $this->CourseInstructorAssignment->Staff->Department->find(
                    'list',
                    array('fields' => array('id', 'id'), 'conditions' => array('Department.active' => 1))
                );
                $selected_department_id = array_keys($departmentsss)[0];
                $selected_college_id = $this->CourseInstructorAssignment->Staff->Department->field(
                    'Department.college_id',
                    array('Department.id' => $selected_department_id)
                );
            }

            $instructors = $this->CourseInstructorAssignment->Staff->find('all', array(
                'conditions' => array(
                    'Staff.department_id' => $selected_department_id,
                    'Staff.active' => 1,
                    //'Staff.user_id  IN (SELECT id FROM users WHERE role_id =' . ROLE_INSTRUCTOR . ' OR (is_admin = 1 and role_id =' . ROLE_DEPARTMENT . '))',
                    'Staff.user_id IN (SELECT id FROM users WHERE active = 1 AND role_id =' . ROLE_INSTRUCTOR . ')',
                    "Staff.id IN (SELECT `cia`.`staff_id` FROM `course_instructor_assignments` `cia` JOIN `published_courses` `pc` ON `cia`.`published_course_id` = `pc`.`id` WHERE `pc`.`academic_year` = $curr_acy AND `pc`.`semester` = $curr_sem )"
                ),
                'contain' => array('Position', 'Title')
            ));


            if (!empty($instructors)) {
                foreach ($instructors as $in => $value) {
                    $staffs[$value['Position']['position']][$value['Staff']['id']] = $value['Title']['title'] . ' ' . $value['Staff']['full_name'];
                }
            }
            //debug($selected_college_id);
        }

        $colleges = $this->CourseInstructorAssignment->Staff->College->find(
            'list',
            array('fields' => array('id', 'name'), 'conditions' => array('College.active' => 1))
        );
        $collegesss = $this->CourseInstructorAssignment->Staff->College->find(
            'list',
            array('fields' => array('id', 'id'), 'conditions' => array('College.active' => 1))
        );

        debug(array_keys($collegesss)[0]);

        if (!empty($this->request->data['Search']['college_id'])) {
            $selected_college_id = $this->request->data['Search']['college_id'];

            $departments = $this->CourseInstructorAssignment->Staff->Department->find(
                'list',
                array(
                    'fields' => array('id', 'name'),
                    'conditions' => array('Department.college_id' => $selected_college_id, 'Department.active' => 1)
                )
            );
            $departmentsss = $this->CourseInstructorAssignment->Staff->Department->find(
                'list',
                array(
                    'fields' => array('id', 'name'),
                    'conditions' => array('Department.college_id' => $selected_college_id, 'Department.active' => 1)
                )
            );

            if (!empty($this->request->data['Search']['department_id'])) {
                $selected_department_id = $this->request->data['Search']['department_id'];
            } else {
                $selected_department_id = array_keys($departmentsss)[0];
                debug($selected_department_id);
            }
        } else {
            $departments = $this->CourseInstructorAssignment->Staff->Department->find(
                'list',
                array('fields' => array('id', 'name'), 'conditions' => array('Department.active' => 1))
            );
            $departmentsss = $this->CourseInstructorAssignment->Staff->Department->find(
                'list',
                array('fields' => array('id', 'name'), 'conditions' => array('Department.active' => 1))
            );

            if ($this->role_id == ROLE_DEPARTMENT) {
                $selected_department_id = $this->department_id;
                $selected_college_id = $this->college_id; //$this->CourseInstructorAssignment->Staff->College->find('list', array('fields' => array('id', 'name'), 'conditions' => array('College.active' => 1, 'College.id' => $this->college_id)));
            } else {
                if (!empty($this->request->data['Search']['department_id'])) {
                    $selected_department_id = $this->request->data['Search']['department_id'];
                } else {
                    $selected_department_id = array_keys($departmentsss)[0];
                    debug($selected_department_id);
                }
                //$colleges = $this->CourseInstructorAssignment->Staff->College->find('list', array('fields' => array('id', 'name'), 'conditions' => array('College.active' => 1)));
                $selected_college_id = array_keys($collegesss)[0];

                debug($selected_college_id);
            }
        }

        $this->set(
            compact(
                'departments',
                'staffs',
                'colleges',
                'selected_department_id',
                'selected_college_id',
                'current_acy_and_semester'
            )
        );
    }

    public function assignCourseInstructor($for_assign_instructor = null)
    {

        if (!empty($for_assign_instructor)) {
            if ($this->Session->check('search_data')) {
                $search_session = $this->Session->read('search_data');
                $this->request->data['Search'] = $search_session;
                debug($this->request->data);
            }

            $this->request->data['getPublishedCourse'] = true;

            $data = $this->CourseInstructorAssignment->PublishedCourse->find('first', array(
                'conditions' => array(
                    'PublishedCourse.id' => $for_assign_instructor,
                    'PublishedCourse.given_by_department_id' => $this->department_id,
                ),
                'contain' => array('YearLevel')
            ));

            debug($data);

            if (!empty($data)) {
                $this->request->data['Search']['academicyear'] = $data['PublishedCourse']['academic_year'];

                if ($data['PublishedCourse']['given_by_department_id'] != $data['PublishedCourse']['department_id']) {
                    //find the equivalent year level of the published department

                    $publishing_department_year_level = $this->CourseInstructorAssignment->PublishedCourse->YearLevel->find(
                        'first',
                        array(
                            'conditions' => array(
                                'YearLevel.name' => $data['YearLevel']['name'],
                                'YearLevel.department_id' => $this->department_id
                            ),
                            'recursive' => -1
                        )
                    );

                    //debug($publishing_department_year_level['YearLevel']['id']);

                    if (!empty($publishing_department_year_level['YearLevel']['id']) && $publishing_department_year_level['YearLevel']['id'] > 0) {
                        $yearLevelArrays[] = $publishing_department_year_level['YearLevel']['id'];
                    } else {
                        $yearLevelArrays[] = 0;
                    }

                    $this->request->data['Search']['year_level_id'] = $yearLevelArrays;
                } else {
                    if (isset($data['YearLevel']['id'])) {
                        $yearLevelArrays[] = $data['YearLevel']['id'];
                        $this->request->data['Search']['year_level_id'] = $yearLevelArrays;
                    }
                    //debug($yearLevelArrays);
                }

                $this->request->data['Search']['semester'] = $data['PublishedCourse']['semester'];
                $this->request->data['Search']['program_id'] = $data['PublishedCourse']['program_id'];
                $this->request->data['Search']['program_type_id'] = $data['PublishedCourse']['program_type_id'];
                $this->request->data['Search']['section_id'] = $data['PublishedCourse']['section_id'];
            }
        }

        if (!empty($this->request->data) && isset($this->request->data['getPublishedCourse'])) {
            $everythingfine = false;

            switch ($this->request->data) {
                case empty($this->request->data['Search']['academicyear']):
                    $this->Flash->error('Please select the academic year you want to assign instructor.');
                    break;
                case empty($this->request->data['Search']['semester']):
                    $this->Flash->error('Please select the semester of the course you want to assign instructor.');
                    break;
                case empty($this->request->data['Search']['program_id']):
                    $this->Flash->error('Please select the program of the course you want to assign instructor.');
                    break;
                case empty($this->request->data['Search']['program_type_id']):
                    $this->Flash->error('Please select the program type of the course you want to assign instructor.');
                    break;
                default:
                    $everythingfine = true;
            }


            if ($everythingfine) {
                $this->__init_search();

                debug($this->request->data['Search']['year_level_id']);

                $semester = $this->request->data['Search']['semester'];
                $academic_year = $this->request->data['Search']['academicyear'];

                if (!empty($this->request->data['Search']['year_level_id'])) {
                    $yearLevelIds = $this->CourseInstructorAssignment->getAllDepartmentYearLevelMatchingYear(
                        $this->request->data['Search']['year_level_id']
                    );
                }

                if ($this->role_id == ROLE_COLLEGE) {
                    $publishedCourses = $this->CourseInstructorAssignment->PublishedCourse->find('all', array(
                        'conditions' => array(
                            'PublishedCourse.drop' => 0,
                            'PublishedCourse.college_id' => $this->college_id,
                            'PublishedCourse.semester' => $this->request->data['Search']['semester'],
                            'PublishedCourse.academic_year' => $this->request->data['Search']['academicyear'],
                            //'PublishedCourse.year_level_id' => $this->request->data['Search']['year_level_id'],
                            'OR' => array(
                                'PublishedCourse.year_level_id' => $this->request->data['Search']['year_level_id'],
                                'PublishedCourse.year_level_id IS NULL',
                                'PublishedCourse.year_level_id = 0',
                            ),
                            'PublishedCourse.program_id' => $this->request->data['Search']['program_id'],
                            'PublishedCourse.program_type_id' => $this->request->data['Search']['program_type_id']
                        ),
                        //'order' => array('PublishedCourse.created' => 'DESC'),
                        'order' => array(
                            'PublishedCourse.year_level_id' => 'ASC',
                            'PublishedCourse.college_id' => 'ASC',
                            'PublishedCourse.department_id' => 'ASC',
                            'PublishedCourse.section_id' => 'ASC',
                            'PublishedCourse.created' => 'DESC'
                        ),
                        'contain' => array(
                            'YearLevel' => array('order' => array('YearLevel.name')),
                            'College',
                            'Department' => array('College'),
                            'GivenByDepartment' => array('College'),
                            'Program',
                            'CourseInstructorAssignment' => array(
                                'fields' => array(
                                    'CourseInstructorAssignment.id',
                                    'CourseInstructorAssignment.staff_id',
                                    'CourseInstructorAssignment.type',
                                    'CourseInstructorAssignment.isprimary',
                                    'CourseInstructorAssignment.course_split_section_id'
                                ),
                                'Staff' => array(
                                    'fields' => array('Staff.full_name'),
                                    'conditions' => array('Staff.active' => 1),
                                    'Title' => array('fields' => array('title')),
                                    'Position' => array('fields' => array('position'))
                                )
                            ),
                            'SectionSplitForPublishedCourse' => array('CourseSplitSection'),
                            'ProgramType',
                            'Section',
                            'Course' => array(
                                'Prerequisite' => array(
                                    'Course',
                                    'PrerequisiteCourse'
                                )
                            )
                        )
                    ));
                } else {
                    if ($this->role_id == ROLE_DEPARTMENT) {
                        if (!empty($this->request->data['Search']['section_id']) && !empty($yearLevelIds)) {
                            $publishedCourses = $this->CourseInstructorAssignment->PublishedCourse->find('all', array(
                                'conditions' => array(
                                    'PublishedCourse.drop' => 0,
                                    'PublishedCourse.given_by_department_id' => $this->department_id,
                                    'PublishedCourse.semester' => $this->request->data['Search']['semester'],
                                    'PublishedCourse.academic_year' => $this->request->data['Search']['academicyear'],
                                    'PublishedCourse.section_id' => $this->request->data['Search']['section_id'],
                                    //'PublishedCourse.year_level_id' => $yearLevelIds,
                                    'OR' => array(
                                        'PublishedCourse.year_level_id' => $yearLevelIds,
                                        'PublishedCourse.year_level_id IS NULL',
                                        'PublishedCourse.year_level_id = 0',
                                    ),
                                    'PublishedCourse.program_id' => $this->request->data['Search']['program_id'],
                                    'PublishedCourse.program_type_id' => $this->request->data['Search']['program_type_id']
                                ),
                                //'order' => array('PublishedCourse.created' => 'DESC'),
                                'order' => array(
                                    'PublishedCourse.year_level_id' => 'ASC',
                                    'PublishedCourse.college_id' => 'ASC',
                                    'PublishedCourse.department_id' => 'ASC',
                                    'PublishedCourse.section_id' => 'ASC',
                                    'PublishedCourse.created' => 'DESC'
                                ),
                                'contain' => array(
                                    'YearLevel' => array('order' => array('YearLevel.name')),
                                    'College',
                                    'Department' => array('College'),
                                    'GivenByDepartment' => array('College'),
                                    'Program',
                                    'CourseInstructorAssignment' => array(
                                        'fields' => array(
                                            'CourseInstructorAssignment.id',
                                            'CourseInstructorAssignment.staff_id',
                                            'CourseInstructorAssignment.type',
                                            'CourseInstructorAssignment.isprimary',
                                            'CourseInstructorAssignment.course_split_section_id'
                                        ),
                                        'Staff' => array(
                                            'fields' => array('Staff.full_name'),
                                            'conditions' => array('Staff.active' => 1),
                                            'Title' => array('fields' => array('title')),
                                            'Position' => array('fields' => array('position'))
                                        )
                                    ),
                                    'SectionSplitForPublishedCourse' => array('CourseSplitSection'),
                                    'ProgramType',
                                    'Section',
                                    'Course' => array(
                                        'Prerequisite' => array(
                                            'Course',
                                            'PrerequisiteCourse'
                                        )
                                    ),
                                ),
                                //'limit' => 5000000000
                            ));
                        } else {
                            if (!empty($yearLevelIds)) {
                                $publishedCourses = $this->CourseInstructorAssignment->PublishedCourse->find(
                                    'all',
                                    array(
                                        'conditions' => array(
                                            'PublishedCourse.drop' => 0,
                                            'PublishedCourse.given_by_department_id' => $this->department_id,
                                            'PublishedCourse.semester' => $this->request->data['Search']['semester'],
                                            'PublishedCourse.academic_year' => $this->request->data['Search']['academicyear'],
                                            // 'PublishedCourse.year_level_id'=>$this->request->data['Search']['year_level_id'],
                                            //'PublishedCourse.year_level_id' => $yearLevelIds,
                                            'OR' => array(
                                                'PublishedCourse.year_level_id' => $yearLevelIds,
                                                'PublishedCourse.year_level_id IS NULL',
                                                'PublishedCourse.year_level_id = 0',
                                            ),
                                            'PublishedCourse.program_id' => $this->request->data['Search']['program_id'],
                                            'PublishedCourse.program_type_id' => $this->request->data['Search']['program_type_id']
                                        ),
                                        //'order' => array('PublishedCourse.created' => 'DESC'),
                                        'order' => array(
                                            'PublishedCourse.year_level_id' => 'ASC',
                                            'PublishedCourse.college_id' => 'ASC',
                                            'PublishedCourse.department_id' => 'ASC',
                                            'PublishedCourse.section_id' => 'ASC',
                                            'PublishedCourse.created' => 'DESC'
                                        ),
                                        'contain' => array(
                                            'YearLevel' => array('order' => array('YearLevel.name')),
                                            'College',
                                            'Department' => array('College'),
                                            'GivenByDepartment' => array('College'),
                                            'Program',
                                            'CourseInstructorAssignment' => array(
                                                'fields' => array(
                                                    'CourseInstructorAssignment.id',
                                                    'CourseInstructorAssignment.staff_id',
                                                    'CourseInstructorAssignment.type',
                                                    'CourseInstructorAssignment.isprimary',
                                                    'CourseInstructorAssignment.course_split_section_id'
                                                ),
                                                'Staff' => array(
                                                    'fields' => array('Staff.full_name'),
                                                    'conditions' => array('Staff.active' => 1),
                                                    'Title' => array('fields' => array('title')),
                                                    'Position' => array('fields' => array('position'))
                                                )
                                            ),
                                            'SectionSplitForPublishedCourse' => array('CourseSplitSection'),
                                            'ProgramType',
                                            'Section',
                                            'Course' => array(
                                                'Prerequisite' => array(
                                                    'Course',
                                                    'PrerequisiteCourse'
                                                )
                                            ),
                                        ),
                                        //'limit' => 5000000000
                                    )
                                );
                            } else {
                                $publishedCourses = $this->CourseInstructorAssignment->PublishedCourse->find(
                                    'all',
                                    array(
                                        'conditions' => array(
                                            'PublishedCourse.drop' => 0,
                                            'PublishedCourse.given_by_department_id' => $this->department_id,
                                            'PublishedCourse.semester' => $this->request->data['Search']['semester'],
                                            'PublishedCourse.academic_year' => $this->request->data['Search']['academicyear'],
                                            'PublishedCourse.program_id' => $this->request->data['Search']['program_id'],
                                            'PublishedCourse.program_type_id' => $this->request->data['Search']['program_type_id']
                                        ),
                                        //'order' => array('PublishedCourse.created' => 'DESC'),
                                        'order' => array(
                                            'PublishedCourse.year_level_id' => 'ASC',
                                            'PublishedCourse.college_id' => 'ASC',
                                            'PublishedCourse.department_id' => 'ASC',
                                            'PublishedCourse.section_id' => 'ASC',
                                            'PublishedCourse.created' => 'DESC'
                                        ),
                                        'contain' => array(
                                            'YearLevel' => array('order' => array('YearLevel.name')),
                                            'College',
                                            'Department' => array('College'),
                                            'GivenByDepartment' => array('College'),
                                            'Program',
                                            'CourseInstructorAssignment' => array(
                                                'fields' => array(
                                                    'CourseInstructorAssignment.id',
                                                    'CourseInstructorAssignment.staff_id',
                                                    'CourseInstructorAssignment.type',
                                                    'CourseInstructorAssignment.isprimary',
                                                    'CourseInstructorAssignment.course_split_section_id'
                                                ),
                                                'Staff' => array(
                                                    'fields' => array('Staff.full_name'),
                                                    'conditions' => array('Staff.active' => 1),
                                                    'Title' => array('fields' => array('title')),
                                                    'Position' => array('fields' => array('position'))
                                                )
                                            ),
                                            'SectionSplitForPublishedCourse' => array('CourseSplitSection'),
                                            'ProgramType',
                                            'Section',
                                            'Course' => array(
                                                'Prerequisite' => array(
                                                    'Course',
                                                    'PrerequisiteCourse'
                                                )
                                            ),
                                        ),
                                        //'limit' => 5000000000
                                    )
                                );
                            }
                        }
                    }
                }

                if (empty($publishedCourses)) {
                    $this->Flash->error(
                        'No published course is found with the given criteria that needs instructor assignment.'
                    );
                } else {
                    $organizedPublishedCourse = $this->CourseInstructorAssignment->organized_Published_courses_by_for_assignment(
                        $publishedCourses
                    );
                    $sections_array = $organizedPublishedCourse['sections_array'];
                    $course_type_array = $organizedPublishedCourse['course_type_array'];

                    $this->set(
                        compact(
                            'organizedPublishedCourse',
                            'sections_array',
                            'course_type_array',
                            'semester',
                            'academic_year'
                        )
                    );
                    $this->set('turn_off_search', true);
                }
            } else {
                //$this->redirect(array('action'=>'add'));
            }
        }

        if ($this->role_id == ROLE_COLLEGE) {
            $yearLevels['0'] = 'Pre/Freshman/Remedial';

            $programTypesAllowed = Configure::read('program_types_available_for_registrar_college_level_permissions');

            if (isset($programTypesAllowed[PROGRAM_TYPE_ADVANCE_STANDING])) {
                unset($programTypesAllowed[PROGRAM_TYPE_ADVANCE_STANDING]);
            }

            $programTypesAllowed[PROGRAM_TYPE_EVENING] = PROGRAM_TYPE_EVENING;
            $programTypesAllowed[PROGRAM_TYPE_WEEKEND] = PROGRAM_TYPE_WEEKEND;

            ksort($programTypesAllowed);

            debug($programTypesAllowed);

            $programTypes = $this->CourseInstructorAssignment->PublishedCourse->ProgramType->find(
                'list',
                array('conditions' => array('ProgramType.id' => $programTypesAllowed))
            );

            if (count($this->college_ids) == 1 && $this->role_id == ROLE_COLLEGE && (in_array(
                        REMEDIAL_PROGRAM_NATURAL_COLLEGE_ID,
                        $this->college_ids
                    ) || in_array(REMEDIAL_PROGRAM_SOCIAL_COLLEGE_ID, $this->college_ids))) {
                $programs = $this->CourseInstructorAssignment->PublishedCourse->Program->find(
                    'list',
                    array(
                        'conditions' => array(
                            'Program.id' => Configure::read(
                                'programs_available_for_registrar_college_level_permissions'
                            )
                        )
                    )
                );
            } else {
                $programs = $this->CourseInstructorAssignment->PublishedCourse->Program->find(
                    'list',
                    array('conditions' => array('Program.id' => PROGRAM_UNDEGRADUATE))
                );
            }
        } else {
            if ($this->role_id == ROLE_DEPARTMENT) {
                $yearLevels = $this->CourseInstructorAssignment->PublishedCourse->YearLevel->find(
                    'list',
                    array('conditions' => array('YearLevel.department_id' => $this->department_id))
                );
                $yearLevels['0'] = 'Pre/Freshman/Remedial';
                $programs = $this->CourseInstructorAssignment->PublishedCourse->Program->find(
                    'list',
                    array('conditions' => array('Program.active' => 1))
                );
                $programTypes = $this->CourseInstructorAssignment->PublishedCourse->ProgramType->find(
                    'list',
                    array('conditions' => array('ProgramType.active' => 1))
                );
            }
        }


        //$programs = $this->CourseInstructorAssignment->PublishedCourse->Program->find('list', array('conditions' => array('Program.active' => 1)));
        //$programTypes = $this->CourseInstructorAssignment->PublishedCourse->ProgramType->find('list', array('conditions' => array('ProgramType.active' => 1)));

        $departments = $this->CourseInstructorAssignment->PublishedCourse->Department->find(
            'list',
            array('conditions' => array('Department.active' => 1))
        );
        $colleges = $this->CourseInstructorAssignment->PublishedCourse->College->find(
            'list',
            array('conditions' => array('College.active' => 1))
        );

        $defaultCollege = $this->college_id;
        $defaultDepartment = $this->department_id;

        $this->set(
            compact(
                'yearLevels',
                'departments',
                'colleges',
                'defaultCollege',
                'defaultDepartment',
                'programs',
                'programTypes'
            )
        );
    }

    public function changeCourseDepartment()
    {

        if (!empty($this->request->data) && isset($this->request->data['changeDispatch'])) {
            if (!empty($this->request->data['PublishedCourse'])) {
                if ($this->CourseInstructorAssignment->PublishedCourse->saveAll(
                    $this->request->data['PublishedCourse'],
                    array('validate' => 'first')
                )) {
                    $this->Flash->success(
                        'The course instructor assignment has been dispatch or changed, and it will be visible for the department to do instructor assignment.'
                    );
                    $this->request->data['getPublishedCourse'] = true;
                } else {
                    $this->Flash->error(
                        'The course departments could not be updated or dispatched. Please, try again.'
                    );
                }
            } else {
                $this->Flash->info('No changes found to update!');
            }
        }

        $this->__init_search();

        if (!empty($this->request->data) && isset($this->request->data['getPublishedCourse'])) {
            $everythingfine = false;
            switch ($this->request->data) {
                case empty($this->request->data['Search']['academicyear']):
                    $this->Flash->error(
                        'Please select the academic year you want to dispatch or change course department.'
                    );
                    break;
                case empty($this->request->data['Search']['semester']):
                    $this->Flash->error(
                        'Please select the semester of the course you want to dispatch or change course department.'
                    );
                    break;
                case empty($this->request->data['Search']['year_level_id']):
                    $this->Flash->error(
                        'Please select the year level  of the course you want to dispatch or change course department.'
                    );
                    break;
                case empty($this->request->data['Search']['program_id']):
                    $this->Flash->error(
                        'Please select the program of the course you want to dispatch or change course department.'
                    );
                    break;
                case empty($this->request->data['Search']['program_type_id']):
                    $this->Flash->error(
                        'Please select the program type of the course you want to dispatch or change course department.'
                    );
                    break;
                default:
                    $everythingfine = true;
            }

            if ($everythingfine) {
                $semester = $this->request->data['Search']['semester'];
                $academic_year = $this->request->data['Search']['academicyear'];

                if ($this->role_id == ROLE_DEPARTMENT) {
                    $publishedCourses = $this->CourseInstructorAssignment->PublishedCourse->find('all', array(
                        'conditions' => array(
                            'PublishedCourse.drop' => 0,
                            'PublishedCourse.department_id' => $this->department_id,
                            'PublishedCourse.semester' => $this->request->data['Search']['semester'],
                            'PublishedCourse.academic_year' => $this->request->data['Search']['academicyear'],
                            'PublishedCourse.year_level_id' => $this->request->data['Search']['year_level_id'],
                            'PublishedCourse.program_id' => $this->request->data['Search']['program_id'],
                            'PublishedCourse.program_type_id' => $this->request->data['Search']['program_type_id']
                        ),
                        'contain' => array(
                            'YearLevel' => array('id', 'name'),
                            'College' => array('id', 'name', 'type', 'stream'),
                            'Department' => array(
                                'fields' => array('id', 'name', 'type', 'college_id'),
                                'College' => array('id', 'name', 'type', 'stream'),
                            ),
                            'GivenByDepartment' => array(
                                'fields' => array('id', 'name', 'type', 'college_id'),
                                'College' => array('id', 'name', 'type', 'stream'),
                            ),
                            'Program' => array('id', 'name'),
                            'CourseInstructorAssignment' => array(
                                'Staff' => array(
                                    'fields' => array('id', 'full_name', 'phone_mobile'),
                                    'Title' => array('title'),
                                    'Position' => array('position')
                                ),
                                'order' => array('isprimary' => 'DESC'),
                            ),
                            'ProgramType' => array('id', 'name'),
                            'Section',
                            'Course' => array(
                                'Prerequisite' => array(
                                    'Course',
                                    'PrerequisiteCourse'
                                ),
                                'Curriculum' => array('id', 'name', 'type_credit', 'year_introduced')
                            )
                        )
                    ));
                } else {
                    if ($this->role_id == ROLE_COLLEGE) {
                        $publishedCourses = $this->CourseInstructorAssignment->PublishedCourse->find('all', array(
                            'conditions' => array(
                                'PublishedCourse.drop' => 0,
                                'PublishedCourse.department_id is null',
                                'PublishedCourse.semester' => $this->request->data['Search']['semester'],
                                'PublishedCourse.academic_year' => $this->request->data['Search']['academicyear'],
                                'PublishedCourse.college_id' => $this->college_id,
                                //'PublishedCourse.year_level_id' => $this->request->data['Search']['year_level_id'],
                                'OR' => array(
                                    'PublishedCourse.year_level_id IS NULL',
                                    'PublishedCourse.year_level_id = 0',
                                    'PublishedCourse.year_level_id = ""'
                                ),
                                'PublishedCourse.program_id' => $this->request->data['Search']['program_id'],
                                'PublishedCourse.program_type_id' => $this->request->data['Search']['program_type_id']
                            ),
                            'contain' => array(
                                'YearLevel' => array('id', 'name'),
                                'College' => array('id', 'name', 'type', 'stream'),
                                'Department' => array(
                                    'fields' => array('id', 'name', 'type', 'college_id'),
                                    'College' => array('id', 'name', 'type', 'stream'),
                                ),
                                'GivenByDepartment' => array(
                                    'fields' => array('id', 'name', 'type', 'college_id'),
                                    'College' => array('id', 'name', 'type', 'stream'),
                                ),
                                'Program' => array('id', 'name'),
                                'CourseInstructorAssignment' => array(
                                    'Staff' => array(
                                        'fields' => array('id', 'full_name', 'phone_mobile'),
                                        'Title' => array('title'),
                                        'Position' => array('position')
                                    ),
                                    'order' => array('isprimary' => 'DESC'),
                                ),
                                'ProgramType' => array('id', 'name'),
                                'Section',
                                'Course' => array(
                                    'Prerequisite' => array(
                                        'Course',
                                        'PrerequisiteCourse'
                                    ),
                                    'Curriculum' => array('id', 'name', 'type_credit', 'year_introduced')
                                )
                            )
                        ));
                    }
                }

                if (empty($publishedCourses)) {
                    $this->Flash->error('No published course is found with the given search criteria.');
                } else {
                    $organizedPublishedCourse = $this->CourseInstructorAssignment->organized_published_courses_by_program_sections(
                        $publishedCourses
                    );
                    $year_level_id = $this->request->data['Search']['year_level_id'];
                    $program_id = $this->request->data['Search']['program_id'];
                    $program_type_id = $this->request->data['Search']['program_type_id'];
                    $this->set(
                        compact(
                            'organizedPublishedCourse',
                            'year_level_id',
                            'program_id',
                            'program_type_id',
                            'semester',
                            'academic_year'
                        )
                    );
                    $this->set('turn_off_search', true);
                }
            } else {
                //$this->redirect(array('action'=>'add'));
            }
        }

        if ($this->role_id == ROLE_COLLEGE) {
            $yearLevels['0'] = 'Pre/Freshman';
            $college_departments = $this->CourseInstructorAssignment->PublishedCourse->Department->find(
                'list',
                array(
                    'conditions' => array(
                        'Department.active' => 1,
                        /* 'Department.college_id' => $this->college_id */
                    )
                )
            );

            if (!empty($college_departments)) {
                debug(key($college_departments));
                $defaultDepartment = ''; //key($college_departments);
            } else {
                $defaultDepartment = '';
            }

            $defaultCollege = ''; //  $this->college_id;

            $programTypesAllowed = Configure::read('program_types_available_for_registrar_college_level_permissions');

            if (isset($programTypesAllowed[PROGRAM_TYPE_ADVANCE_STANDING])) {
                unset($programTypesAllowed[PROGRAM_TYPE_ADVANCE_STANDING]);
            }

            $programTypesAllowed[PROGRAM_TYPE_EVENING] = PROGRAM_TYPE_EVENING;
            $programTypesAllowed[PROGRAM_TYPE_WEEKEND] = PROGRAM_TYPE_WEEKEND;

            ksort($programTypesAllowed);

            debug($programTypesAllowed);

            $programTypes = $this->CourseInstructorAssignment->PublishedCourse->ProgramType->find(
                'list',
                array('conditions' => array('ProgramType.id' => $programTypesAllowed))
            );

            if (count($this->college_ids) == 1 && $this->role_id == ROLE_COLLEGE && (in_array(
                        REMEDIAL_PROGRAM_NATURAL_COLLEGE_ID,
                        $this->college_ids
                    ) || in_array(REMEDIAL_PROGRAM_SOCIAL_COLLEGE_ID, $this->college_ids))) {
                $programs = $this->CourseInstructorAssignment->PublishedCourse->Program->find(
                    'list',
                    array(
                        'conditions' => array(
                            'Program.id' => Configure::read(
                                'programs_available_for_registrar_college_level_permissions'
                            )
                        )
                    )
                );
            } else {
                $programs = $this->CourseInstructorAssignment->PublishedCourse->Program->find(
                    'list',
                    array('conditions' => array('Program.id' => PROGRAM_UNDEGRADUATE))
                );
            }
        } else {
            if ($this->role_id == ROLE_DEPARTMENT) {
                $yearLevels = $this->CourseInstructorAssignment->PublishedCourse->YearLevel->find(
                    'list',
                    array('conditions' => array('YearLevel.department_id' => $this->department_id))
                );
                $defaultDepartment = $this->department_id;
                $defaultCollege = $this->CourseInstructorAssignment->PublishedCourse->Department->field(
                    'Department.college_id',
                    array('Department.id' => $this->department_id)
                );

                $programs = $this->CourseInstructorAssignment->PublishedCourse->Program->find(
                    'list',
                    array('conditions' => array('Program.id' => $this->program_ids))
                );
                $programTypes = $this->CourseInstructorAssignment->PublishedCourse->ProgramType->find(
                    'list',
                    array('conditions' => array('ProgramType.id' => $this->program_type_ids))
                );
            }
        }

        $defaultCollege = '';

        //$programs = $this->CourseInstructorAssignment->PublishedCourse->Program->find('list', array('conditions' => array('Program.active' => 1)));
        //$programTypes = $this->CourseInstructorAssignment->PublishedCourse->ProgramType->find('list', array('conditions' => array('ProgramType.active' => 1)));

        $departments = $this->CourseInstructorAssignment->PublishedCourse->Department->find('list', array(
            'conditions' => array(
                'Department.active' => 1,
                'accept_course_dispatch' => 1
            ),
            'order' => array('Department.college_id' => 'ASC', 'Department.id' => 'ASC', 'Department.name' => 'ASC')
        ));

        $departmentsAll = $this->CourseInstructorAssignment->PublishedCourse->Department->find('list');

        $colleges = $this->CourseInstructorAssignment->PublishedCourse->College->find(
            'list',
            array('conditions' => array('College.active' => 1))
        );

        //$defaultCollege = $this->college_id;
        //$defaultDepartment = $this->department_id;

        $this->set(
            compact(
                'yearLevels',
                'departments',
                'colleges',
                'defaultCollege',
                'defaultDepartment',
                'programs',
                'programTypes',
                'departmentsAll'
            )
        );
    }

    public function __init_search()
    {

        if (!empty($this->request->data['Search'])) {
            $this->Session->write('search_data', $this->request->data['Search']);
        } else {
            if ($this->Session->check('search_data')) {
                $this->request->data['Search'] = $this->Session->read('search_data');
            }
        }
    }

    public function getInstructorCombo($department_id = null)
    {

        $this->layout = 'ajax';

        $current_acy_and_semester = $this->AcademicYear->currentAcyAndSemester();

        $curr_acy = '"' . $current_acy_and_semester['academic_year'] . '"';
        $curr_sem = '"' . $current_acy_and_semester['semester'] . '"';

        if ($this->Session->check('search_data')) {
            $this->request->data['Search'] = $this->Session->read('search_data');
            $curr_acy = '"' . $this->request->data['Search']['academic_year'] . '"';
            $curr_sem = '"' . $this->request->data['Search']['semester'] . '"';
        }

        $staffs = $this->CourseInstructorAssignment->Staff->find('all', array(
            'conditions' => array(
                'Staff.department_id' => $department_id,
                'Staff.active' => 1,
                //'Staff.user_id  IN (SELECT id FROM users WHERE role_id =' . ROLE_INSTRUCTOR . ' OR (is_admin = 1 and role_id =' . ROLE_DEPARTMENT . '))',
                'Staff.user_id IN (SELECT id FROM users WHERE active = 1 AND role_id =' . ROLE_INSTRUCTOR . ')',
                "Staff.id IN (SELECT `cia`.`staff_id` FROM `course_instructor_assignments` `cia` JOIN `published_courses` `pc` ON `cia`.`published_course_id` = `pc`.`id` WHERE `pc`.`academic_year` = $curr_acy AND `pc`.`semester` = $curr_sem )"
            ),
            'contain' => array('Position', 'Title')
        ));

        $instructors = array();

        if (!empty($staffs)) {
            foreach ($staffs as $in => $value) {
                $instructors[$value['Position']['position']][$value['Staff']['id']] = $value['Title']['title'] . ' ' . $value['Staff']['full_name'];
            }
        }

        $this->set(compact('instructors'));
    }
}
