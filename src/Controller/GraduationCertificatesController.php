<?php

namespace App\Controller;

use App\Controller\AppController;


use Cake\Event\Event;
use Cake\ORM\TableRegistry;
use Cake\Core\Configure;

class GraduationCertificatesController extends AppController
{

    public $name = 'GraduationCertificates';
    public $menuOptions = array(
        'parent' => 'graduation',
        'weight' => 5,
        'alias' => array(
            'index' => 'View Graduation Certificate Templates',
            'add' => 'Add Graduation Certificate Template'
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

        $this->GraduationCertificate->recursive = 0;
        $this->set('graduationCertificates', $this->paginate());
        $departments = $this->GraduationCertificate->Program->Student->Department->allDepartmentsByCollege2(
            1,
            $this->department_ids,
            $this->college_ids
        );
        $departments = array(0 => 'All University') + $departments;
        $this->set(compact('departments'));
    }

    public function view($id = null)
    {

        if (!$id) {
            $this->Flash->error('Invalid graduation certificate');
            return $this->redirect(array('action' => 'index'));
        }
        $this->set('graduationCertificate', $this->GraduationCertificate->read(null, $id));
    }

    public function add()
    {

        if (!empty($this->request->data)) {
            $duplicated = $this->GraduationCertificate->find('count', array(
                'conditions' => array(
                    'GraduationCertificate.program_id' => $this->request->data['GraduationCertificate']['program_id'],
                    'GraduationCertificate.program_type_id' => $this->request->data['GraduationCertificate']['program_type_id'],
                    'GraduationCertificate.academic_year' => $this->request->data['GraduationCertificate']['academic_year'],
                    'GraduationCertificate.department' => $this->request->data['GraduationCertificate']['department']
                )
            ));

            if ($duplicated > 0) {
                $this->Flash->error(
                    'There is already a graduation certificate template by the selected program, program type, and academic year. Please use edit to apply changes.'
                );
            } else {
                $this->GraduationCertificate->create();
                if ($this->GraduationCertificate->save($this->request->data)) {
                    $this->Flash->success('The graduation certificate has been saved');
                    return $this->redirect(array('action' => 'index'));
                } else {
                    $this->Flash->error('The graduation certificate could not be saved. Please, try again.');
                }
            }
        }

        $programs = $this->GraduationCertificate->Program->find('list');
        $programTypes = $this->GraduationCertificate->ProgramType->find('list');
        $acs = array();

        for ($i = date('Y') + 1; $i >= Configure::read('Calendar.universityEstablishement'); $i--) {
            $acs[$i] = $i;
        }

        $departments = $this->GraduationCertificate->Program->Student->Department->allDepartmentsByCollege2(
            1,
            $this->department_ids,
            $this->college_ids
        );
        $departments = array(0 => 'All University') + $departments;
        $default_department_id = null;
        $this->set(compact('programs', 'departments', 'programTypes', 'acs', 'default_department_id', 'departments'));
    }

    public function edit($id = null)
    {

        if (!$id && empty($this->request->data)) {
            $this->Flash->error('Invalid graduation certificate');
            return $this->redirect(array('action' => 'index'));
        }
        if (!empty($this->request->data)) {
            $duplicated = $this->GraduationCertificate->find('count', array(
                'conditions' => array(
                    'GraduationCertificate.id <> ' => $this->request->data['GraduationCertificate']['id'],
                    'GraduationCertificate.program_id' => $this->request->data['GraduationCertificate']['program_id'],
                    'GraduationCertificate.program_type_id' => $this->request->data['GraduationCertificate']['program_type_id'],
                    'GraduationCertificate.academic_year' => $this->request->data['GraduationCertificate']['academic_year'],
                    'GraduationCertificate.department' => $this->request->data['GraduationCertificate']['department']
                )
            ));

            if ($duplicated > 0) {
                $this->Flash->error(
                    'There is already a graduation certificate template by the selected program, program type, and academic year. Please use edit to apply changes.'
                );
            } else {
                if ($this->GraduationCertificate->save($this->request->data)) {
                    $this->Flash->success('The graduation certificate has been saved');
                    return $this->redirect(array('action' => 'index'));
                } else {
                    $this->Flash->error('The graduation certificate could not be saved. Please, try again.');
                }
            }
        }

        if (empty($this->request->data)) {
            $this->request->data = $this->GraduationCertificate->read(null, $id);
        }

        $programs = $this->GraduationCertificate->Program->find('list');
        $programTypes = $this->GraduationCertificate->ProgramType->find('list');
        $acs = array();

        for ($i = date('Y') + 1; $i >= Configure::read('Calendar.universityEstablishement'); $i--) {
            $acs[$i] = $i;
        }

        $departments = $this->GraduationCertificate->Program->Student->Department->allDepartmentsByCollege2(
            1,
            $this->department_ids,
            $this->college_ids
        );
        $departments = array(0 => 'All University') + $departments;
        $default_department_id = null;
        $this->set(compact('programs', 'departments', 'programTypes', 'acs', 'default_department_id', 'departments'));
    }

    public function delete($id = null)
    {

        if (!$id) {
            $this->Flash->error('Invalid id for graduation certificate');
            return $this->redirect(array('action' => 'index'));
        }
        if ($this->GraduationCertificate->delete($id)) {
            $this->Flash->success('Graduation certificate deleted');
            return $this->redirect(array('action' => 'index'));
        }
        $this->Flash->error('Graduation certificate was not deleted');
        return $this->redirect(array('action' => 'index'));
    }
}
