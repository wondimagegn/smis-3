<?php

namespace App\Controller;

use App\Controller\AppController;

use Cake\Event\Event;
use Cake\ORM\TableRegistry;
use Cake\Core\Configure;

class CourseSplitSectionsController extends AppController
{

    public $name = 'CourseSplitSections';
    public $menuOptions = array(
        'controllerButton' => false,
        'exclude' => '*'
    );
    public $paginate = [];

    public function initialize()
    {

        parent::initialize();
        $this->loadComponent('Paginator'); // Ensure Paginator is loaded

    }

    public function beforeFilter(Event $event)
    {

        parent::beforeFilter($event);
    }

    public function index()
    {

        $this->CourseSplitSection->recursive = 0;
        $this->set('courseSplitSections', $this->paginate());
    }

    public function view($id = null)
    {

        if (!$id) {
            $this->Session->setFlash(__('Invalid course split section'));
            return $this->redirect(array('action' => 'index'));
        }
        $this->set('courseSplitSection', $this->CourseSplitSection->read(null, $id));
    }

    public function add()
    {

        if (!empty($this->request->data)) {
            $this->CourseSplitSection->create();
            if ($this->CourseSplitSection->save($this->request->data)) {
                $this->Session->setFlash(__('The course split section has been saved'));
                return $this->redirect(array('action' => 'index'));
            } else {
                $this->Session->setFlash(__('The course split section could not be saved. Please, try again.'));
            }
        }
        $sectionSplitForPublishedCourses = $this->CourseSplitSection->SectionSplitForPublishedCourse->find('list');
        $this->set(compact('sectionSplitForPublishedCourses'));
    }

    public function edit($id = null)
    {

        if (!$id && empty($this->request->data)) {
            $this->Session->setFlash(__('Invalid course split section'));
            return $this->redirect(array('action' => 'index'));
        }
        if (!empty($this->request->data)) {
            if ($this->CourseSplitSection->save($this->request->data)) {
                $this->Session->setFlash(__('The course split section has been saved'));
                return $this->redirect(array('action' => 'index'));
            } else {
                $this->Session->setFlash(__('The course split section could not be saved. Please, try again.'));
            }
        }
        if (empty($this->request->data)) {
            $this->request->data = $this->CourseSplitSection->read(null, $id);
        }
        $sectionSplitForPublishedCourses = $this->CourseSplitSection->SectionSplitForPublishedCourse->find('list');
        $this->set(compact('sectionSplitForPublishedCourses'));
    }

    public function delete($id = null)
    {

        if (!$id) {
            $this->Session->setFlash(__('Invalid id for course split section'));
            return $this->redirect(array('action' => 'index'));
        }
        if ($this->CourseSplitSection->delete($id)) {
            $this->Session->setFlash(__('Course split section deleted'));
            return $this->redirect(array('action' => 'index'));
        }
        $this->Session->setFlash(__('Course split section was not deleted'));
        return $this->redirect(array('action' => 'index'));
    }
}
