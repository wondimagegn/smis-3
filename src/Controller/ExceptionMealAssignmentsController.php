<?php

namespace App\Controller;

use Cake\Event\Event;


class ExceptionMealAssignmentsController extends AppController
{

    public $name = 'ExceptionMealAssignments';
    public $menuOptions = array(
        'parent' => 'mealService',

        'alias' => array(
            'index' => 'List Exception ',
            'add' => 'Add To Exception'
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

    public function index()
    {

        $this->ExceptionMealAssignment->recursive = 0;
        /*
        $this->set('exceptionMealAssignments', $this->paginate());
        */
        if (!empty($this->request->data) && isset($this->request->data['continue'])) {
            $options = array();

            if (!empty($this->request->data['Search']['meal_hall_id'])) {
                $options[] = array(

                    "ExceptionMealAssignment.meal_hall_id" => $this->request->data['Search']['meal_hall_id']
                );
            }

            if (!empty($this->request->data['Search']['name'])) {
                $options[] = array(
                    "OR" => array(
                        'Student.first_name like' => trim($this->request->data['Search']['name']) . '%',
                        'Student.last_name like' => trim($this->request->data['Search']['name']) . '%',
                        'Student.middle_name LIKE ' => trim($this->request->data['Search']['name']) . '%'

                    )
                );
            }


            if (!empty($this->request->data['Search']['studentnumber'])) {
                unset($options);

                $options[] = array(

                    "Student.studentnumber" => $this->request->data['Search']['studentnumber']
                );
            }
            $exceptionMealAssignments = $this->paginate($options);
            if (empty($exceptionMealAssignments)) {
                $this->Session->setFlash(
                    '<span></span>' .
                    __('There is no student in the system in the given criteria .'),
                    'default',
                    array('class' => 'info-box info-message')
                );
                //  $this->redirect(array('action' => 'index'));

            }
        } else {
            $exceptionMealAssignments = $this->paginate();
        }
        $mealHalls = $this->ExceptionMealAssignment->MealHall->find('list');
        $this->set(compact('exceptionMealAssignments', 'mealHalls'));
    }

    /*function view($id = null) {
        if (!$id) {
            $this->Session->setFlash(__('Invalid exception meal assignment'));
            return $this->redirect(array('action' => 'index'));
        }
        $this->set('exceptionMealAssignment', $this->ExceptionMealAssignment->read(null, $id));
    }*/

    public function add()
    {

        if (!empty($this->request->data) && isset($this->request->data['saveException'])) {
            $this->ExceptionMealAssignment->create();
            foreach ($this->request->data['ExceptionMealAssignment'] as $in => &$va) {
                if ($va['accept_deny'] != "") {
                } else {
                    unset($this->request->data['ExceptionMealAssignment'][$in]);
                }
            }
            if (isset($this->request->data['ExceptionMealAssignment']) && !empty($this->request->data['ExceptionMealAssignment'])) {
                if ($this->ExceptionMealAssignment->checkDuplication($this->request->data)) {
                    if ($this->ExceptionMealAssignment->saveAll(
                        $this->request->data['ExceptionMealAssignment'],
                        array('validate' => 'first')
                    )) {
                        $this->Session->setFlash(
                            '<span></span>' . __('The exception meal assignment has been saved'),
                            'default',
                            array('class' => 'success-box success-message')
                        );
                        $this->redirect(array('action' => 'index'));
                    } else {
                        $this->Session->setFlash(
                            '<span></span>' . __(
                                'The exception meal assignment could not be saved. Please, try again.'
                            ),
                            'default',
                            array('class' => 'error-box error-message')
                        );
                    }
                } else {
                    $error = $this->ExceptionMealAssignment->invalidFields();

                    if (isset($error['error'])) {
                        $this->Session->setFlash(
                            __('<span></span>' . $error['error'][0]),
                            'default',
                            array('class' => 'error-box error-message')
                        );
                    }
                }
            } else {
                $this->Session->setFlash(
                    '<span></span>' . __(
                        'Inorder to put student in exception, you have to put either deny or allow for atleast one student.'
                    ),
                    'default',
                    array('class' => 'info-box info-message')
                );
            }
            $this->request->data['continue'] = true;
        }

        if (!empty($this->request->data) && isset($this->request->data['continue'])) {
            //$options['conditions'] = array();

            $options[] = array(
                'MealHallAssignment.academic_year' => $this->AcademicYear->current_academicyear()
            );
            if (!empty($this->request->data['Search']['meal_hall_id'])) {
                $options[] = array(

                    "MealHallAssignment.meal_hall_id" => $this->request->data['Search']['meal_hall_id']
                );
            }

            if (!empty($this->request->data['Search']['name'])) {
                $options[] = array(
                    "OR" => array(
                        'Student.first_name like' => trim($this->request->data['Search']['name']) . '%',
                        'Student.last_name like' => trim($this->request->data['Search']['name']) . '%',
                        'Student.middle_name LIKE ' => trim($this->request->data['Search']['name']) . '%',
                        //'Student.username LIKE'=>trim($this->request->data['Search']['name']).'%'
                    )
                );
            }


            if (!empty($this->request->data['Search']['studentnumber'])) {
                unset($options);
                // $options['conditions'] = array();
                $options[] = array(
                    'MealHallAssignment.academic_year' =>
                        $this->AcademicYear->current_academicyear()
                );
                $options[] = array(

                    "Student.studentnumber like " =>
                        $this->request->data['Search']['studentnumber'] . '%'
                );
                $options['conditions'] = $options;
                $studentslist = $this->ExceptionMealAssignment->MealHall->MealHallAssignment->find('all', $options);
                if (empty($studentslist)) {
                    unset($options);
                    $options[] = array(

                        "Student.studentnumber like " =>
                            $this->request->data['Search']['studentnumber'] . '%'
                    );
                    $options['conditions'] = $options;
                    $options['contain'] = array('MealHallAssignment');
                    $studentslist = $this->ExceptionMealAssignment->Student->find(
                        'all',
                        $options
                    );
                }
            } else {
                $options['conditions'] = $options;
                $studentslist = $this->ExceptionMealAssignment->MealHall->MealHallAssignment->find('all', $options);
            }

            if (empty($studentslist)) {
                $this->Session->setFlash(
                    '<span></span>' .
                    __('There is no student in the system in the given criteria .'),
                    'default',
                    array('class' => 'info-box info-message')
                );
                //  $this->redirect(array('action' => 'index'));

            }
        }


        $mealHalls = $this->ExceptionMealAssignment->MealHall->find('list');
        $this->set(compact('students', 'mealHalls', 'studentslist'));
    }

    /*function edit($id = null) {
        if (!$id && empty($this->request->data)) {
            $this->Session->setFlash(__('Invalid exception meal assignment'));
            return $this->redirect(array('action' => 'index'));
        }
        if (!empty($this->request->data)) {
            if ($this->ExceptionMealAssignment->save($this->request->data)) {
                $this->Session->setFlash(__('The exception meal assignment has been saved'));
                return $this->redirect(array('action' => 'index'));
            } else {
                $this->Session->setFlash(__('The exception meal assignment could not be saved. Please, try again.'));
            }
        }
        if (empty($this->request->data)) {
            $this->request->data = $this->ExceptionMealAssignment->read(null, $id);
        }
        $students = $this->ExceptionMealAssignment->Student->find('list');
        $mealHalls = $this->ExceptionMealAssignment->MealHall->find('list');
        $this->set(compact('students', 'mealHalls'));
    }*/

    public function delete($id = null)
    {

        if (!$id) {
            $this->Session->setFlash(__('Invalid id for exception meal assignment'));
            return $this->redirect(array('action' => 'index'));
        }
        if ($this->ExceptionMealAssignment->delete($id)) {
            $this->Session->setFlash(
                '<span></span>' . __('Exception meal assignment deleted'),
                'default',
                array('class' => 'success-box success-message')
            );
            return $this->redirect(array('action' => 'index'));
        }
        $this->Session->setFlash(
            '<span></span>' . __('Exception meal assignment was not deleted'),
            'default',
            array('class' => 'error-box error-message')
        );
        return $this->redirect(array('action' => 'index'));
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

}
