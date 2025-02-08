<?php
namespace App\Controller;

use App\Controller\AppController;

class UnschedulePublishedCoursesController extends AppController
{

    public function index()
    {
        $this->paginate = [
            'contain' => ['PublishedCourses', 'CourseSplitSections'],
        ];
        $unschedulePublishedCourses = $this->paginate($this->UnschedulePublishedCourses);

        $this->set(compact('unschedulePublishedCourses'));
    }

    public function view($id = null)
    {
        $unschedulePublishedCourse = $this->UnschedulePublishedCourses->get($id, [
            'contain' => ['PublishedCourses', 'CourseSplitSections'],
        ]);

        $this->set('unschedulePublishedCourse', $unschedulePublishedCourse);
    }


    public function add()
    {
        $unschedulePublishedCourse = $this->UnschedulePublishedCourses->newEntity();
        if ($this->request->is('post')) {
            $unschedulePublishedCourse = $this->UnschedulePublishedCourses->patchEntity($unschedulePublishedCourse, $this->request->getData());
            if ($this->UnschedulePublishedCourses->save($unschedulePublishedCourse)) {
                $this->Flash->success(__('The unschedule published course has been saved.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The unschedule published course could not be saved. Please, try again.'));
        }
       $this->set(compact('unschedulePublishedCourse'));
    }

    public function edit($id = null)
    {
        $unschedulePublishedCourse = $this->UnschedulePublishedCourses->get($id, [
            'contain' => [],
        ]);
        if ($this->request->is(['patch', 'post', 'put'])) {
            $unschedulePublishedCourse = $this->UnschedulePublishedCourses->patchEntity($unschedulePublishedCourse, $this->request->getData());
            if ($this->UnschedulePublishedCourses->save($unschedulePublishedCourse)) {
                $this->Flash->success(__('The unschedule published course has been saved.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The unschedule published course could not be saved. Please, try again.'));
        }
        $this->set(compact('unschedulePublishedCourse'));
    }

    public function delete($id = null)
    {
        $this->request->allowMethod(['post', 'delete']);
        $unschedulePublishedCourse = $this->UnschedulePublishedCourses->get($id);
        if ($this->UnschedulePublishedCourses->delete($unschedulePublishedCourse)) {
            $this->Flash->success(__('The unschedule published course has been deleted.'));
        } else {
            $this->Flash->error(__('The unschedule published course could not be deleted. Please, try again.'));
        }

        return $this->redirect(['action' => 'index']);
    }
}
