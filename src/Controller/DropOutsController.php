<?php

namespace App\Controller;

use Cake\Event\Event;


class DropOutsController extends AppController
{

    public $name = 'DropOuts';
    public $menuOptions = array(
        //'parent' => 'courseRegistrations',
        'parent' => 'registrations',
        'alias' => array(
            'index' => 'View Drop Outs',
            'add' => 'Add drop out student',

        )

    );

    public function initialize()
    {

        parent::initialize();
        $this->loadComponent('AcademicYear');
        $this->loadComponent('Paginator');
    }

    public function beforeRender(Event $event)
    {

        parent::beforeRender($event);
        $acyear_array_data = $this->AcademicYear->acyearArray();
        //To diplay current academic year as default in drop down list
        $defaultacademicyear = $this->AcademicYear->currentAcademicYear();
        foreach ($acyear_array_data as $k => $v) {
            if ($v == $defaultacademicyear) {
                $defaultacademicyear = $k;
                break;
            }
        }
        $this->set(compact('acyear_array_data', 'defaultacademicyear'));
        unset($this->request->data['User']['password']);
    }

    public function index()
    {
    }

    public function view($id = null)
    {

        if (!$id) {
            $this->Session->setFlash(__('Invalid equivalent course'));
            $this->redirect(array('action' => 'index'));
        }
        $this->set('equivalentCourse', $this->EquivalentCourse->read(null, $id));
    }

    public function add()
    {

        $programs = $this->DropOut->Student->Program->find('list');
        $program_types = $this->DropOut->Student->ProgramType->find('list');
        $departments = $this->DropOut->Student->Department->allDepartmentsByCollege2(
            0,
            $this->department_ids,
            $this->college_ids
        );

        $department_combo_id = null;

        $default_department_id = null;
        $default_program_id = null;
        $default_program_type_id = null;
        //When any of the button is clicked (List students )
        if (isset($this->request->data) && !empty($this->request->data['DropOut'])) {
            if (!empty($this->department_ids)) {
                $students_for_readmission_list = $this->DropOut->getListOfStudentsForReadmission(
                    1,
                    $this->request->data['DropOut']['program_id'],
                    $this->request->data['DropOut']['program_type_id'],
                    $this->request->data['DropOut']['department_id'],
                    $this->request->data['DropOut']['academic_year'],
                    $this->request->data['DropOut']['semester'],
                    $this->request->data['DropOut']['name']
                );
            } else {
                if (!empty($this->college_ids)) {
                    $extracted_college = explode('~', $this->request->data['DropOut']['department_id']);

                    $students_for_readmission_list = $this->Readmission->getListOfStudentsForReadmission(
                        0,
                        $this->request->data['DropOut']['program_id'],
                        $this->request->data['DropOut']['program_type_id'],
                        $extracted_college[1],
                        $this->request->data['DropOut']['academic_year'],
                        $this->request->data['DropOut']['semester'],
                        $this->request->data['DropOut']['name']
                    );
                }
            }

            $default_department_id = $this->request->data['DropOut']['department_id'];
            $default_program_id = $this->request->data['DropOut']['program_id'];
            $default_program_type_id = $this->request->data['DropOut']['program_type_id'];
        } else {
            if (!empty($department_id) && !empty($program_id)) {
                $students_for_readmission_list = $this->DropOut->getListOfStudentsForReadmission(
                    $program_id,
                    $program_type_id,
                    $department_id
                );
                $default_department_id = $department_id;
                $default_program_id = $program_id;
                $default_program_type_id = $program_type_id;
            }
        }

        if (isset($this->request->data) && isset($this->request->data['addStudentToReadmissionList'])) {
            $readmission_list = array();
            foreach ($this->request->data['Student'] as $key => $student) {
                if ($student['include_readmission'] == 1) {
                    $sl_count = $this->Readmission->find('count', array(
                        'conditions' =>
                            array(
                                'Readmission.student_id' => $student['id'],
                                'Readmission.semester' => $this->request->data['Readmission']['semester'],
                                'Readmission.academic_year' => $this->request->data['Readmission']['academic_year'],
                            )
                    ));
                    if ($sl_count == 0) {
                        $sl_index = count($readmission_list);
                        $readmission_list[$sl_index]['student_id'] = $student['id'];
                        $readmission_list[$sl_index]['semester'] = $this->request->data['Readmission']['semester'];
                        $readmission_list[$sl_index]['academic_year'] = $this->request->data['Readmission']['academic_year'];
                    }
                }
            }
            if (empty($readmission_list)) {
                $this->Session->setFlash(
                    '<span></span>' . __(
                        'You are required to select at least
					one student to be included in the readmission list.',
                        true
                    ),
                    'default',
                    array('class' => 'error-box error-message')
                );
            } else {
                if ($this->Readmission->saveAll($readmission_list, array('validate' => false))) {
                    $this->Session->setFlash(
                        '<span></span>' . __(
                            count($readmission_list) .
                            ' students are included in the readmission list. After registrar filtering and
						academic commission approval,the student will be readmitted.',
                            true
                        ),
                        'default',
                        array('class' => 'success-box success-message')
                    );
                } else {
                    $this->Session->setFlash(
                        '<span></span>' . __(
                            'The system unable to include
						the selected students in the readmission list. Please try again.',
                            true
                        ),
                        'default',
                        array('class' => 'error-box error-message')
                    );
                }
            }
        }

        $this->set(
            compact(
                'programs',
                'program_types',
                'departments',
                'department_combo_id',
                'students_for_readmission_list',
                'default_department_id',
                'default_program_id',
                'default_program_type_id'
            )
        );
    }


    public function edit($id = null)
    {
    }

    public function delete($id = null)
    {
    }
}
