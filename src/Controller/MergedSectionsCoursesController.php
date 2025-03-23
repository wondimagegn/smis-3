<?php

namespace App\Controller;

use App\Controller\AppController;


use Cake\Event\Event;
use Cake\ORM\TableRegistry;
use Cake\Core\Configure;
class MergedSectionsCoursesController extends AppController
{

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
        $this->paginate = [
            'contain' => ['PublishedCourses'],
        ];
        $mergedSectionsCourses = $this->paginate($this->MergedSectionsCourses);

        $this->set(compact('mergedSectionsCourses'));
    }


    public function view($id = null)
    {
        $mergedSectionsCourse = $this->MergedSectionsCourses->get($id, [
            'contain' => ['PublishedCourses', 'Sections'],
        ]);

        $this->set('mergedSectionsCourse', $mergedSectionsCourse);
    }

    public function add()
    {
        $mergedSectionsCourse = $this->MergedSectionsCourses->newEntity();
        if ($this->request->is('post')) {
            $mergedSectionsCourse = $this->MergedSectionsCourses->patchEntity($mergedSectionsCourse, $this->request->getData());
            if ($this->MergedSectionsCourses->save($mergedSectionsCourse)) {
                $this->Flash->success(__('The merged sections course has been saved.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The merged sections course could not be saved. Please, try again.'));
        }
        $this->set(compact('mergedSectionsCourse'));
    }


    public function edit($id = null)
    {
        $mergedSectionsCourse = $this->MergedSectionsCourses->get($id, [
            'contain' => ['Sections'],
        ]);
        if ($this->request->is(['patch', 'post', 'put'])) {
            $mergedSectionsCourse = $this->MergedSectionsCourses->patchEntity($mergedSectionsCourse, $this->request->getData());
            if ($this->MergedSectionsCourses->save($mergedSectionsCourse)) {
                $this->Flash->success(__('The merged sections course has been saved.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The merged sections course could not be saved. Please, try again.'));
        }

        $this->set(compact('mergedSectionsCourse'));
    }

    public function delete($id = null)
    {
        $this->request->allowMethod(['post', 'delete']);
        $mergedSectionsCourse = $this->MergedSectionsCourses->get($id);
        if ($this->MergedSectionsCourses->delete($mergedSectionsCourse)) {
            $this->Flash->success(__('The merged sections course has been deleted.'));
        } else {
            $this->Flash->error(__('The merged sections course could not be deleted. Please, try again.'));
        }

        return $this->redirect(['action' => 'index']);
    }
}
