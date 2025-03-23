<?php

namespace App\Controller;

use Cake\Event\Event;

class InstructorEvalutionQuestionsController extends AppController
{

    public $menuOptions = array(

        'parent' => 'evalution',

        'alias' => array(
            'add' => 'Add Evalution Question',
            'index' => 'View Evalution Questions'
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

    /**
     * index method
     *
     * @return void
     */
    public function index()
    {

        $options = array();
        $this->InstructorEvalutionQuestion->recursive = 0;

        if (!empty($this->request->data['InstructorEvalutionQuestion']['for'])) {
            $options['conditions']['InstructorEvalutionQuestion.for'] = $this->request->data['InstructorEvalutionQuestion']['for'];
        }
        if (!empty($this->request->data['InstructorEvalutionQuestion']['type'])) {
            $options['conditions']['InstructorEvalutionQuestion.type'] = $this->request->data['InstructorEvalutionQuestion']['type'];
        }
        if (!empty($this->request->data['search'])) {
            debug($options);
            if (!empty($options)) {
                $this->paginate['conditions'] = $options['conditions'];
                $this->Paginator->settings = $this->paginate;
                $instructorEvalutionQuestions = $this->Paginator->paginate();
            }
        } else {
            $instructorEvalutionQuestions = $this->Paginator->paginate();
        }
        $this->set('instructorEvalutionQuestions', $instructorEvalutionQuestions);
    }

    /**
     * view method
     *
     * @param string $id
     * @return void
     * @throws NotFoundException
     */
    public function view($id = null)
    {

        if (!$this->InstructorEvalutionQuestion->exists($id)) {
            throw new NotFoundException(__('Invalid instructor evalution question'));
        }
        $options = array('conditions' => array('InstructorEvalutionQuestion.' . $this->InstructorEvalutionQuestion->primaryKey => $id));
        $this->set('instructorEvalutionQuestion', $this->InstructorEvalutionQuestion->find('first', $options));
    }

/**
 * add method
 *
 * @return void
 */
    public function add()
    {

        if ($this->request->is('post')) {
            $this->InstructorEvalutionQuestion->create();
            if ($this->InstructorEvalutionQuestion->save($this->request->data)) {
                $this->Session->setFlash(
                    '<span></span>' . __('The instructor evalution question has been saved.'),
                    'default',
                    array('class' => 'success-box success-message')
                );

                return $this->redirect(array('action' => 'index'));
            } else {
                $this->Session->setFlash(
                    '<span></span>' . __('The instructor evalution question could not be saved. Please, try again.'),
                    'default',
                    array('class' => 'error-box error-message')
                );
            }
        }
    }

    /**
     * edit method
     *
     * @throws NotFoundException
     * @param string $id
     * @return void
     */
    public function edit($id = null)
    {

        if (!$this->InstructorEvalutionQuestion->exists($id)) {
            throw new NotFoundException(__('Invalid instructor evalution question'));
        }
        if ($this->request->is(array('post', 'put'))) {
            if ($this->InstructorEvalutionQuestion->save($this->request->data)) {
                $this->Session->setFlash(
                    '<span></span>' . __('The instructor evalution question has been saved.'),
                    'default',
                    array('class' => 'success-box success-message')
                );

                return $this->redirect(array('action' => 'index'));
            } else {
                $this->Session->setFlash(
                    '<span></span>' . __('The instructor evalution question could not be saved. Please, try again.'),
                    'default',
                    array('class' => 'error-box error-message')
                );
            }
        } else {
            $options = array('conditions' => array('InstructorEvalutionQuestion.' . $this->InstructorEvalutionQuestion->primaryKey => $id));
            $this->request->data = $this->InstructorEvalutionQuestion->find('first', $options);
        }
    }

    /**
     * delete method
     *
     * @param string $id
     * @return void
     * @throws NotFoundException
     */
    public function delete($id = null)
    {

        $this->InstructorEvalutionQuestion->id = $id;
        if (!$this->InstructorEvalutionQuestion->exists()) {
            throw new NotFoundException(__('Invalid instructor evalution question'));
        }
        $this->request->allowMethod('post', 'delete');
        if ($this->InstructorEvalutionQuestion->delete()) {
            $this->Session->setFlash(
                '<span></span>' . __('The instructor evalution question has been deleted.'),
                'default',
                array('class' => 'success-box success-message')
            );
        } else {
            $this->Session->setFlash(
                '<span></span>' . __('The instructor evalution question could not be deleted. Please, try again.'),
                'default',
                array('class' => 'error-box error-message')
            );
        }
        return $this->redirect(array('action' => 'index'));
    }
}
