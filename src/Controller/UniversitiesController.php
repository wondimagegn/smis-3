<?php

namespace App\Controller;

use Cake\Core\Configure;
use Cake\Event\Event;

class UniversitiesController extends AppController
{

    public $name = 'Universities';
    public $menuOptions = array(
        'parent' => 'mainDatas',
        'alias' => array(
            'index' => 'View name',
            'add' => 'Add Name',
        )
    );

    public $paginate = [];

    public function initialize()
    {

        parent::initialize();
        $this->loadComponent('AcademicYear');
        $this->loadComponent('Paginator'); // Ensure Paginator is loaded
        $this->viewBuilder()->setHelpers(['Media.Media']);
    }

    public function beforeFilter(Event $event)
    {

        parent::beforeFilter($event);
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

        $this->University->recursive = 0;
        $this->set('universities', $this->paginate());
    }

    public function view($id = null)
    {

        if (!$id) {
            $this->Session->setFlash(
                '<span></span>' . __('Invalid university'),
                'default',
                array('class' => 'error-box error-message')
            );
            return $this->redirect(array('action' => 'index'));
        }
        $this->set('university', $this->University->read(null, $id));
    }

    public function add()
    {

        if (!empty($this->request->data)) {
            $this->University->create();
            $this->request->data = $this->University->attach_temp_photo($this->request->data);
            if ($this->University->saveAll($this->request->data, array('validate' => 'first'))) {
                $this->Session->setFlash(
                    '<span></span>' . __('The university has been saved'),
                    'default',
                    array('class' => 'success-box success-message')
                );
                $this->redirect(array('action' => 'index'));
            } else {
                $this->Session->setFlash(
                    '<span></span>' . __('The university could not be saved. Please, try again.'),
                    'default',
                    array('class' => 'error-box error-message')
                );
            }
        }
        $years = array();
        for ($i = Configure::read('Calendar.universityEstablishement'); $i <= date('Y') + 1; $i++) {
            $years[$i] = $i;
        }
        $this->set(compact('years'));
    }

    public function edit($id = null)
    {

        if (!$id && empty($this->request->data)) {
            $this->Session->setFlash(
                '<span></span>' . __('Invalid university'),
                'default',
                array('class' => 'error-box error-message')
            );
            return $this->redirect(array('action' => 'index'));
        }
        if (!empty($this->request->data)) {
            $this->request->data = $this->University->attach_temp_photo($this->request->data);


            if ($this->University->saveAll($this->request->data, array('validate' => 'first'))) {
                $this->Session->setFlash(
                    '<span></span>' . __('The university has been saved'),
                    'default',
                    array('class' => 'success-box success-message')
                );
                return $this->redirect(array('action' => 'index'));
            } else {
                $this->Session->setFlash(
                    '<span></span>' . __('The university could not be saved. Please, try again.'),
                    'default',
                    array('class' => 'error-box error-message')
                );
            }
        }
        if (empty($this->request->data)) {
            $this->request->data = $this->University->read(null, $id);
        }
        $years = array();
        for ($i = Configure::read('Calendar.universityEstablishement'); $i <= date('Y') + 1; $i++) {
            $years[$i] = $i;
        }
        $this->set(compact('years'));
    }

    public function delete($id = null)
    {

        if (!$id) {
            $this->Session->setFlash(
                '<span></span>' . __('Invalid id for university'),
                'default',
                array('class' => 'error-box error-message')
            );
            return $this->redirect(array('action' => 'index'));
        }
        if ($this->University->delete($id)) {
            //if ($this->University->deleteAll(array('id' =>$id), false, true)) {
            $this->Session->setFlash(
                '<span></span>' . __('University deleted'),
                'default',
                array('class' => 'success-box success-message')
            );
            return $this->redirect(array('action' => 'index'));
        }
        $this->Session->setFlash(
            '<span></span>' . __('University was not deleted'),
            'default',
            array('class' => 'error-box error-message')
        );
        return $this->redirect(array('action' => 'index'));
    }
}
