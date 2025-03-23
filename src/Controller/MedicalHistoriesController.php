<?php

namespace App\Controller;

use App\Controller\AppController;

use Cake\Event\Event;
use Cake\ORM\TableRegistry;
use Cake\Core\Configure;

class MedicalHistoriesController extends AppController
{

    public $name = 'MedicalHistories';
    public $menuOptions = array(
        'parent' => 'healthService',
        'exclude' => array('index'),
        'alias' => array(

            'add' => 'Manage Medical Histories'
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

        return $this->redirect(array('action' => 'add'));
    }


    public function add()
    {

        $from_edit = $this->Session->read('from_edit');
        if ($from_edit == 1) {
            $this->request->data['MedicalHistory']['studentnumber'] = $this->Session->read('edited_studentnumber');
            $this->request->data['MedicalHistory']['card_number'] = null;
            $this->Session->delete('from_edit');
            $this->request->data['search'] = true;
        } else {
            if ($this->Session->read('edited_studentnumber')) {
                $this->Session->delete('edited_studentnumber');
            }
        }
        if (isset($this->request->data['search'])) {
            $studentnumber = $this->request->data['MedicalHistory']['studentnumber'];
            $card_number = $this->request->data['MedicalHistory']['card_number'];
            if (!empty($studentnumber) || !empty($card_number)) {
                $student_id = null;
                if (!empty($studentnumber)) {
                    $student_id = $this->MedicalHistory->Student->field(
                        'Student.id',
                        array('Student.studentnumber' => $studentnumber)
                    );
                    if (empty($student_id) && !empty($card_number)) {
                        $student_id = $this->MedicalHistory->Student->field(
                            'Student.id',
                            array('Student.card_number' => $card_number)
                        );
                    }
                } else {
                    if (!empty($card_number)) {
                        $student_id = $this->MedicalHistory->Student->field(
                            'Student.id',
                            array('Student.card_number' => $card_number)
                        );
                    }
                }
                //$students = $this->Student->get_student_details_for_health($studentnumber);
                if (empty($student_id)) {
                    $this->Session->setFlash(
                        '<span></span>' . __(
                            'There is no student in this student ID and/or card number. Please provide correct student id (format example. Reg/453/88) Or card number.'
                        ),
                        'default',
                        array('class' => 'error-box error-message')
                    );
                } else {
                    $students = $this->MedicalHistory->get_student_details_for_health($student_id);
                    $medicalHistories = $this->_get_medical_histories($student_id);
                    //////TODO: Student status
                    $this->set(compact('student_id', 'students', 'medicalHistories'));
                }
            } else {
                $this->Session->setFlash(
                    '<span></span>' . __(
                        'Please provide student ID (format example. Reg/453/88) or Student card number.'
                    ),
                    'default',
                    array('class' => 'info-box info-message')
                );
            }
        }
        if (isset($this->request->data['submit'])) {
            $student_id = $this->request->data['MedicalHistory']['student_id'];
            $this->request->data['MedicalHistories']['student_id'] = $student_id;
            $this->request->data['MedicalHistories']['user_id'] = $this->Auth->user('id');
            $this->request->data['MedicalHistories']['record_type'] = $this->request->data['MedicalHistory']['record_type'];
            $this->request->data['MedicalHistories']['details'] = $this->request->data['MedicalHistory']['details'];

            $this->MedicalHistory->create();
            if ($this->MedicalHistory->save($this->request->data['MedicalHistories'])) {
                $this->Session->setFlash(
                    '<span></span>' . __(' The medical history has been saved.'),
                    'default',
                    array('class' => 'success-box success-message')
                );
            } else {
                $this->Session->setFlash(
                    '<span></span>' . __(' The medical history could not be saved. Please, try again.'),
                    'default',
                    array('class' => 'error-box error-message')
                );
            }
            $students = $this->MedicalHistory->get_student_details_for_health($student_id);
            $medicalHistories = $this->_get_medical_histories($student_id);
            //////TODO: Student status
            $this->set(compact('student_id', 'students', 'medicalHistories'));
        }
    }

    public function edit($id = null)
    {

        if (!$id && empty($this->request->data)) {
            $this->Session->setFlash(
                '<span></span>' . __('Invalid medical history'),
                'default',
                array('class' => 'error-box error-box')
            );
            return $this->redirect(array('action' => 'add'));
        }
        $from_edit = 1;
        $this->Session->write('from_edit', $from_edit);
        $student_id = $this->MedicalHistory->field('MedicalHistory.student_id', array('MedicalHistory.id' => $id));
        $edited_studentnumber = $this->MedicalHistory->Student->field(
            'Student.studentnumber',
            array('Student.id' => $student_id)
        );
        $this->Session->write('edited_studentnumber', $edited_studentnumber);
        if (!empty($this->request->data)) {
            if ($this->MedicalHistory->save($this->request->data)) {
                $this->Session->setFlash(
                    '<span></span>' . __('The medical history has been updated'),
                    'default',
                    array('class' => 'success-box success-message')
                );
                return $this->redirect(array('action' => 'add'));
            } else {
                $this->Session->setFlash(
                    '<span></span>' . __('The medical history could not be updated. Please, try again.'),
                    'default',
                    array('class' => 'error-box error-message')
                );
            }
        }
        if (empty($this->request->data)) {
            $this->request->data = $this->MedicalHistory->read(null, $id);
        }
        $students = $this->MedicalHistory->Student->find('list');
        $users = $this->MedicalHistory->User->find('list');
        $this->set(compact('students', 'users'));
    }

    public function _get_medical_histories($student_id)
    {

        if (!empty($student_id)) {
            $medicalHistories = $this->MedicalHistory->find(
                'all',
                array(
                    'conditions' => array('MedicalHistory.student_id' => $student_id),
                    'order' => array('MedicalHistory.created DESC'),
                    'recursive' => -1
                )
            );
            return $medicalHistories;
        }
    }
}
