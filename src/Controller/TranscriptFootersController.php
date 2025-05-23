<?php

namespace App\Controller;

use App\Controller\AppController;

use Cake\Event\Event;
use Cake\ORM\TableRegistry;
use Cake\Core\Configure;

class TranscriptFootersController extends AppController
{

    public $name = 'TranscriptFooters';
    public $components = array('AcademicYear');
    public $menuOptions = array(
        'parent' => 'certificates',
        'exclude' => array('edit', 'delete', 'view'),
        'alias' => array(
            'index' => 'View Footer',
            'add' => 'New Transcript Footer'
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

        $this->TranscriptFooter->recursive = 0;
        $this->set('transcriptFooters', $this->paginate());
    }

    public function beforeRender(Event $event)
    {

        $acyear_array_data = $this->AcademicYear->acyearArray();
        $defaultacademicyear = $this->AcademicYear->currentAcademicYear();
        $this->set(compact('acyear_array_data', 'defaultacademicyear'));
    }

    public function view($id = null)
    {

        if (!$id) {
            $this->Session->setFlash(
                '<span></span>' . __('Invalid transcript footer'),
                'default',
                array('class' => 'error-message error-box')
            );
            return $this->redirect(array('action' => 'index'));
        }
        $this->set('transcriptFooter', $this->TranscriptFooter->read(null, $id));
    }

    public function add()
    {

        if (!empty($this->request->data)) {
            $this->TranscriptFooter->create();
            if ($this->TranscriptFooter->save($this->request->data)) {
                $this->Session->setFlash(
                    '<span></span>' . __('The transcript footer has been saved'),
                    'default',
                    array('class' => 'success-message success-box')
                );
                return $this->redirect(array('action' => 'index'));
            } else {
                $this->Session->setFlash(
                    '<span></span>' . __('The transcript footer could not be saved. Please, try again.'),
                    'default',
                    array('class' => 'error-message error-box')
                );
            }
        }
        $programs = $this->TranscriptFooter->Program->find('list');
        $this->set(compact('programs'));
    }

    public function edit($id = null)
    {

        if (!$id && empty($this->request->data)) {
            $this->Session->setFlash(
                '<span></span>' . __('Invalid transcript footer'),
                'default',
                array('class' => 'error-message error-box')
            );
            return $this->redirect(array('action' => 'index'));
        }
        if (!empty($this->request->data)) {
            $this->request->data['TranscriptFooter']['academic_year'] = substr(
                $this->request->data['TranscriptFooter']['academic_year'],
                0,
                4
            );
            //debug($this->request->data);exit();
            if ($this->TranscriptFooter->save($this->request->data)) {
                $this->Session->setFlash(
                    '<span></span>' . __('The transcript footer has been saved'),
                    'default',
                    array('class' => 'success-message success-box')
                );
                return $this->redirect(array('action' => 'index'));
            } else {
                $this->Session->setFlash(
                    '<span></span>' . __('The transcript footer could not be saved. Please, try again.'),
                    'default',
                    array('class' => 'error-message error-box')
                );
            }
        }
        if (empty($this->request->data)) {
            $this->request->data = $this->TranscriptFooter->read(null, $id);
        }
        $programs = $this->TranscriptFooter->Program->find('list');
        $this->set(compact('programs'));
    }

    public function delete($id = null)
    {

        if (!$id) {
            $this->Session->setFlash(
                '<span></span>' . __('Invalid id for transcript footer'),
                'default',
                array('class' => 'error-message error-box')
            );
            return $this->redirect(array('action' => 'index'));
        }
        if ($this->TranscriptFooter->delete($id)) {
            $this->Session->setFlash(
                '<span></span>' . __('Transcript footer deleted'),
                'default',
                array('class' => 'success-message success-box')
            );
            return $this->redirect(array('action' => 'index'));
        }
        $this->Session->setFlash(
            '<span></span>' . __('Transcript footer was not deleted'),
            'default',
            array('class' => 'error-message error-box')
        );
        return $this->redirect(array('action' => 'index'));
    }
}
