<?php

namespace App\Controller;

use App\Controller\AppController;

use Cake\Event\Event;
use Cake\ORM\TableRegistry;
use Cake\Core\Configure;

class UnschedulePublishedCoursesController extends AppController
{

    public $name = 'UnschedulePublishedCourses';
    public $menuOptions = array(
        'controllerButton' => false,
        'exclude' => '*'
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

        $this->UnschedulePublishedCourse->recursive = 0;
        $this->set('unschedulePublishedCourses', $this->paginate());
    }

    public function view($id = null)
    {

        if (!$id) {
            $this->Session->setFlash(__('Invalid unschedule published course'));
            return $this->redirect(array('action' => 'index'));
        }
        $this->set('unschedulePublishedCourse', $this->UnschedulePublishedCourse->read(null, $id));
    }

    public function add()
    {

        if (!empty($this->request->data)) {
            $this->UnschedulePublishedCourse->create();
            if ($this->UnschedulePublishedCourse->save($this->request->data)) {
                $this->Session->setFlash(__('The unschedule published course has been saved'));
                return $this->redirect(array('action' => 'index'));
            } else {
                $this->Session->setFlash(__('The unschedule published course could not be saved. Please, try again.'));
            }
        }
        $publishedCourses = $this->UnschedulePublishedCourse->PublishedCourse->find('list');
        $courseSplitSections = $this->UnschedulePublishedCourse->CourseSplitSection->find('list');
        $this->set(compact('publishedCourses', 'courseSplitSections'));
    }

    public function edit($id = null)
    {

        if (!$id && empty($this->request->data)) {
            $this->Session->setFlash(__('Invalid unschedule published course'));
            return $this->redirect(array('action' => 'index'));
        }
        if (!empty($this->request->data)) {
            if ($this->UnschedulePublishedCourse->save($this->request->data)) {
                $this->Session->setFlash(__('The unschedule published course has been saved'));
                return $this->redirect(array('action' => 'index'));
            } else {
                $this->Session->setFlash(__('The unschedule published course could not be saved. Please, try again.'));
            }
        }
        if (empty($this->request->data)) {
            $this->request->data = $this->UnschedulePublishedCourse->read(null, $id);
        }
        $publishedCourses = $this->UnschedulePublishedCourse->PublishedCourse->find('list');
        $courseSplitSections = $this->UnschedulePublishedCourse->CourseSplitSection->find('list');
        $this->set(compact('publishedCourses', 'courseSplitSections'));
    }

    public function delete($id = null)
    {

        if (!$id) {
            $this->Session->setFlash(__('Invalid id for unschedule published course'));
            return $this->redirect(array('action' => 'index'));
        }
        if ($this->UnschedulePublishedCourse->delete($id)) {
            $this->Session->setFlash(__('Unschedule published course deleted'));
            return $this->redirect(array('action' => 'index'));
        }
        $this->Session->setFlash(__('Unschedule published course was not deleted'));
        return $this->redirect(array('action' => 'index'));
    }
}
