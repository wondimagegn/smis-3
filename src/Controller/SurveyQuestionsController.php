<?php

namespace App\Controller;

use App\Controller\AppController;

use Cake\Event\Event;
use Cake\ORM\TableRegistry;
use Cake\Core\Configure;

class SurveyQuestionsController extends AppController
{

    public $paginate = [];

    public function initialize()
    {

        parent::initialize();
        $this->loadComponent('Paginator'); // Ensure Paginator is loaded

    }

    public function beforeFilter(Event $event)
    {

        parent::beforeFilter($event);
        $this->Auth->Allow('add', 'edit', 'index');
    }

    public function index()
    {

        $this->SurveyQuestion->recursive = 0;
        $this->set('surveyQuestions', $this->Paginator->paginate());
    }

    public function view($id = null)
    {

        if (!$this->SurveyQuestion->exists($id)) {
            throw new NotFoundException(__('Invalid survey question'));
        }
        $options = array('conditions' => array('SurveyQuestion.' . $this->SurveyQuestion->primaryKey => $id));
        $this->set('surveyQuestion', $this->SurveyQuestion->find('first', $options));
    }

    public function add()
    {

        if ($this->request->is('post')) {
            $this->SurveyQuestion->create();

            $this->request->data = $this->SurveyQuestion->unsetdata($this->request->data);

            if (isset($this->request->data['SurveyQuestionAnswer']) && !empty($this->request->data['SurveyQuestionAnswer'])) {
                $saved = $this->SurveyQuestion->saveAll($this->request->data);
            } else {
                $saved = $this->SurveyQuestion->save($this->request->data);
            }

            if ($saved) {
                $this->Session->setFlash(
                    '<span></span>' . __('The survey question has been saved.'),
                    'default',
                    array('class' => 'success-box success-message')
                );

                return $this->redirect(array('action' => 'index'));
            } else {
                $this->Session->setFlash(
                    '<span></span>' . __('The survey question could not be saved. Please, try again.'),
                    'default',
                    array('class' => 'error-box error-message')
                );
            }
        }
    }

    public function edit($id = null)
    {

        if (!$this->SurveyQuestion->exists($id)) {
            throw new NotFoundException(__('Invalid survey question'));
        }
        if ($this->request->is(array('post', 'put'))) {
            /*
            if ($this->SurveyQuestion->save($this->request->data)) {

                $this->Session->setFlash('<span></span>'.__('The survey question has been saved.'),'default',array('class'=>'success-box success-message'));

                return $this->redirect(array('action' => 'index'));
            } else {

                    $this->Session->setFlash('<span></span>'.__('The survey question could not be saved. Please, try again.'),'default',array('class'=>'error-box error-message'));

            }
            */

            $this->request->data = $this->SurveyQuestion->unsetdata($this->request->data);

            if (isset($this->request->data['SurveyQuestionAnswer']) && !empty($this->request->data['SurveyQuestionAnswer'])) {
                $saved = $this->SurveyQuestion->saveAll($this->request->data);
            } else {
                $saved = $this->SurveyQuestion->save($this->request->data);
            }

            if ($saved) {
                $this->Session->setFlash(
                    '<span></span>' . __('The survey question has been saved.'),
                    'default',
                    array('class' => 'success-box success-message')
                );

                return $this->redirect(array('action' => 'index'));
            } else {
                $this->Session->setFlash(
                    '<span></span>' . __('The survey question could not be saved. Please, try again.'),
                    'default',
                    array('class' => 'error-box error-message')
                );
            }
        } else {
            $options = array('conditions' => array('SurveyQuestion.' . $this->SurveyQuestion->primaryKey => $id));
            $this->request->data = $this->SurveyQuestion->find('first', $options);
        }
    }

    public function delete($id = null)
    {

        $this->SurveyQuestion->id = $id;
        if (!$this->SurveyQuestion->exists()) {
            throw new NotFoundException(__('Invalid survey question'));
        }
        $this->request->allowMethod('post', 'delete');
        if ($this->SurveyQuestion->delete()) {
            $this->Session->setFlash(
                '<span></span>' . __('The survey question has been deleted.'),
                'default',
                array('class' => 'success-box success-message')
            );
        } else {
            $this->Session->setFlash(
                '<span></span>' . __('The survey question could not be deleted. Please, try again.'),
                'default',
                array('class' => 'error-box error-message')
            );
        }
        return $this->redirect(array('action' => 'index'));
    }
}
