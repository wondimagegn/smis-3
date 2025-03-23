<?php

namespace App\Controller;

use Cake\Core\Configure;
use Cake\Event\Event;


class GraduationLettersController extends AppController
{

    public $name = 'GraduationLetters';
    public $menuOptions = array(
        'parent' => 'graduation',
        'weight' => 4,
        'alias' => array(
            'index' => 'View Graduation Letter Templates',
            'add' => 'New Graduation Letter Template'
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

        $this->GraduationLetter->recursive = 0;
        $this->set('graduationLetters', $this->paginate());
    }

    public function view($id = null)
    {

        if (!$id) {
            $this->Session->setFlash(
                '<span></span>' . __('Invalid graduation letter'),
                'default',
                array('class' => 'error-box error-message')
            );
            return $this->redirect(array('action' => 'index'));
        }
        $this->set('graduationLetter', $this->GraduationLetter->read(null, $id));
    }

    public function add()
    {

        if (!empty($this->request->data)) {
            //$this->request->data['GraduationLetter']['academic_year'] = $this->request->data['GraduationLetter']['academic_year']['year'];
            $duplicated = $this->GraduationLetter->find(
                'count',
                array(
                    'conditions' =>
                        array(
                            'GraduationLetter.type' => $this->request->data['GraduationLetter']['type'],
                            'GraduationLetter.program_id' => $this->request->data['GraduationLetter']['program_id'],
                            'GraduationLetter.program_type_id' => $this->request->data['GraduationLetter']['program_type_id'],
                            'GraduationLetter.academic_year' => $this->request->data['GraduationLetter']['academic_year'],
                        )
                )
            );
            if ($duplicated > 0) {
                $this->Session->setFlash(
                    '<span></span>' . __(
                        'There is already a graduation letter template by the selected letter type, program, program type, and academic year. Please use edit to apply changes.'
                    ),
                    'default',
                    array('class' => 'error-box error-message')
                );
            } else {
                $this->GraduationLetter->create();
                if ($this->GraduationLetter->save($this->request->data)) {
                    $this->Session->setFlash(
                        '<span></span>' . __('The graduation letter has been saved'),
                        'default',
                        array('class' => 'success-box success-message')
                    );
                    return $this->redirect(array('action' => 'index'));
                } else {
                    $this->Session->setFlash(
                        '<span></span>' . __('The graduation letter could not be saved. Please, try again.'),
                        'default',
                        array('class' => 'error-box error-message')
                    );
                }
            }
        }
        $programs = $this->GraduationLetter->Program->find('list');
        $programTypes = $this->GraduationLetter->ProgramType->find('list');
        $acs = array();
        for ($i = date('Y') + 1; $i >= Configure::read('Calendar.universityEstablishement'); $i--) {
            $acs[$i] = $i;
        }

        $departments = $this->GraduationLetter->Program->Student->Department->allDepartmentsByCollege2(
            1,
            $this->department_ids,
            $this->college_ids
        );
        $departments = array(0 => 'All University') + $departments;

        $default_department_id = null;
        $this->set(compact('programs', 'programTypes', 'acs', 'departments', 'default_department_id'));
    }

    public function edit($id = null)
    {

        if (!$id && empty($this->request->data)) {
            $this->Session->setFlash(
                '<span></span>' . __('Invalid graduation letter'),
                'default',
                array('class' => 'error-box error-message')
            );
            return $this->redirect(array('action' => 'index'));
        }
        if (!empty($this->request->data)) {
            //$this->request->data['GraduationLetter']['academic_year'] = $this->request->data['GraduationLetter']['academic_year']['year'];
            $duplicated = $this->GraduationLetter->find(
                'count',
                array(
                    'conditions' =>
                        array(
                            'GraduationLetter.id <> ' => $this->request->data['GraduationLetter']['id'],
                            'GraduationLetter.type' => $this->request->data['GraduationLetter']['type'],
                            'GraduationLetter.program_id' => $this->request->data['GraduationLetter']['program_id'],
                            'GraduationLetter.program_type_id' => $this->request->data['GraduationLetter']['program_type_id'],
                            'GraduationLetter.academic_year' => $this->request->data['GraduationLetter']['academic_year'],
                        )
                )
            );
            if ($duplicated > 0) {
                $this->Session->setFlash(
                    '<span></span>' . __(
                        'There is already a graduation letter template by the selected letter type, program, program type, and academic year. Please use edit to apply changes.'
                    ),
                    'default',
                    array('class' => 'error-box error-message')
                );
            } else {
                if ($this->GraduationLetter->save($this->request->data)) {
                    $this->Session->setFlash(
                        '<span></span>' . __('The graduation letter has been saved'),
                        'default',
                        array('class' => 'success-box success-message')
                    );
                    return $this->redirect(array('action' => 'index'));
                } else {
                    $this->Session->setFlash(
                        '<span></span>' . __('The graduation letter could not be saved. Please, try again.'),
                        'default',
                        array('class' => 'error-box error-message')
                    );
                    //debug($this->GraduationLetter->invalidFields());
                }
            }
        }
        if (empty($this->request->data)) {
            $this->request->data = $this->GraduationLetter->read(null, $id);
        }
        $programs = $this->GraduationLetter->Program->find('list');
        $programTypes = $this->GraduationLetter->ProgramType->find('list');
        $acs = array();
        for ($i = date('Y') + 1; $i >= Configure::read('Calendar.universityEstablishement'); $i--) {
            $acs[$i] = $i;
        }

        $departments = $this->GraduationLetter->Program->Student->Department->allDepartmentsByCollege2(
            1,
            $this->department_ids,
            $this->college_ids
        );
        $departments = array(0 => 'All University') + $departments;

        $default_department_id = null;
        $this->set(compact('programs', 'programTypes', 'acs', 'departments', 'default_department_id'));
        $this->set(compact('programs', 'programTypes', 'acs'));
    }

    public function delete($id = null)
    {

        if (!$id) {
            $this->Session->setFlash(
                '<span></span>' . __('Invalid id for graduation letter'),
                'default',
                array('class' => 'error-box error-message')
            );
            return $this->redirect(array('action' => 'index'));
        }
        if ($this->GraduationLetter->delete($id)) {
            $this->Session->setFlash(
                '<span></span>' . __('Graduation letter deleted'),
                'default',
                array('class' => 'success-box success-message')
            );
            return $this->redirect(array('action' => 'index'));
        }
        $this->Session->setFlash(
            '<span></span>' . __('Graduation letter was not deleted'),
            'default',
            array('class' => 'error-box error-message')
        );
        return $this->redirect(array('action' => 'index'));
    }
}
