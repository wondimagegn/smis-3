<?php
namespace App\Controller;

use App\Controller\AppController;

class EquivalentCoursesController extends AppController
{

    public function index()
    {
        $this->paginate = [
            'contain' => ['CourseForSubstitueds', 'CourseBeSubstitueds'],
        ];
        $equivalentCourses = $this->paginate($this->EquivalentCourses);

        $this->set(compact('equivalentCourses'));
    }


    public function view($id = null)
    {
        $equivalentCourse = $this->EquivalentCourses->get($id, [
            'contain' => ['CourseForSubstitueds', 'CourseBeSubstitueds'],
        ]);

        $this->set('equivalentCourse', $equivalentCourse);
    }


    public function add()
    {
        $equivalentCourse = $this->EquivalentCourses->newEntity();
        if ($this->request->is('post')) {
            $equivalentCourse = $this->EquivalentCourses->patchEntity($equivalentCourse, $this->request->getData());
            if ($this->EquivalentCourses->save($equivalentCourse)) {
                $this->Flash->success(__('The equivalent course has been saved.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The equivalent course could not be saved. Please, try again.'));
        }
        $this->set(compact('equivalentCourse'));
    }


    public function edit($id = null)
    {
        $equivalentCourse = $this->EquivalentCourses->get($id, [
            'contain' => [],
        ]);
        if ($this->request->is(['patch', 'post', 'put'])) {
            $equivalentCourse = $this->EquivalentCourses->patchEntity($equivalentCourse, $this->request->getData());
            if ($this->EquivalentCourses->save($equivalentCourse)) {
                $this->Flash->success(__('The equivalent course has been saved.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The equivalent course could not be saved. Please, try again.'));
        }
          $this->set(compact('equivalentCourse'));
    }

    public function delete($id = null)
    {
        $this->request->allowMethod(['post', 'delete']);
        $equivalentCourse = $this->EquivalentCourses->get($id);
        if ($this->EquivalentCourses->delete($equivalentCourse)) {
            $this->Flash->success(__('The equivalent course has been deleted.'));
        } else {
            $this->Flash->error(__('The equivalent course could not be deleted. Please, try again.'));
        }

        return $this->redirect(['action' => 'index']);
    }
}
