<?php

namespace App\Controller;

use App\Controller\AppController;

use Cake\Event\Event;
use Cake\ORM\TableRegistry;
use Cake\Core\Configure;

class InstructorEvalutionSettingsController extends AppController
{

    public $menuOptions = array(
        'parent' => 'evalution',
        'exclude' => array('index'),
        'alias' => array(
            'view_ss' => 'Instructor Evaluation Settings',
        )
    );

    public function initialize()
    {

        parent::initialize();
        $this->loadComponent('AcademicYear');
        $this->loadComponent('Paginator'); // Ensure Paginator is loaded


    }

    public function beforeFilter(Event $event)
    {

        parent::beforeFilter($event);
        //$this->Auth->Allow();
    }

    public function beforeRender($event)
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

        return $this->redirect(array('action' => 'view_ss'));
    }

    public function view_ss()
    {

        $this->set(
            'instructorEvalutionSetting',
            $this->InstructorEvalutionSetting->find(
                'first',
                array('order' => array('InstructorEvalutionSetting.academic_year' => 'DESC'))
            )
        );
    }

    public function edit()
    {

        if (!empty($this->request->data)) {
            if ($this->InstructorEvalutionSetting->save($this->request->data)) {
                $this->Flash->success(__('Instructor Evaluation setting has been updated.'));
                return $this->redirect(array('action' => 'view_ss'));
            } else {
                $this->Flash->error(__('Instructor evaluation setting could not be updated. Please, try again.'));
            }
        }

        if (empty($this->request->data)) {
            $this->request->data = $this->InstructorEvalutionSetting->find(
                'first',
                array('order' => array('InstructorEvalutionSetting.academic_year DESC'))
            );
        }
    }

}
