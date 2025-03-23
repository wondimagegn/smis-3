<?php

namespace App\Controller;

use App\Controller\AppController;


use Cake\Event\Event;
use Cake\ORM\TableRegistry;
use Cake\Core\Configure;

class GraduationWorksController extends AppController
{

    public $name = 'GraduationWorks';
    public $menuOptions = array(
        'parent' => 'graduation',
        'exclude' => array('index'),
        'weight' => 7,
        'alias' => array(
            'add' => 'Add Graduation Work'
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
    }

    public function __init_search()
    {

        // We create a search_data session variable when we fill any criteria
        // in the search form.
        if (!empty($this->request->data['Search'])) {
            $search_session = $this->request->data['Search'];
            // Session variable 'search_data'
            $this->Session->write('search_data', $search_session);
        } else {
            $search_session = $this->Session->read('search_data');
            $this->request->data['Search'] = $search_session;
        }
    }

    public function index()
    {

        //$this->GraduationWork->recursive = 0;
        $this->__init_search();
        if ($this->Session->read('search_data')) {
            $this->request->data['viewGraduationWorks'] = true;
        }
        if (!empty($this->request->data) && isset($this->request->data['viewGraduationWorks'])) {
            $options = array();
            if (!empty($this->request->data['Search']['department_id'])) {
                $options [] = array(
                    'Student.department_id' => $this->request->data['Search']['department_id']

                );
            }
            // filter by section
            if (!empty($this->request->data['Search']['section_id'])) {
                $section_id = $this->request->data['Search']['section_id'];

                $list_of_students = ClassRegistry::init('StudentsSection')->find(
                    'list',
                    array(
                        'conditions' => array(
                            'StudentsSection.section_id' => $section_id,
                            'StudentsSection.archive' => 0
                        ),
                        'fields' => array('student_id')
                    )
                );
                $options [] = array(
                    'Student.id' => $list_of_students

                );
            }

            if (!empty($this->request->data['Search']['name'])) {
                $options [] = array(
                    'Student.first_name like' => $this->request->data['Search']['name'],
                    'Student.department_id' => $this->department_ids
                );
            }


            $graduationWorks = $this->paginate($options);
            if (empty($graduationWorks)) {
                $this->Session->setFlash(
                    '<span></span>' . __('There is no graduation works in the given criteria.'),
                    'default',
                    array('class' => 'info-box info-message')
                );
            }
        }

        $departments = $this->GraduationWork->Student->Department->find(
            'list',
            array('conditions' => array('Department.id' => $this->department_ids))
        );
        $this->set(compact('departments', 'graduationWorks'));
        //$this->set('graduationWorks', $this->paginate());
    }

    public function view($id = null)
    {

        if (!$id) {
            $this->Session->setFlash(__('Invalid graduation work'));
            return $this->redirect(array('action' => 'index'));
        }
        $this->set('graduationWork', $this->GraduationWork->read(null, $id));
    }

    public function add()
    {

        /*
        if (!empty($this->request->data)) {
            $this->GraduationWork->create();
            if ($this->GraduationWork->save($this->request->data)) {
                $this->Session->setFlash(__('The graduation work has been saved'));
                return $this->redirect(array('action' => 'index'));
            } else {
                $this->Session->setFlash(__('The graduation work could not be saved. Please, try again.'));
            }
        }
        $students = $this->GraduationWork->Student->find('list');
        $courses = $this->GraduationWork->Course->find('list');
        $this->set(compact('students', 'courses'));
        */

        if (!empty($this->request->data) && isset($this->request->data['saveGraduationWork'])) {
            $this->GraduationWork->create();
            //if (empty($this->request->data['GraduationWork']['course_id'])) {

            if ($this->GraduationWork->save($this->request->data)) {
                $this->Session->setFlash(
                    '<span></span>' . __('The graduation work has been saved.'),
                    'default',
                    array('class' => 'success-box success-message')
                );
                $this->redirect(array('action' => 'index'));
            } else {
                $this->Session->setFlash(
                    '<span></span>' . __('The graduation work could not be saved. Please, try again..'),
                    'default',
                    array('class' => 'error-box error-message')
                );
                $this->request->data['continue'] = true;
                $student_number = $this->GraduationWork->Student->field(
                    'studentnumber',
                    array('id' => trim($this->request->data['GraduationWork']['student_id']))
                );
                $this->request->data['Search']['studentID'] = $student_number;
            }
            //}
        }
        if (!empty($this->request->data) && isset($this->request->data['continue'])) {
            $everythingfine = false;
            if (!empty($this->request->data['Search']['studentID'])) {
                $check_id_is_valid = $this->GraduationWork->Student->
                find(
                    'count',
                    array(
                        'conditions' => array(
                            'Student.studentnumber' =>
                                trim($this->request->data['Search']['studentID'])
                        )
                    )
                );
                $studentIDs = 1;

                if ($check_id_is_valid > 0) {
                    $belongs_to_you = $this->GraduationWork->Student->
                    find(
                        'count',
                        array(
                            'conditions' => array(
                                'Student.studentnumber' =>
                                    trim($this->request->data['Search']['studentID']),
                                'Student.department_id' => $this->department_ids
                            )
                        )
                    );
                    // belongs to your assignment
                    if ($belongs_to_you > 0) {
                        $student_id = $this->GraduationWork->Student->field(
                            'id',
                            array('studentnumber' => trim($this->request->data['Search']['studentID']))
                        );
                        $curriculum_id = $this->GraduationWork->Student->
                        field('curriculum_id', array('Student.id' => $student_id));

                        $curriculum_has_thesis = $this->GraduationWork->Student->Curriculum->Course->find(
                            'first',
                            array(
                                'conditions' =>
                                    array('Course.curriculum_id' => $curriculum_id, 'Course.thesis' => 1)
                            )
                        );

                        if (!empty($curriculum_has_thesis)) {
                            $everythingfine = true;
                            /*$student_id=$this->GraduationWork->Student->field('id',
                            array('studentnumber'=>trim($this->request->data['Search']['studentID'])));
                            $student_section_exam_status=$this->GraduationWork->Student->
                    get_student_section($student_id);
                            $curriculum_id=$this->GraduationWork->Student->field('
                            curriculum_id
                            ',array('Student.id'=>$student_id));
                            */
                            $student_section_exam_status = $this->GraduationWork->Student->
                            get_student_section($student_id);

                            $courses = $this->GraduationWork->Student->Course->find(
                                'list',
                                array(
                                    'conditions' => array(
                                        'Course.curriculum_id' => $curriculum_id,
                                        'Course.thesis' => 1
                                    ),
                                    'fields' => array('id', 'course_title')
                                )
                            );

                            $is_already_recored = $this->GraduationWork->find(
                                'first',
                                array(
                                    'conditions' => array('GraduationWork.student_id' => $student_id),
                                    'contain' => array('Course')
                                )
                            );

                            if (!empty($is_already_recored)) {
                                $this->request->data = $this->GraduationWork->read(
                                    null,
                                    $is_already_recored['GraduationWork']['id']
                                );
                            }

                            $this->set(compact('student_section_exam_status', 'courses'));

                            $this->set(compact('studentIDs'));
                        } else {
                            $this->Session->setFlash(
                                '<span></span> ' . __(
                                    'The curriculum attached to the student does not have thesis or project course defined, please advice the department of the student to define thesis/project in their curriculum.'
                                ),
                                'default',
                                array('class' => 'error-box error-message')
                            );
                        }
                    } else {
                        $this->Session->setFlash(
                            '<span></span> ' . __(
                                'You are not elegible to maintaint the selected student graduation work.'
                            ),
                            'default',
                            array('class' => 'error-box error-message')
                        );
                    }
                } else {
                    $this->Session->setFlash(
                        '<span></span> ' . __('The provided student number is not valid.'),
                        'default',
                        array('class' => 'error-box error-message')
                    );
                }
            } else {
                $this->Session->setFlash(
                    '<span></span> ' . __('Please provide student number to maintain graduation work.'),
                    'default',
                    array('class' => 'error-box error-message')
                );
            }
        }
    }

    public function edit($id = null)
    {

        if (!$id && empty($this->request->data)) {
            $this->Session->setFlash(__('Invalid graduation work'));
            return $this->redirect(array('action' => 'index'));
        }
        if (!empty($this->request->data)) {
            if ($this->GraduationWork->save($this->request->data)) {
                $this->Session->setFlash(__('The graduation work has been saved'));
                return $this->redirect(array('action' => 'index'));
            } else {
                $this->Session->setFlash(__('The graduation work could not be saved. Please, try again.'));
            }
        }
        if (empty($this->request->data)) {
            $this->request->data = $this->GraduationWork->read(null, $id);
        }
        $students = $this->GraduationWork->Student->find('list');
        $courses = $this->GraduationWork->Course->find('list');
        $this->set(compact('students', 'courses'));
    }

    public function delete($id = null)
    {

        if (!$id) {
            $this->Session->setFlash(__('Invalid id for graduation work'));
            return $this->redirect(array('action' => 'index'));
        }
        if ($this->GraduationWork->delete($id)) {
            $this->Session->setFlash(
                '<span></span>' . __('Graduation work deleted'),
                'default',
                array('class' => 'success-box success-message')
            );
            return $this->redirect(array('action' => 'index'));
        }
        $this->Session->setFlash(
            '<span></span>' . __('Graduation work was not deleted'),
            'default',
            array('class' => 'error-box error-message')
        );
        return $this->redirect(array('action' => 'index'));
    }
}
