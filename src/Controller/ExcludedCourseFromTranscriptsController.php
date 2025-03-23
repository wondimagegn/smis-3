<?php

namespace App\Controller;

use App\Controller\AppController;

use Cake\Event\Event;
use Cake\ORM\TableRegistry;
use Cake\Core\Configure;

class ExcludedCourseFromTranscriptsController extends AppController
{

    public $name = 'ExcludedCourseFromTranscripts';
    public $menuOptions = array(
        'parent' => 'certificates',
        'exclude' => array('edit', 'delete', 'view'),
        'alias' => array(
            'index' => 'View Excluded Courses',
            'add' => 'Add Excluded Course'
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

        $this->ExcludedCourseFromTranscript->recursive = 0;
        $this->set('excludedCourseFromTranscripts', $this->paginate());
    }

    public function view($id = null)
    {

        if (!$id) {
            $this->Session->setFlash(__('Invalid excluded course from transcript'));
            return $this->redirect(array('action' => 'index'));
        }
        $this->set('excludedCourseFromTranscript', $this->ExcludedCourseFromTranscript->read(null, $id));
    }

    public function add()
    {

        if (!empty($this->request->data)) {
            $this->ExcludedCourseFromTranscript->create();
            if ($this->ExcludedCourseFromTranscript->save($this->request->data)) {
                $this->Session->setFlash(__('The excluded course from transcript has been saved'));
                return $this->redirect(array('action' => 'index'));
            } else {
                $this->Session->setFlash(
                    __('The excluded course from transcript could not be saved. Please, try again.')
                );
            }
        }
        $courseRegistrations = $this->ExcludedCourseFromTranscript->CourseRegistration->find('list');
        $courseExemptions = $this->ExcludedCourseFromTranscript->CourseExemption->find('list');
        $this->set(compact('courseRegistrations', 'courseExemptions'));
    }

    public function edit($id = null)
    {

        if (!$id && empty($this->request->data)) {
            $this->Session->setFlash(__('Invalid excluded course from transcript'));
            return $this->redirect(array('action' => 'index'));
        }
        if (!empty($this->request->data)) {
            if ($this->ExcludedCourseFromTranscript->save($this->request->data)) {
                $this->Session->setFlash(__('The excluded course from transcript has been saved'));
                return $this->redirect(array('action' => 'index'));
            } else {
                $this->Session->setFlash(
                    __('The excluded course from transcript could not be saved. Please, try again.')
                );
            }
        }
        if (empty($this->request->data)) {
            $this->request->data = $this->ExcludedCourseFromTranscript->read(null, $id);
        }
        $courseRegistrations = $this->ExcludedCourseFromTranscript->CourseRegistration->find('list');
        $courseExemptions = $this->ExcludedCourseFromTranscript->CourseExemption->find('list');
        $this->set(compact('courseRegistrations', 'courseExemptions'));
    }

    public function delete($id = null)
    {

        if (!$id) {
            $this->Session->setFlash(__('Invalid id for excluded course from transcript'));
            return $this->redirect(array('action' => 'index'));
        }
        if ($this->ExcludedCourseFromTranscript->delete($id)) {
            $this->Session->setFlash(__('Excluded course from transcript deleted'));
            return $this->redirect(array('action' => 'index'));
        }
        $this->Session->setFlash(__('Excluded course from transcript was not deleted'));
        return $this->redirect(array('action' => 'index'));
    }
}
