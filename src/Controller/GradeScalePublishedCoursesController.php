<?php

namespace App\Controller;

use App\Controller\AppController;

use Cake\Event\Event;
use Cake\ORM\TableRegistry;
use Cake\Core\Configure;

class GradeScalePublishedCoursesController extends AppController
{

    public $name = 'GradeScalePublishedCourses';
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

        $this->GradeScalePublishedCourse->recursive = 0;
        $this->set('gradeScalePublishedCourses', $this->paginate());
    }

    public function view($id = null)
    {

        if (!$id) {
            $this->Session->setFlash(__('Invalid grade scale published course'));
            return $this->redirect(array('action' => 'index'));
        }
        $this->set('gradeScalePublishedCourse', $this->GradeScalePublishedCourse->read(null, $id));
    }

    public function add()
    {

        if (!empty($this->request->data)) {
            $this->GradeScalePublishedCourse->create();
            if ($this->GradeScalePublishedCourse->save($this->request->data)) {
                $this->Session->setFlash(__('The grade scale published course has been saved'));
                return $this->redirect(array('action' => 'index'));
            } else {
                $this->Session->setFlash(__('The grade scale published course could not be saved. Please, try again.'));
            }
        }
        $gradeScales = $this->GradeScalePublishedCourse->GradeScale->find('list');
        $publishedCourses = $this->GradeScalePublishedCourse->PublishedCourse->find('list');
        $this->set(compact('gradeScales', 'publishedCourses'));
    }

    public function edit($id = null)
    {

        if (!$id && empty($this->request->data)) {
            $this->Session->setFlash(__('Invalid grade scale published course'));
            return $this->redirect(array('action' => 'index'));
        }
        if (!empty($this->request->data)) {
            if ($this->GradeScalePublishedCourse->save($this->request->data)) {
                $this->Session->setFlash(__('The grade scale published course has been saved'));
                return $this->redirect(array('action' => 'index'));
            } else {
                $this->Session->setFlash(__('The grade scale published course could not be saved. Please, try again.'));
            }
        }
        if (empty($this->request->data)) {
            $this->request->data = $this->GradeScalePublishedCourse->read(null, $id);
        }
        $gradeScales = $this->GradeScalePublishedCourse->GradeScale->find('list');
        $publishedCourses = $this->GradeScalePublishedCourse->PublishedCourse->find('list');
        $this->set(compact('gradeScales', 'publishedCourses'));
    }

    public function delete($id = null)
    {

        if (!$id) {
            $this->Session->setFlash(__('Invalid id for grade scale published course'));
            return $this->redirect(array('action' => 'index'));
        }
        if ($this->GradeScalePublishedCourse->delete($id)) {
            $this->Session->setFlash(__('Grade scale published course deleted'));
            return $this->redirect(array('action' => 'index'));
        }
        $this->Session->setFlash(__('Grade scale published course was not deleted'));
        return $this->redirect(array('action' => 'index'));
    }
}
