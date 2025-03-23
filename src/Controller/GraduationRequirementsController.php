<?php

namespace App\Controller;

use App\Controller\AppController;

use Cake\Event\Event;
use Cake\ORM\TableRegistry;
use Cake\Core\Configure;

class GraduationRequirementsController extends AppController
{

    public $name = 'GraduationRequirements';
    public $components = array('AcademicYear');
    public $menuOptions = array(
        'parent' => 'graduateLists',
        'alias' => array(
            'index' => 'View Graduation Requirement',
            'add' => 'New Graduation Requirement'
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

        $this->GraduationRequirement->recursive = 0;
        $this->set('graduationRequirements', $this->paginate());
    }

    public function beforeRender(Event $event)
    {
        parent::beforeRender($event);

        $acyear_array_data = $this->AcademicYear->acyearArray();
        $defaultacademicyear = $this->AcademicYear->currentAcademicYear();
        $this->set(compact('acyear_array_data', 'defaultacademicyear'));
    }

    public function add()
    {

        if (!empty($this->request->data)) {
            $this->GraduationRequirement->create();
            if ($this->GraduationRequirement->save($this->request->data)) {
                $this->Session->setFlash(
                    '<span></span>' . __('The graduation requirement has been saved'),
                    'default',
                    array('class' => 'success-message success-box')
                );
                return $this->redirect(array('action' => 'index'));
            } else {
                $this->Session->setFlash(
                    '<span></span>' . __('The graduation requirement could not be saved. Please, try again.'),
                    'default',
                    array('class' => 'error-message error-box')
                );
            }
        }
        $programs = $this->GraduationRequirement->Program->find('list');
        $this->set(compact('programs'));
    }

    public function edit($id = null)
    {

        if (!$id && empty($this->request->data)) {
            $this->Session->setFlash(
                '<span></span>' . __('Invalid graduation requirement'),
                'default',
                array('class' => 'error-message error-box')
            );
            return $this->redirect(array('action' => 'index'));
        }
        if (!empty($this->request->data)) {
            if ($this->GraduationRequirement->save($this->request->data)) {
                $this->Session->setFlash(
                    '<span></span>' . __('The graduation requirement has been saved'),
                    'default',
                    array('class' => 'success-message success-box')
                );
                return $this->redirect(array('action' => 'index'));
            } else {
                $this->Session->setFlash(
                    '<span></span>' . __('The graduation requirement could not be saved. Please, try again.'),
                    'default',
                    array('class' => 'error-message error-box')
                );
            }
        }
        if (empty($this->request->data)) {
            $this->request->data = $this->GraduationRequirement->read(null, $id);
        }
        $programs = $this->GraduationRequirement->Program->find('list');
        $this->set(compact('programs'));
    }
}
