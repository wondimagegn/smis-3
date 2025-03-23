<?php

namespace App\Controller;

use App\Controller\AppController;

use Cake\Event\Event;
use Cake\ORM\TableRegistry;
use Cake\Core\Configure;

class StaffForExamsController extends AppController
{

    public $name = 'StaffForExams';

    public $menuOptions = array(
        'parent' => 'examSchedule',
        'exclude' => array('get_instructors', 'get_departments'),
        'alias' => array(
            'index' => 'List Staff For Exams',
            'add' => 'Add Staff For Exams'
        )
    );

    public function initialize()
    {

        parent::initialize();
        $this->loadComponent('AcademicYear');
        $this->loadComponent('Paginator');
    }

    public function beforeFilter(Event $event)
    {

        parent::beforeFilter($event);
        //$this->Auth->allow(array('*'));
        $this->Auth->allow('get_instructors', 'get_departments');
    }

    public function beforeRender(Event $event)
    {

        parent::beforeRender($event);

        $acyear_array_data = $this->AcademicYear->acyearArray();
        //To diplay current academic year as default in drop down list
        $defaultacademicyear = $this->AcademicYear->currentAcademicYear();

        $this->set(compact('acyear_array_data', 'defaultacademicyear'));
        unset($this->request->data['User']['password']);
    }

    public function index()
    {

        $this->StaffForExam->recursive = 0;
        $semester = '%';
        $academicyear = '%';
        if (!empty($this->request->data['StaffForExam']['academicyear'])) {
            $academicyear = $this->request->data['StaffForExam']['academicyear'];
        }
        if (!empty($this->request->data['StaffForExam']['semester'])) {
            $semester = $this->request->data['StaffForExam']['semester'];
        }
        $conditions = array(
            'StaffForExam.college_id' => $this->college_id,
            'StaffForExam.academic_year LIKE' => $academicyear,
            'StaffForExam.semester LIKE' => $semester
        );
        $this->paginate = array(
            'conditions' => $conditions,
            'contain' => array(
                'Staff' => array(
                    'fields' => array('Staff.id', 'Staff.full_name'),
                    'Title' => array('fields' => array('title')),
                    'Position' => array('fields' => array('position')),
                    'College' => array('fields' => array('College.id', 'College.name'))
                )
            )
        );

        $this->set('staffForExams', $this->paginate());
    }

    public function view($id = null)
    {

        if (!$id) {
            $this->Session->setFlash(__('Invalid staff for exam'));
            return $this->redirect(array('action' => 'index'));
        }
        $this->set('staffForExam', $this->StaffForExam->read(null, $id));
    }

    public function add()
    {

        if (!empty($this->request->data)) {
            $selected_academicyear = $this->request->data['StaffForExam']['academicyear'];
            $this->Session->write('selected_academicyear', $selected_academicyear);
            $selected_semester = $this->request->data['StaffForExam']['semester'];
            $this->Session->write('selected_semester', $selected_semester);

            $selected_instructors = array();
            if (!empty($this->request->data['StaffForExam']['Selected'])) {
                foreach ($this->request->data['StaffForExam']['Selected'] as $sfesk => $sfesv) {
                    if ($sfesv != '0') {
                        $selected_instructors[] = $sfesv;
                    }
                }
            }
            $this->request->data['StaffForExams']['college_id'] = $this->college_id;
            $this->request->data['StaffForExams']['academic_year'] = $selected_academicyear;
            $this->request->data['StaffForExams']['semester'] = $selected_semester;
            $count_selected_instructors = count($selected_instructors);
            if ($count_selected_instructors != 0) {
                $issave = false;
                foreach ($selected_instructors as $instructor_id) {
                    $this->request->data['StaffForExams']['staff_id'] = $instructor_id;
                    $this->StaffForExam->create();
                    if ($this->StaffForExam->save($this->request->data['StaffForExams'])) {
                        $issave = true;
                    }
                }
                if ($issave == true) {
                    $this->Session->setFlash(
                        '<span></span>' . __('The Invigilators from other college has been saved.'),
                        'default',
                        array('class' => 'success-box success-message')
                    );
                    return $this->redirect(array('action' => 'index'));
                } else {
                    $this->Session->setFlash(
                        '<span></span>' . __(
                            'The Invigilators from other college could not be saved. Please, try again.'
                        ),
                        'default',
                        array('class' => 'error-box error-message')
                    );
                }
            } else {
                $this->Session->setFlash(
                    '<span></span>' . __('Please check at least 1 Invigilator.'),
                    'default',
                    array('class' => 'error-box error-message')
                );
            }
        }
        $colleges = $this->StaffForExam->College->find('list');
        $departments = null;
        $this->set(compact('colleges', 'departments'));
    }

    public function edit($id = null)
    {

        if (!$id && empty($this->request->data)) {
            $this->Session->setFlash(__('Invalid staff for exam'));
            return $this->redirect(array('action' => 'index'));
        }
        if (!empty($this->request->data)) {
            if ($this->StaffForExam->save($this->request->data)) {
                $this->Session->setFlash(__('The staff for exam has been saved'));
                return $this->redirect(array('action' => 'index'));
            } else {
                $this->Session->setFlash(__('The staff for exam could not be saved. Please, try again.'));
            }
        }
        if (empty($this->request->data)) {
            $this->request->data = $this->StaffForExam->read(null, $id);
        }
        $colleges = $this->StaffForExam->College->find('list');
        $staffs = $this->StaffForExam->Staff->find('list');
        $this->set(compact('colleges', 'staffs'));
    }

    public function delete($id = null)
    {

        if (!$id) {
            $this->Session->setFlash(
                '<span></span>' . __('Invalid id for staff for exam'),
                'default',
                array('class' => 'error-box error-message')
            );
            return $this->redirect(array('action' => 'index'));
        }
        if ($this->StaffForExam->delete($id)) {
            $this->Session->setFlash(
                '<span></span>' . __('Staff for exam deleted'),
                'default',
                array('class' => 'success-box success-message')
            );
            return $this->redirect(array('action' => 'index'));
        }
        $this->Session->setFlash(
            '<span></span>' . __('Staff for exam was not deleted'),
            'default',
            array('class' => 'error-box error-message')
        );
        return $this->redirect(array('action' => 'index'));
    }

    public function get_departments($college_id = null)
    {

        if (!empty($college_id)) {
            $this->layout = 'ajax';
            $departments = $this->StaffForExam->Staff->Department->find(
                'list',
                array('conditions' => array('Department.college_id' => $college_id))
            );

            $this->set(compact('departments'));
        }
    }

    public function get_instructors($data = null)
    {

        $explode_data = explode("~", $data);
        $department_id = $explode_data[0];
        $academicyear = $explode_data[1];
        $academicyear = str_replace('-', '/', $academicyear);
        $semester = $explode_data[2];
        if (!empty($department_id)) {
            $already_recorded_staff_for_exams = $this->StaffForExam->find(
                'list',
                array(
                    'fields' => array('StaffForExam.staff_id'),
                    'conditions' => array(
                        'StaffForExam.college_id' => $this->college_id,
                        'StaffForExam.academic_year' => $academicyear,
                        'StaffForExam.semester' => $semester
                    )
                )
            );

            $instructors_list = $this->StaffForExam->Staff->find(
                'all',
                array(
                    'conditions' => array(
                        'Staff.department_id' => $department_id,
                        'Staff.active' => 1,
                        "NOT" => array('Staff.id' => $already_recorded_staff_for_exams)
                    ),
                    'fields' => array('Staff.id', 'Staff.full_name'),
                    'contain' => array(
                        'Title' => array('fields' => array('title')),
                        'Position' => array('fields' => array('position'))
                    )
                )
            );

            $this->set(compact('instructors_list'));
        }
    }
}
