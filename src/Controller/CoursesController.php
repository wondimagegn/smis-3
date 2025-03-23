<?php

namespace App\Controller;

use App\Controller\AppController;
use Cake\Event\Event;
use Cake\ORM\TableRegistry;
use Cake\Core\Configure;

class CoursesController extends AppController
{

    public $name = 'Courses';
    public $menuOptions = array(
        'parent' => 'curriculums',
        'exclude' => array(
            'index',
            'print_courses_pdf',
            'export_courses_xls',
            'deleteChildren',
            'search'
        ),
        'alias' => array(
            'add' => 'Add New Course',
            'index' => 'List Courses',
            'list_courses' => 'List Course of a Curriculum',
        )
    );

    public $paginate = [];

    public function initialize()
    {

        parent::initialize();
        $this->loadComponent('AcademicYear');
        $this->loadComponent('Paginator'); // Ensure Paginator is loaded
        $this->viewBuilder()->setHelpers(['xls', 'Media.Meida']);
    }

    public function beforeFilter(Event $event)
    {

        parent::beforeFilter($event);
        $this->Auth->Allow(
            'search',
            'view'
        );
    }

    public function search()
    {

        $url['action'] = 'index';

        foreach ($this->request->data as $k => $v) {
            foreach ($v as $kk => $vv) {
                $url[$k . '.' . $kk] = $vv;
            }
        }
        return $this->redirect($url, null, true);
    }

    public function index()
    {

        if (!empty($this->request->data) && isset($this->request->data['search'])) {
            $options = array();
            if ($this->role_id == ROLE_DEPARTMENT) {
                $options[] = array("Course.department_id" => $this->department_id);
            } else {
                if ($this->role_id == ROLE_COLLEGE) {
                    $department_ids = $this->Course->Department->find('list', array(
                        'conditions' => array('Department.college_id' => $this->college_id),
                        'fields' => array('Department.id', 'Department.id')
                    ));

                    if (!empty($this->request->data['Search']['department_id'])) {
                        $options[] = array("Course.department_id" => $this->request->data['Search']['department_id']);
                    } else {
                        $options[] = array("Course.department_id" => $department_ids);
                    }
                } else {
                    if ($this->role_id == ROLE_REGISTRAR || $this->role_id == $this->Session->read(
                            'Auth.User'
                        )['Role']['parent_id']) {
                        if (!empty($this->request->data['Search']['department_id'])) {
                            $options[] = array("Course.department_id" => $this->request->data['Search']['department_id']);
                        } else {
                            if (!empty($this->department_ids)) {
                                $options[] = array("Course.department_id" => $this->department_ids);
                            } else {
                                if (!empty($this->college_ids)) {
                                    $options[] = array(
                                        "Course.department_id in (select id from departments where college_id in (" . join(
                                            ',',
                                            $this->college_ids
                                        ) . ")"
                                    );
                                }
                            }
                        }
                    }
                }
            }

            if (!empty($this->request->data['Search']['semester'])) {
                $options[] = array("Course.semester" => $this->request->data['Search']['semester']);
            }

            if (!empty($this->request->data['Search']['year_level_id'])) {
                $options[] = array("Course.year_level_id" => $this->request->data['Search']['year_level_id']);
            }

            if (!empty($this->request->data['Search']['curriculum_id'])) {
                $options[] = array("Course.curriculum_id" => $this->request->data['Search']['curriculum_id']);
                $courseCategories = $this->Course->CourseCategory->find('list', array(
                    'conditions' => array(
                        'CourseCategory.curriculum_id' => $this->request->data['Search']['curriculum_id']
                    ),
                    'fields' => array('CourseCategory.id', 'CourseCategory.name')
                ));

                $selected_curriculum_details = $this->Course->Curriculum->find('first', array(
                    'conditions' => array('Curriculum.id' => $this->request->data['Search']['curriculum_id']),
                    'contain' => array(
                        'Department' => array(
                            'fields' => array('id', 'name', 'type'),
                            'College' => array(
                                'fields' => array('id', 'name', 'type', 'stream'),
                                'Campus' => array('id', 'name')
                            )
                        ),
                        'Program' => array('id', 'name'),
                        'ProgramType' => array('id', 'name'),
                    ),
                    'recursive' => -1
                ));

                $this->set(compact('courseCategories', 'selected_curriculum_details'));
            }

            if (!empty($this->request->data['Search']['course_category_id'])) {
                $options[] = array("Course.course_category_id" => $this->request->data['Search']['course_category_id']);
            }

            //$this->paginate = array('limit' => 1000);
            $this->Course->recursive = 1;

            $this->Paginator->settings = array(
                'order' => array(
                    'Course.year_level_id' => 'ASC',
                    'Course.semester' => 'ASC',
                    'Course.course_title' => 'ASC',
                ),
                'contain' => array(
                    'Curriculum' => array(
                        'Department' => array('id', 'name', 'type'),
                        'Program' => array('id', 'name'),
                        'ProgramType' => array('id', 'name'),
                    ),
                    'CourseCategory',
                    'Department' => array('id', 'name', 'type'),
                    'YearLevel' => array('id', 'name'),
                    'Prerequisite' => array('PrerequisiteCourse'),
                    'GradeType' => array('id', 'type'),
                ),
                'limit' => 100,
                'maxLimit' => 100
            );

            $courses = $this->paginate($options);

            $program_name = null;
            $program_type_name = null;

            if (!empty($courses)) {
                $program_name = $this->Course->Curriculum->Program->field(
                    'Program.name',
                    array('Program.id' => $courses[0]['Curriculum']['program_id'])
                );
                $program_type_name = $this->Course->Curriculum->ProgramType->field(
                    'ProgramType.name',
                    array('ProgramType.id' => $courses[0]['Curriculum']['program_type_id'])
                );
                $selected_department = $courses[0]['Curriculum']['department_id'];
                $course_associate_array = array();

                foreach ($courses as $coursekey => $coursevalue) {
                    $course_yearlevel = $coursevalue['Course']['year_level_id'];
                    $course_semester = $coursevalue['Course']['semester'];
                    $course_associate_array[$course_yearlevel][$course_semester][] = $coursevalue;
                }

                $this->Session->write('course_associate_array', $course_associate_array);
                $this->Session->write('selected_curriculum', $this->request->data['Search']['curriculum_id']);
                $this->Session->write('program_name', $program_name);
                $this->Session->write('program_type_name', $program_type_name);
                $this->Session->write('selected_department', $selected_department);
                $this->Session->write('selected_curriculum_details', $selected_curriculum_details);

                $this->set(compact('isbeforesearch', 'program_name', 'program_type_name', 'course_associate_array'));
            }

            if (empty($courses)) {
                $this->Flash->info('No result is found for the given search criteria.');
            }
        }

        if ($this->role_id == ROLE_COLLEGE) {
            $departments = $this->Course->Department->find(
                'list',
                array('conditions' => array('Department.college_id' => $this->college_id, 'Department.active' => 1))
            );
        } else {
            if ($this->role_id == ROLE_DEPARTMENT) {
                $departments = $this->Course->Department->find(
                    'list',
                    array('conditions' => array('Department.id' => $this->department_id))
                );
                $yearLevels = $this->Course->Department->YearLevel->find(
                    'list',
                    array('conditions' => array('YearLevel.department_id' => $this->department_id))
                );

                $curriculums = $this->Course->Curriculum->find('list', array(
                    'fields' => array('Curriculum.curriculum_detail'),
                    'conditions' => array(
                        'Curriculum.department_id' => $this->department_id,
                        'Curriculum.registrar_approved' => 1
                    )
                ));
            } else {
                if ($this->role_id == ROLE_REGISTRAR || $this->role_id == $this->Session->read(
                        'Auth.User'
                    )['Role']['parent_id']) {
                    if (!empty($this->department_ids)) {
                        $departments = $this->Course->Department->find(
                            'list',
                            array(
                                'conditions' => array(
                                    'Department.id' => $this->department_ids,
                                    'Department.active' => 1
                                )
                            )
                        );
                        $college_ids = $this->Course->Department->find('list', array(
                            'conditions' => array('Department.id' => $this->department_ids),
                            'fields' => array('Department.college_id')
                        ));

                        $colleges = $this->Course->Department->College->find(
                            'list',
                            array('conditions' => array('College.id' => $college_ids, 'College.active' => 1))
                        );

                        $curriculums = $this->Course->Curriculum->find('list', array(
                            'fields' => array('Curriculum.curriculum_detail'),
                            'conditions' => array(
                                'Curriculum.department_id' => $this->department_ids,
                                'Curriculum.registrar_approved' => 1

                            )
                        ));
                    } else {
                        if (!empty($this->college_ids)) {
                            $colleges = $this->Course->Department->College->find(
                                'list',
                                array('conditions' => array('College.id' => $this->college_ids, 'College.active' => 1))
                            );
                            $departments = $this->Course->Department->find(
                                'list',
                                array(
                                    'conditions' => array(
                                        'Department.college_id' => $this->college_ids,
                                        'Department.active' => 1
                                    )
                                )
                            );

                            $curriculums = $this->Course->Curriculum->find('list', array(
                                'fields' => array('Curriculum.curriculum_detail'),
                                'conditions' => array(
                                    'Curriculum.department_id in (select id from departments where active = 1 and college_id in (' . join(
                                        ', ',
                                        $this->college_ids
                                    ) . ')',
                                    'Curriculum.registrar_approved' => 1
                                )
                            ));
                        }
                    }
                }
            }
        }

        if (isset($this->request->data['Search']['curriculum_id']) && !empty($this->request->data['Search']['curriculum_id'])) {
            $courseCategories = $this->Course->CourseCategory->find('list', array(
                'conditions' => array(
                    'CourseCategory.curriculum_id' => $this->request->data['Search']['curriculum_id']
                )
            ));

            $selected_curriculum_details = $this->Course->Curriculum->find('first', array(
                'conditions' => array('Curriculum.id' => $this->request->data['Search']['curriculum_id']),
                'contain' => array(
                    'Department' => array(
                        'fields' => array('id', 'name', 'type'),
                        'College' => array(
                            'fields' => array('id', 'name', 'type', 'stream'),
                            'Campus' => array('id', 'name')
                        )
                    ),
                    'Program' => array('id', 'name'),
                    'ProgramType' => array('id', 'name'),
                ),
                'recursive' => -1
            ));
        }

        if (isset($this->request->data['Search']['department_id']) && !empty($this->request->data['Search']['department_id'])) {
            $curriculums = $this->Course->Curriculum->find('list', array(
                'fields' => array('Curriculum.curriculum_detail'),
                'conditions' => array(
                    'Curriculum.department_id' => $this->request->data['Search']['department_id'],
                    'Curriculum.registrar_approved' => 1
                )
            ));

            $yearLevels = $this->Course->Department->YearLevel->find('list', array(
                'conditions' => array(
                    'YearLevel.department_id' => $this->request->data['Search']['department_id']
                )
            ));
        }

        $this->set(
            compact(
                'yearLevels',
                'departments',
                'curriculums',
                'colleges',
                'courseCategories',
                'selected_curriculum_details'
            )
        );

        if ($this->request->is('ajax')) {
            $term = $this->request->query('term');
            $courses = $this->Course->getCourseTitle($term);
            $this->set(compact('courses'));
            $this->set('_serialize', 'courses');
        }
    }

    public function list_courses($passed_curriculum_id = null)
    {

        if (!empty($passed_curriculum_id)) {
            $curriculum_exist = $this->Course->Curriculum->find(
                'count',
                array('conditions' => array('Curriculum.id' => $passed_curriculum_id))
            );

            if ($curriculum_exist == 0) {
                $this->Flash->warning('Invalid curriculum');
                $this->redirect(array('action' => 'index'));
            }

            $elgible_user = $this->Course->Curriculum->find(
                'count',
                array(
                    'conditions' => array(
                        'Curriculum.id' => $passed_curriculum_id,
                        'Curriculum.department_id' => $this->department_id
                    )
                )
            );

            if ($elgible_user == 0) {
                $this->Flash->warning('You are not elgible to view courses for the selected curriculum.');
                $this->redirect(array('action' => 'index'));
            }

            $this->request->data['Search']['curriculum_id'] = $passed_curriculum_id;
            $this->request->data['search'] = true;
        }

        if (!empty($this->request->data) && isset($this->request->data['search'])) {
            $options = array();

            if ($this->role_id == ROLE_DEPARTMENT) {
                $options[] = array("Course.department_id" => $this->department_id);
            } else {
                if ($this->role_id == ROLE_COLLEGE) {
                    $department_ids = $this->Course->Department->find('list', array(
                        'conditions' => array('Department.college_id' => $this->college_id, 'Department.active' => 1),
                        'fields' => array('Department.id', 'Department.id')
                    ));

                    if (!empty($this->request->data['Search']['department_id'])) {
                        $options[] = array("Course.department_id" => $this->request->data['Search']['department_id']);
                    } else {
                        $options[] = array("Course.department_id" => $department_ids);
                    }
                } else {
                    if ($this->role_id == ROLE_REGISTRAR || $this->role_id == $this->Session->read(
                            'Auth.User'
                        )['Role']['parent_id']) {
                        if (!empty($this->request->data['Search']['department_id'])) {
                            $options[] = array("Course.department_id" => $this->request->data['Search']['department_id']);
                        } else {
                            if (!empty($this->department_ids)) {
                                $options[] = array("Course.department_id" => $this->department_ids);
                            } else {
                                if (!empty($this->college_ids)) {
                                    $options[] = array(
                                        "Course.department_id in (select id from departments where college_id in (" . join(
                                            ',',
                                            $this->college_ids
                                        ) . ")"
                                    );
                                }
                            }
                        }
                    }
                }
            }

            if (!empty($this->request->data['Search']['semester'])) {
                $options[] = array("Course.semester" => $this->request->data['Search']['semester']);
            }

            if (!empty($this->request->data['Search']['year_level_id'])) {
                $options[] = array("Course.year_level_id" => $this->request->data['Search']['year_level_id']);
            }

            if (!empty($this->request->data['Search']['curriculum_id'])) {
                $options[] = array("Course.curriculum_id" => $this->request->data['Search']['curriculum_id']);
                $courseCategories = $this->Course->CourseCategory->find('list', array(
                    'conditions' => array(
                        'CourseCategory.curriculum_id' => $this->request->data['Search']['curriculum_id']
                    ),
                    'fields' => array('CourseCategory.id', 'CourseCategory.name')
                ));
                $this->set(compact('courseCategories'));
            }

            if (!empty($this->request->data['Search']['course_category_id'])) {
                $options[] = array("Course.course_category_id" => $this->request->data['Search']['course_category_id']);
            }

            //$this->paginate = array('limit'=>1000);

            $this->Paginator->settings = array(
                'order' => array(
                    'Course.year_level_id' => 'ASC',
                    'Course.semester' => 'ASC',
                    'Course.course_title' => 'ASC',
                ),
                'contain' => array(
                    'Curriculum' => array(
                        'Department' => array('id', 'name', 'type'),
                        'Program' => array('id', 'name'),
                        'ProgramType' => array('id', 'name'),
                    ),
                    'CourseCategory',
                    'Department' => array('id', 'name', 'type'),
                    'YearLevel' => array('id', 'name'),
                    'Prerequisite' => array('PrerequisiteCourse'),
                    'GradeType' => array('id', 'type'),
                ),
                'limit' => 100,
                'maxLimit' => 100
            );

            // $this->paginate['conditions'][] = $options;
            // $this->Paginator->settings = $this->paginate;

            $courses = $this->Paginator->paginate($options);

            $program_name = null;
            $program_type_name = null;

            if (!empty($courses)) {
                $program_name = $this->Course->Curriculum->Program->field(
                    'Program.name',
                    array('Program.id' => $courses[0]['Curriculum']['program_id'])
                );
                $program_type_name = $this->Course->Curriculum->ProgramType->field(
                    'ProgramType.name',
                    array('ProgramType.id' => $courses[0]['Curriculum']['program_type_id'])
                );

                if (!empty($this->request->data['Search']['curriculum_id'])) {
                    $curriculum_type = $this->Course->Curriculum->field(
                        'Curriculum.type_credit',
                        array('Curriculum.id' => $this->request->data['Search']['curriculum_id'])
                    );
                }

                $selected_department = $courses[0]['Curriculum']['department_id'];

                $selected_curriculum_details = $this->Course->Curriculum->find('first', array(
                    'conditions' => array('Curriculum.id' => $this->request->data['Search']['curriculum_id']),
                    'contain' => array(
                        'Department' => array(
                            'fields' => array('id', 'name', 'type'),
                            'College' => array(
                                'fields' => array('id', 'name', 'type', 'stream'),
                                'Campus' => array('id', 'name')
                            )
                        ),
                        'Program' => array('id', 'name'),
                        'ProgramType' => array('id', 'name'),
                    ),
                    'recursive' => -1
                ));

                $course_associate_array = array();

                foreach ($courses as $coursekey => $coursevalue) {
                    $course_yearlevel = $coursevalue['Course']['year_level_id'];
                    $course_semester = $coursevalue['Course']['semester'];
                    $course_associate_array[$course_yearlevel][$course_semester][] = $coursevalue;
                }

                $this->Session->write('course_associate_array', $course_associate_array);
                $this->Session->write('selected_curriculum', $this->request->data['Search']['curriculum_id']);
                $this->Session->write('program_name', $program_name);
                $this->Session->write('program_type_name', $program_type_name);
                $this->Session->write('selected_department', $selected_department);
                $this->Session->write('curriculum_type', $curriculum_type);

                $this->set(
                    compact(
                        'isbeforesearch',
                        'program_name',
                        'program_type_name',
                        'curriculum_type',
                        'course_associate_array',
                        'selected_curriculum_details'
                    )
                );
            }

            if (empty($courses)) {
                $this->Flash->info('No result is found for the given search criteria.');
            }
        }

        if ($this->role_id == ROLE_COLLEGE) {
            $departments = $this->Course->Department->find(
                'list',
                array('conditions' => array('Department.college_id' => $this->college_id, 'Department.active' => 1))
            );
        } elseif ($this->role_id == ROLE_DEPARTMENT) {
            $departments = $this->Course->Department->find(
                'list',
                array('conditions' => array('Department.id' => $this->department_id))
            );
            $yearLevels = $this->Course->Department->YearLevel->find(
                'list',
                array('conditions' => array('YearLevel.department_id' => $this->department_id))
            );

            $curriculums = $this->Course->Curriculum->find('list', array(
                'fields' => array('Curriculum.curriculum_detail'),
                'conditions' => array(
                    'Curriculum.department_id' => $this->department_id,
                    //'Curriculum.registrar_approved' => 1
                )
            ));
        } elseif ($this->role_id == ROLE_REGISTRAR || $this->role_id == $this->Session->read(
                'Auth.User'
            )['Role']['parent_id']) {
            if (!empty($this->department_ids)) {
                $departments = $this->Course->Department->find(
                    'list',
                    array('conditions' => array('Department.id' => $this->department_ids, 'Department.active' => 1))
                );
                $college_ids = $this->Course->Department->find('list', array(
                    'conditions' => array('Department.id' => $this->department_ids),
                    'fields' => array('Department.college_id')
                ));
                $colleges = $this->Course->Department->College->find(
                    'list',
                    array('conditions' => array('College.id' => $college_ids))
                );

                $curriculums = $this->Course->Curriculum->find('list', array(
                    'fields' => array('Curriculum.curriculum_detail'),
                    'conditions' => array(
                        'Curriculum.department_id' => $this->department_ids,
                        'Curriculum.registrar_approved' => 1
                    )
                ));
            } elseif (!empty($this->college_ids)) {
                $colleges = $this->Course->Department->College->find(
                    'list',
                    array('conditions' => array('College.id' => $this->college_ids, 'College.active' => 1))
                );
                $departments = $this->Course->Department->find(
                    'list',
                    array(
                        'conditions' => array(
                            'Department.college_id' => $this->college_ids,
                            'Department.active' => 1
                        )
                    )
                );
                $curriculums = $this->Course->Curriculum->find('list', array(
                    'fields' => array('Curriculum.curriculum_detail'),
                    'conditions' => array(
                        'Curriculum.department_id in (select id from departments where active = 1 and college_id in (' . join(
                            ', ',
                            $this->college_ids
                        ) . ')',
                        'Curriculum.registrar_approved' => 1
                    )
                ));
            }
        }

        if (isset($this->request->data['Search']['curriculum_id']) && !empty($this->request->data['Search']['curriculum_id'])) {
            $courseCategories = $this->Course->CourseCategory->find(
                'list',
                array('conditions' => array('CourseCategory.curriculum_id' => $this->request->data['Search']['curriculum_id']))
            );
        }

        if (isset($this->request->data['Search']['department_id']) && !empty($this->request->data['Search']['department_id'])) {
            $curriculums = $this->Course->Curriculum->find('list', array(
                'fields' => array('Curriculum.curriculum_detail'),
                'conditions' => array(
                    'Curriculum.department_id' => $this->request->data['Search']['department_id'],
                    'Curriculum.registrar_approved' => 1
                )
            ));

            $yearLevels = $this->Course->Department->YearLevel->find(
                'list',
                array('conditions' => array('YearLevel.department_id' => $this->request->data['Search']['department_id']))
            );
        }
        $this->set(compact('yearLevels', 'departments', 'curriculums', 'colleges', 'courseCategories'));
    }

    public function view($id = null)
    {

        if (!$id) {
            $this->Flash->info('Invalid course!');
            return $this->redirect(array('action' => 'index'));
        }

        $this->Course->id = $id;

        if (!$this->Course->exists()) {
            $this->Flash->error('Course ID doesn\'t exist.');
            return $this->redirect(array('action' => 'index'));
        }

        $course = $this->Course->find('first', array(
            'conditions' => array('Course.id' => $id),
            'contain' => array(
                'Book',
                'CourseCategory' => array(
                    'fields' => array('id', 'name')
                ),
                'Prerequisite' => array('Course', 'PrerequisiteCourse'),
                'Curriculum',
                'Department' => array('id', 'name'),
                'GradeType' => array('id', 'type'),
                'PublishedCourse' => array(
                    'conditions' => array(
                        'PublishedCourse.course_id' => $id
                    ),
                    'CourseInstructorAssignment'
                )
            )
        ));

        $this->set('course', $course);
    }

    public function add($curriculum_id = null)
    {

        if (!empty($curriculum_id)) {
            $curriculum_exist = $this->Course->Curriculum->find(
                'count',
                array('conditions' => array('Curriculum.id' => $curriculum_id))
            );

            if ($curriculum_exist == 0) {
                $this->Flash->warning('Invalid curriculum!');
                $this->redirect(array('action' => 'index'));
            }

            $elgible_user = $this->Course->Curriculum->find('count', array(
                'conditions' => array(
                    'Curriculum.id' => $curriculum_id,
                    'Curriculum.department_id' => $this->department_id
                )
            ));

            if ($elgible_user == 0) {
                $this->Flash->warning('You are not elgible to add courses for the selected curriculum!');
                $this->redirect(array('action' => 'index'));
            }
            $this->request->data['Course']['curriculum_id'] = $curriculum_id;
            $this->request->data['selectcurriculum'] = true;
        }

        $curriculums = $this->Course->Curriculum->find('list', array(
            'fields' => array('Curriculum.curriculum_detail'),
            'conditions' => array(
                'Curriculum.department_id' => $this->department_id,
                'Curriculum.registrar_approved' => 0
            )
        ));

        $isbeforesearch = 1;
        $this->set(compact('curriculums', 'isbeforesearch'));

        if (!empty($this->request->data) && isset($this->request->data['selectcurriculum'])) {
            $selected_curriculum_id = $this->request->data['Course']['curriculum_id'];

            if (!empty($selected_curriculum_id)) {
                $isbeforesearch = 0;

                $creditname = $this->Course->Curriculum->field(
                    'type_credit',
                    array('Curriculum.id' => $selected_curriculum_id)
                );
                $currProgramID = $this->Course->Curriculum->field(
                    'program_id',
                    array('Curriculum.id' => $selected_curriculum_id)
                );
                $currDeptID = $this->Course->Curriculum->field(
                    'department_id',
                    array('Curriculum.id' => $selected_curriculum_id)
                );
                // not always feasible to pick $this->department_id and $this->college_id from currently loggedin user. it will create some loophole or overwite somewhere in the entire project code.

                //To get curriculum list except the selected one
                $selected_curriculum_array = array();
                $selected_curriculum_array[$selected_curriculum_id] = $curriculums[$selected_curriculum_id];
                //$otherCurriculumList = array_diff($curriculums, $selected_curriculum_array);

                $otherCurriculumList = $this->Course->Curriculum->find('list', array(
                    'fields' => array('Curriculum.curriculum_detail'),
                    'conditions' => array(
                        'Curriculum.department_id' => $this->department_id,
                        'Curriculum.program_id' => $currProgramID,
                        'Curriculum.id <> ' => $selected_curriculum_id
                        //'Curriculum.active' => 1
                        //'Curriculum.registrar_approved' => 1
                    )
                ));

                debug($otherCurriculumList);

                $curriculum_program_name = $this->Course->Curriculum->Program->field(
                    'Program.name',
                    array('Program.id' => $currProgramID)
                );

                //Check selected curriculum whether have at least one course or not
                $is_there_a_course_in_selected_curriculum = $this->Course->find(
                    'count',
                    array('conditions' => array('Course.curriculum_id' => $selected_curriculum_id))
                );

                $prerequisite_courses = $this->Course->find(
                    'list',
                    array(
                        'conditions' => array('Course.curriculum_id' => $selected_curriculum_id),
                        'fields' => array('Course.id', 'Course.course_code_title')
                    )
                );
                //debug($prerequisite_courses);

                $yearLevels = $this->Course->YearLevel->find(
                    'list',
                    array('conditions' => array('YearLevel.department_id' => $this->department_id))
                );
                //$gradeTypes = $this->Course->GradeType->find('list',array('fields'=>'GradeType.type'));

                //to display only active and filtered grade scales and grade types according to the program of the curriculum,college or department and show latest defined Grade Type on the top of select list.
                $collegeGradeTypes = ClassRegistry::init('GradeScale')->find(
                    'list',
                    array(
                        'fields' => 'GradeScale.grade_type_id',
                        'conditions' => array(
                            'GradeScale.foreign_key' => $this->college_id,
                            'GradeScale.program_id' => $currProgramID,
                            'GradeScale.active' => 1,
                            'GradeScale.model' => 'College'
                        )
                    )
                );
                $deptGradeTypes = ClassRegistry::init('GradeScale')->find(
                    'list',
                    array(
                        'fields' => 'GradeScale.grade_type_id',
                        'conditions' => array(
                            'GradeScale.foreign_key' => $currDeptID,
                            'GradeScale.program_id' => $currProgramID,
                            'GradeScale.active' => 1,
                            'GradeScale.model' => 'Department'
                        )
                    )
                );

                //$deptGradeTypes = array(0 => 'Departmet Level Grade Types') + $deptGradeTypes;
                //$collegeGradeTypes = array(0 => 'College Level Grade Types') + $collegeGradeTypes;

                $activeGradeTypes = $collegeGradeTypes + $deptGradeTypes;

                debug($deptGradeTypes);
                debug($collegeGradeTypes);
                debug($activeGradeTypes);

                $gradeTypes = $this->Course->GradeType->find(
                    'list',
                    array(
                        'fields' => 'GradeType.type',
                        'conditions' => array('GradeType.id' => $activeGradeTypes, 'GradeType.active' => 1),
                        'order' => 'GradeType.id DESC'
                    )
                );

                //$courseCategorys
                $courseCategories = $this->Course->CourseCategory->find(
                    'list',
                    array('conditions' => array('CourseCategory.curriculum_id' => $selected_curriculum_id))
                );
                $turn_off_search = true;

                $this->set(
                    compact(
                        'turn_off_search',
                        'gradeTypes',
                        'yearLevels',
                        'creditname',
                        'courseCategories',
                        'prerequisite_courses',
                        'creditname',
                        'curriculum_program_name',
                        'otherCurriculumList',
                        'is_there_a_course_in_selected_curriculum'
                    )
                );
            } else {
                $this->Flash->error('Please select curriculum.');
            }
        }

        //**************************************************
        //To copy courses from selected curriculum to this curriculum

        if (!empty($this->request->data) && isset($this->request->data['copycourses'])) {
            if (!empty($this->request->data['Course']['form_curriculum'])) {
                $to_curriculum = $this->request->data['Course']['curriculum_id'];
                unset($this->request->data['Course']['curriculum_id']);

                $copied_courses['Course'] = $this->Course->find('all', array(
                    'conditions' => array(
                        'Course.curriculum_id' => $this->request->data['Course']['form_curriculum']
                    ),
                    'contain' => array('Prerequisite', 'Book', 'Journal', 'Weblink')
                ));

                //unset empty prerequisite/Book/Journal and weblink data before save
                $copied_courses = $this->Course->unset_empty_for_copy($copied_courses);

                foreach ($copied_courses['Course'] as &$each_courses) {
                    unset($each_courses['Course']['id']);
                    unset($each_courses['Course']['created']);
                    unset($each_courses['Course']['modified']);
                    $each_courses['Course']['curriculum_id'] = $to_curriculum;
                }

                $issaved = false;

                foreach ($copied_courses['Course'] as $each_courses_data) {
                    if ($this->Course->saveAll($each_courses_data, array('validate' => false))) {
                        $issaved = true;
                    } else {
                        $issaved = false;
                        break 1;
                    }
                }

                if ($issaved == true) {
                    //find the name of curriculum for display purpose
                    $from_curriculum_name = $this->Course->Curriculum->field(
                        'Curriculum.curriculum_detail',
                        array('Curriculum.id' => $this->request->data['Course']['form_curriculum'])
                    );
                    $to_curriculum_name = $this->Course->Curriculum->field(
                        'Curriculum.curriculum_detail',
                        array('Curriculum.id' => $to_curriculum)
                    );

                    $this->Flash->success(
                        'All courses from ' . $from_curriculum_name . ' are now copied to ' . $to_curriculum_name . '. You can further edit the new curricullum or use as it is.'
                    );
                    $this->redirect(array('action' => 'list_courses', $to_curriculum));
                } else {
                    $this->Flash->error('The course could not be copied. Please, try again.');
                }
            } else {
                $this->Flash->error('Please select a curriculum from which you want to copy courses.');

                // Incase of error to redisplay form as it is.
                $selected_curriculum_id = $this->request->data['Course']['curriculum_id'];
                $isbeforesearch = 0;

                $creditname = $this->Course->Curriculum->field(
                    'type_credit',
                    array('Curriculum.id' => $selected_curriculum_id)
                );
                $currProgramID = $this->Course->Curriculum->field(
                    'program_id',
                    array('Curriculum.id' => $selected_curriculum_id)
                );
                $currDeptID = $this->Course->Curriculum->field(
                    'department_id',
                    array('Curriculum.id' => $selected_curriculum_id)
                );

                //To get curriculum list except the selected one
                $selected_curriculum_array = array();
                $selected_curriculum_array[$selected_curriculum_id] = $curriculums[$selected_curriculum_id];
                //$otherCurriculumList = array_diff($curriculums, $selected_curriculum_array);

                $otherCurriculumList = $this->Course->Curriculum->find('list', array(
                    'fields' => array('Curriculum.curriculum_detail'),
                    'conditions' => array(
                        'Curriculum.department_id' => $this->department_id,
                        'Curriculum.program_id' => $currProgramID,
                        'Curriculum.id <> ' => $selected_curriculum_id
                        //'Curriculum.active' => 1
                        //'Curriculum.registrar_approved' => 1
                    )
                ));

                debug($otherCurriculumList);

                $curriculum_program_name = $this->Course->Curriculum->Program->field(
                    'Program.name',
                    array('Program.id' => $currProgramID)
                );

                //Check selected curriculum whether have at least one course or not
                $is_there_a_course_in_selected_curriculum = $this->Course->find('count', array(
                    'conditions' => array('Course.curriculum_id' => $selected_curriculum_id)
                ));

                $prerequisite_courses = $this->Course->find('list', array(
                    'conditions' => array(
                        'Course.curriculum_id' => $selected_curriculum_id
                    ),
                    'fields' => array('Course.id', 'Course.course_code_title')
                ));

                $yearLevels = $this->Course->YearLevel->find(
                    'list',
                    array('conditions' => array('YearLevel.department_id' => $this->department_id))
                );
                //$gradeTypes = $this->Course->GradeType->find('list',array('fields'=>'GradeType.type'));

                //to display only active and filtered grade scales according to the program of the curriculum, college or department and latest defined Grade Type on the top
                $collegeGradeTypes = ClassRegistry::init('GradeScale')->find('list', array(
                    'fields' => 'GradeScale.grade_type_id',
                    'conditions' => array(
                        'GradeScale.foreign_key' => $this->college_id,
                        'GradeScale.program_id' => $currProgramID,
                        'GradeScale.active' => 1,
                        'GradeScale.model' => 'College'
                    )
                ));

                $deptGradeTypes = ClassRegistry::init('GradeScale')->find('list', array(
                    'fields' => 'GradeScale.grade_type_id',
                    'conditions' => array(
                        'GradeScale.foreign_key' => $currDeptID,
                        'GradeScale.program_id' => $currProgramID,
                        'GradeScale.active' => 1,
                        'GradeScale.model' => 'Department'
                    )
                ));

                $activeGradeTypes = $collegeGradeTypes + $deptGradeTypes;

                $gradeTypes = $this->Course->GradeType->find('list', array(
                    'fields' => 'GradeType.type',
                    'conditions' => array(
                        'GradeType.id' => $activeGradeTypes,
                        'GradeType.active' => 1
                    ),
                    'order' => array('GradeType.id' => 'DESC')
                ));

                //$courseCategorys
                $courseCategories = $this->Course->CourseCategory->find(
                    'list',
                    array('conditions' => array('CourseCategory.curriculum_id' => $selected_curriculum_id))
                );
                $turn_off_search = true;

                $this->set(
                    compact(
                        'turn_off_search',
                        'gradeTypes',
                        'yearLevels',
                        'creditname',
                        'courseCategories',
                        'prerequisite_courses',
                        'creditname',
                        'curriculum_program_name',
                        'otherCurriculumList',
                        'is_there_a_course_in_selected_curriculum'
                    )
                );
            }
        }

        if (!empty($this->request->data) && isset($this->request->data['submit'])) {
            //unset empty prerequisite/Book/Journal and weblink data before save
            $this->request->data = $this->Course->unset_empty($this->request->data);
            $this->request->data['Course']['course_code'] = trim($this->request->data['Course']['course_code']);


            //debug($this->Course->not_allow_more_than_one_thesis($this->request->data));
            if ($this->Course->checkAttendanceRequirementValue($this->request->data['Course'])) {
                //$Prerequisite_is_unique=$this->Course->Prerequisite->prerequisiteCourseCodeUnique($this->request->data);
                if ($this->Course->Prerequisite->prerequisiteCourseCodeUnique($this->request->data)) {
                    $this->Course->create();
                    if ($this->Course->not_allow_more_than_one_thesis(
                            $this->request->data
                        ) && $this->Course->not_allow_more_than_one_exit_exam($this->request->data)) {
                        if ($this->Course->saveAll($this->request->data, array('validate' => 'first'))) {
                            $this->Flash->success(
                                'The course ' . $this->request->data['Course']['course_code'] . ' has been saved.'
                            );
                            $this->request->data['selectcurriculum'] = true;
                            $curriculum_id = $this->request->data['Course']['curriculum_id'];
                            $this->request->data = null;
                            $this->request->data['Course']['curriculum_id'] = $curriculum_id;
                            //$this->redirect(array('action'=>'add',$curriculum_id));
                        } else {
                            $this->Flash->error('The course could not be saved. Please try again.');

                            if (!empty($this->request->data['Course']['curriculum_id'])) {
                                $prerequisite_courses = $this->Course->find('list', array(
                                    'conditions' => array(
                                        'Course.curriculum_id' => $this->request->data['Course']['curriculum_id']
                                    ),
                                    'fields' => array('Course.id', 'Course.course_code_title')
                                ));
                            } else {
                                if (isset($selected_curriculum_id)) {
                                    $prerequisite_courses = $this->Course->find('list', array(
                                        'conditions' => array(
                                            'Course.curriculum_id' => $selected_curriculum_id
                                        ),
                                        'fields' => array('Course.id', 'Course.course_code_title')
                                    ));
                                }
                            }
                            $this->set(compact('prerequisite_courses'));
                        }
                    } else {
                        $error = $this->Course->invalidFields();
                        if (isset($error['thesis_error'])) {
                            $this->Flash->error($error['thesis_error'][0]);
                        }
                        if (isset($error['exit_exam_error'])) {
                            $this->Flash->error($error['exit_exam_error'][0]);
                        }
                    }
                } else {
                    $error = $this->Course->Prerequisite->invalidFields();
                    if (isset($error['prerequisite'])) {
                        $this->Flash->error($error['prerequisite'][0]);
                    }
                }
            } else {
                $error = $this->Course->invalidFields();
                if (isset($error['attendance'])) {
                    $this->Flash->error($error['attendance'][0]);
                }
            }

            $turn_off_search = true;

            $this->request->data['submit'] = true;

            //To get curriculum list except the selected one
            $selected_curriculum_array = array();
            $selected_curriculum_array[$this->request->data['Course']['curriculum_id']] = $curriculums[$this->request->data['Course']['curriculum_id']];
            //$otherCurriculumList = array_diff($curriculums, $selected_curriculum_array);


            //Check selected curriculum whether have at least one course or not
            $is_there_a_course_in_selected_curriculum = $this->Course->find('count', array(
                'conditions' => array(
                    'Course.curriculum_id' => $this->request->data['Course']['curriculum_id']
                )
            ));

            $prerequisite_courses = $this->Course->find(
                'list',
                array(
                    'conditions' => array('Course.curriculum_id' => $this->request->data['Course']['curriculum_id']),
                    'fields' => array('Course.course_code_title')
                )
            );

            $creditname = $this->Course->Curriculum->field(
                'type_credit',
                array('Curriculum.id' => $this->request->data['Course']['curriculum_id'])
            );
            $yearLevels = $this->Course->YearLevel->find(
                'list',
                array('conditions' => array('YearLevel.department_id' => $this->department_id))
            );
            $currProgramID = $this->Course->Curriculum->field(
                'program_id',
                array('Curriculum.id' => $this->request->data['Course']['curriculum_id'])
            );
            $currDeptID = $this->Course->Curriculum->field(
                'department_id',
                array('Curriculum.id' => $this->request->data['Course']['curriculum_id'])
            );

            $otherCurriculumList = $this->Course->Curriculum->find('list', array(
                'fields' => array('Curriculum.curriculum_detail'),
                'conditions' => array(
                    'Curriculum.department_id' => $this->department_id,
                    'Curriculum.program_id' => $currProgramID,
                    'Curriculum.id <> ' => $this->request->data['Course']['curriculum_id']
                    //'Curriculum.active' => 1
                    //'Curriculum.registrar_approved' => 1
                )
            ));

            debug($otherCurriculumList);

            $curriculum_program_name = $this->Course->Curriculum->Program->field(
                'Program.name',
                array('Program.id' => $currProgramID)
            );

            //$gradeTypes = $this->Course->GradeType->find('list',array('fields'=>'GradeType.type','conditions'=>array('GradeType.active'=>1)));

            //to display only active and filtered grade scales according to the program of the curriculum, college or department and latest defined Grade Type on the top
            $collegeGradeTypes = ClassRegistry::init('GradeScale')->find('list', array(
                'fields' => 'GradeScale.grade_type_id',
                'conditions' => array(
                    'GradeScale.foreign_key' => $this->college_id,
                    'GradeScale.program_id' => $currProgramID,
                    'GradeScale.active' => 1,
                    'GradeScale.model' => 'College'
                )
            ));

            $deptGradeTypes = ClassRegistry::init('GradeScale')->find('list', array(
                'fields' => 'GradeScale.grade_type_id',
                'conditions' => array(
                    'GradeScale.foreign_key' => $currDeptID,
                    'GradeScale.program_id' => $currProgramID,
                    'GradeScale.active' => 1,
                    'GradeScale.model' => 'Department'
                )
            ));

            $activeGradeTypes = $collegeGradeTypes + $deptGradeTypes;

            $gradeTypes = $this->Course->GradeType->find('list', array(
                'fields' => 'GradeType.type',
                'conditions' => array(
                    'GradeType.id' => $activeGradeTypes,
                    'GradeType.active' => 1
                ),
                'order' => array('GradeType.id' => 'DESC')
            ));

            //$courseCategorys
            $courseCategories = $this->Course->CourseCategory->find(
                'list',
                array('conditions' => array('CourseCategory.curriculum_id' => $this->request->data['Course']['curriculum_id']))
            );

            $this->set(
                compact(
                    'gradeTypes',
                    'yearLevels',
                    'creditname',
                    'turn_off_search',
                    'creditname',
                    'courseCategories',
                    'prerequisite_courses',
                    'curriculum_program_name',
                    'otherCurriculumList',
                    'is_there_a_course_in_selected_curriculum'
                )
            );
        }
    }

    public function edit($id = null)
    {

        $course_exist = $this->Course->find('count', array('conditions' => array('Course.id' => $id)));

        if ($course_exist == 0) {
            $this->Flash->warning('Invalid course!');
            return $this->redirect(array('action' => 'index'));
        }

        $elgible_user = $this->Course->find(
            'count',
            array('conditions' => array('Course.id' => $id, 'Course.department_id' => $this->department_id))
        );

        if ($elgible_user == 0) {
            $this->Flash->warning('You are not elgible to edit this course!');
            return $this->redirect(array('action' => 'index'));
        }

        $editingLocked = $this->Course->find(
            'first',
            array('conditions' => array('Course.id' => $id), 'contain' => array('Curriculum'))
        );

        if ($editingLocked['Curriculum']['lock'] == 1 || $editingLocked['Curriculum']['registrar_approved'] == 1) {
            $this->Flash->warning(
                'You can not edit the selected course. Editing is locked  by registrar. Contact your college registrar representative to unlock the curriculum this course included in.'
            );
            return $this->redirect(array('action' => 'view', $id));
        }

        if (!empty($this->request->data)) {
            //unset empty prerequisite/Book/Journal and weblink data before save
            $this->request->data = $this->Course->unset_empty($this->request->data);

            $this->request->data['Course']['course_code'] = trim($this->request->data['Course']['course_code']);

            debug($this->request->data['Course']['course_code']);

            if ($this->Course->checkAttendanceRequirementValue($this->request->data['Course'])) {
                // controller validation
                if ($this->Course->Prerequisite->prerequisiteCourseCodeUnique($this->request->data)) {
                    if ($this->Course->not_allow_more_than_one_thesis(
                            $this->request->data
                        ) && $this->Course->not_allow_more_than_one_exit_exam($this->request->data)) {
                        $this->Course->Book->deleteBookList($this->request->data['Course']['id'], $this->request->data);
                        $this->Course->Journal->deleteJournalList(
                            $this->request->data['Course']['id'],
                            $this->request->data
                        );
                        $this->Course->Weblink->deleteWeblinkList(
                            $this->request->data['Course']['id'],
                            $this->request->data
                        );
                        $this->Course->Prerequisite->deletePrerequisiteList(
                            $this->request->data['Course']['id'],
                            $this->request->data
                        );

                        if ($this->Course->saveAll($this->request->data, array('validate' => 'first'))) {
                            $this->Flash->success(
                                'The course ' . $this->request->data['Course']['course_title'] . ' has been updated'
                            );
                            $this->redirect(
                                array('action' => 'list_courses', $this->request->data['Course']['curriculum_id'])
                            );
                        } else {
                            $this->Flash->error('The course could not be updated. Please try again.');
                        }
                    } else {
                        $error = $this->Course->invalidFields();
                        if (isset($error['thesis_error'])) {
                            $this->Flash->error($error['thesis_error'][0]);
                        }
                        if (isset($error['exit_exam_error'])) {
                            $this->Flash->error($error['exit_exam_error'][0]);
                        }
                        $this->request->data = $this->request->data = $this->Course->read(null, $id);
                    }
                } else {
                    $error = $this->Course->Prerequisite->invalidFields();
                    if (isset($error['prerequisite'])) {
                        $this->Flash->error($error['prerequisite'][0]);
                    }
                }
            } else {
                $error = $this->Course->invalidFields();
                if (isset($error['attendance'])) {
                    $this->Flash->error($error['attendance'][0]);
                }
            }
        }

        if (empty($this->request->data)) {
            $this->request->data = $this->Course->read(null, $id);
        }

        if (!empty($this->request->data['Course']['curriculum_id'])) {
            $prerequisite_courses = $this->Course->find('list', array(
                'conditions' => array(
                    'Course.curriculum_id' => $this->request->data['Course']['curriculum_id'],
                    'Course.id <> ' => $this->request->data['Course']['id']
                ),
                'fields' => array('Course.id', 'Course.course_code_title')
            ));

            $creditname = $this->Course->Curriculum->field(
                'type_credit',
                array('Curriculum.id' => $this->request->data['Course']['curriculum_id'])
            );

            $currProgramID = $this->Course->Curriculum->field(
                'program_id',
                array('Curriculum.id' => $this->request->data['Course']['curriculum_id'])
            );
            $currDeptID = $this->Course->Curriculum->field(
                'department_id',
                array('Curriculum.id' => $this->request->data['Course']['curriculum_id'])
            );

            //to display only active and filtered grade scales according to the program of the curriculum,college or department and latest defined Grade Type on the top
            $collegeGradeTypes = ClassRegistry::init('GradeScale')->find('list', array(
                'fields' => 'GradeScale.grade_type_id',
                'conditions' => array(
                    'GradeScale.foreign_key' => $this->college_id,
                    'GradeScale.program_id' => $currProgramID,
                    'GradeScale.active' => 1,
                    'GradeScale.model' => 'College'
                )
            ));

            $deptGradeTypes = ClassRegistry::init('GradeScale')->find('list', array(
                'fields' => 'GradeScale.grade_type_id',
                'conditions' => array(
                    'GradeScale.foreign_key' => $currDeptID,
                    'GradeScale.program_id' => $currProgramID,
                    'GradeScale.active' => 1,
                    'GradeScale.model' => 'Department'
                )
            ));

            $activeGradeTypes = $collegeGradeTypes + $deptGradeTypes;

            $gradeTypes = $this->Course->GradeType->find('list', array(
                'fields' => 'GradeType.type',
                'conditions' => array(
                    'GradeType.id' => $activeGradeTypes,
                    'GradeType.active' => 1
                ),
                'order' => array('GradeType.id' => 'DESC')
            ));
        } else {
            if (!empty($this->request->data['Curriculum']['id'])) {
                $creditname = $this->request->data['Curriculum']['type_credit'];

                $prerequisite_courses = $this->Course->find('list', array(
                    'conditions' => array(
                        'Course.curriculum_id' => $this->request->data['Curriculum']['id'],
                        'Course.id <> ' => $this->request->data['Course']['id']
                    ),
                    'fields' => array('Course.id', 'Course.course_code_title')
                ));

                $currProgramID = $this->Course->Curriculum->field(
                    'program_id',
                    array('Curriculum.id' => $this->request->data['Curriculum']['id'])
                );
                $currDeptID = $this->Course->Curriculum->field(
                    'department_id',
                    array('Curriculum.id' => $this->request->data['Curriculum']['id'])
                );

                //to display only active and filtered grade scales according to the program of the curriculum,college or department and latest defined Grade Type on the top
                $collegeGradeTypes = ClassRegistry::init('GradeScale')->find('list', array(
                    'fields' => 'GradeScale.grade_type_id',
                    'conditions' => array(
                        'GradeScale.foreign_key' => $this->college_id,
                        'GradeScale.program_id' => $currProgramID,
                        'GradeScale.active' => 1,
                        'GradeScale.model' => 'College'
                    )
                ));

                $deptGradeTypes = ClassRegistry::init('GradeScale')->find('list', array(
                    'fields' => 'GradeScale.grade_type_id',
                    'conditions' => array(
                        'GradeScale.foreign_key' => $currDeptID,
                        'GradeScale.program_id' => $currProgramID,
                        'GradeScale.active' => 1,
                        'GradeScale.model' => 'Department'
                    )
                ));

                $activeGradeTypes = $collegeGradeTypes + $deptGradeTypes;

                $gradeTypes = $this->Course->GradeType->find('list', array(
                    'fields' => 'GradeType.type',
                    'conditions' => array(
                        'GradeType.id' => $activeGradeTypes,
                        'GradeType.active' => 1
                    ),
                    'order' => array('GradeType.id' => 'DESC')
                ));
            }
        }

        $yearLevels = $this->Course->YearLevel->find(
            'list',
            array('conditions' => array('YearLevel.department_id' => $this->department_id))
        );
        //$gradeTypes = $this->Course->GradeType->find('list',array('fields'=>'GradeType.type','conditions'=>array('GradeType.active'=>1)));

        $curriculums = $this->Course->Curriculum->find('list', array(
            'fields' => array('Curriculum.curriculum_detail'),
            'conditions' => array(
                'Curriculum.department_id' => $this->department_id,
                //'Curriculum.registrar_approved'=>1
            )
        ));

        $editCredit = $this->Course->denyEditDeleteCredit($id);
        $editCreditDetail = $this->Course->denyEditDeleteCourseBasicDetailChange($id);
        $course_code_title = $this->Course->field('course_code_title', array('Course.id' => $id));
        $courseCategories = $this->Course->CourseCategory->find(
            'list',
            array('conditions' => array('CourseCategory.curriculum_id' => $this->request->data['Course']['curriculum_id']))
        );

        $this->set(
            compact(
                'gradeTypes',
                'yearLevels',
                'course_code_title',
                'editCreditDetail',
                'editCredit',
                'curriculums',
                'creditname',
                'courseCategories',
                'prerequisite_courses'
            )
        );
    }

    public function delete($id = null)
    {

        $course_exist = $this->Course->find('count', array('conditions' => array('Course.id' => $id)));
        if ($course_exist == 0) {
            $this->Flash->warning('Invalid course');
            return $this->redirect(array('action' => 'index'));
        }

        $elgible_user = $this->Course->find(
            'count',
            array('conditions' => array('Course.id' => $id, 'Course.department_id' => $this->department_id))
        );

        $courseApproved = $this->Course->find('first', array(
            'conditions' => array(
                'Course.id' => $id,
                'Course.department_id' => $this->department_id
            ),
            'contain' => array('Curriculum')
        ));

        if ($elgible_user == 0) {
            $this->Flash->warning('You are not elgible to delete this course');
            return $this->redirect(array('action' => 'index'));
        }

        if ($courseApproved['Curriculum']['registrar_approved'] == 1) {
            $this->Flash->warning('The course curriculum is approved by registrar and not allowed for deletion.');
            return $this->redirect(array('action' => 'index'));
        }

        //check not related children
        if ($this->Course->canItBeDeleted($id)) {
            if ($this->Course->delete($id)) {
                $this->Flash->success('Course deleted.');
                //$this->redirect(array('action'=>'index'));
            }
        } else {
            $this->Flash->error(
                'The course was not deleted. It is used by other models. Please check this course is set as Prerequisite course for another course or used in course publication for any section!'
            );
        }
        return $this->redirect(array('action' => 'index'));
    }

    public function print_courses_pdf()
    {

        $course_associate_array = $this->Session->read('course_associate_array');
        $program_name = $this->Session->read('program_name');
        $program_type_name = $this->Session->read('program_type_name');
        $selected_curriculum = $this->Session->read('selected_curriculum');
        $selected_department = $this->Session->read('selected_department');

        $selected_department_name = $this->Course->Department->field(
            'Department.name',
            array('Department.id' => $selected_department)
        );
        $selected_curriculum_name = $this->Course->Curriculum->field(
            'Curriculum.name',
            array('Curriculum.id' => $selected_curriculum)
        );

        $college_id = $this->Course->Department->field(
            'Department.college_id',
            array('Department.id' => $selected_department)
        );
        $this_department_college_name = $this->Course->Department->College->field(
            'College.name',
            array('College.id' => $college_id)
        );
        $university = ClassRegistry::init('University')->find(
            'first',
            array(
                'contain' => array('Attachment' => array('order' => array('Attachment.created DESC'))),
                'order' => array('University.created DESC')
            )
        );

        $this->set(
            compact(
                'course_associate_array',
                'selected_curriculum_name',
                'program_name',
                'program_type_name',
                'selected_department_name',
                'university',
                'this_department_college_name'
            )
        );

        $this->response->type('application/pdf');
        $this->layout = '/pdf/default';
        $this->render();
    }

    public function export_courses_xls()
    {

        if ($this->Session->check('course_associate_array')) {
            $course_associate_array = $this->Session->read('course_associate_array');
            $program_name = $this->Session->read('program_name');
            $program_type_name = $this->Session->read('program_type_name');
            $selected_curriculum = $this->Session->read('selected_curriculum');
            $selected_department = $this->Session->read('selected_department');

            $selected_department_name = $this->Course->Department->field(
                'Department.name',
                array('Department.id' => $selected_department)
            );
            $selected_curriculum_name = $this->Course->Curriculum->field(
                'Curriculum.name',
                array('Curriculum.id' => $selected_curriculum)
            );

            $college_id = $this->Course->Department->field(
                'Department.college_id',
                array('Department.id' => $selected_department)
            );
            $this_department_college_name = $this->Course->Department->College->field(
                'College.name',
                array('College.id' => $college_id)
            );

            $this->set(
                compact(
                    'course_associate_array',
                    'selected_curriculum_name',
                    'program_name',
                    'program_type_name',
                    'selected_department_name',
                    'this_department_college_name'
                )
            );
        }
    }

    public function deleteChildren($id = null, $action_model_id = null)
    {

        if (!empty($action_model_id)) {
            $course_children = explode('~', $action_model_id);
            $this->Course->$course_children[1]->id = $id;

            if (!$this->Course->$course_children[1]->exists()) {
                $this->Flash->error('Invalid ' . $course_children[1] . ' id.');
                if (!empty($course_children[0]) && !empty($course_children[1]) && !empty($course_children[2])) {
                    $this->redirect(array('action' => $course_children[0], $course_children[2]));
                }
            }

            $elgible_user = $this->Course->find('count', array(
                'conditions' => array(
                    'Course.id' => $course_children[2],
                    'Course.department_id' => $this->department_id
                )
            ));

            if ($elgible_user > 0) {
                if ($this->Course->$course_children[1]->delete($id)) {
                    $this->Flash->success($course_children[1] . ' deleted');
                } else {
                    $this->Flash->error($course_children[1] . ' could not be deleted');
                }
            }

            if (!empty($course_children[0]) && !empty($course_children[1]) && !empty($course_children[2])) {
                $this->redirect(array('action' => $course_children[0], $course_children[2]));
            }
        }
    }

    public function __init_search()
    {

        if (!empty($this->request->data['Search'])) {
            $search_session = $this->request->data['Search'];
            $this->Session->write('search_data', $search_session);
        } else {
            $search_session = $this->Session->read('search_data');
            $this->request->data['Search'] = $search_session;
        }
    }
}
