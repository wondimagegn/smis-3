<?php
namespace App\Controller;

use App\Controller\AppController;

class ExcludedPublishedCourseExamsController extends AppController
{

    public function index()
    {
        $this->paginate = [
            'contain' => ['PublishedCourses'],
        ];
        $excludedPublishedCourseExams = $this->paginate($this->ExcludedPublishedCourseExams);

        $this->set(compact('excludedPublishedCourseExams'));
    }


    public function view($id = null)
    {
        $excludedPublishedCourseExam = $this->ExcludedPublishedCourseExams->get($id, [
            'contain' => ['PublishedCourses'],
        ]);

        $this->set('excludedPublishedCourseExam', $excludedPublishedCourseExam);
    }

    public function add()
    {
        $excludedPublishedCourseExam = $this->ExcludedPublishedCourseExams->newEntity();
        if ($this->request->is('post')) {
            $excludedPublishedCourseExam = $this->ExcludedPublishedCourseExams->patchEntity($excludedPublishedCourseExam, $this->request->getData());
            if ($this->ExcludedPublishedCourseExams->save($excludedPublishedCourseExam)) {
                $this->Flash->success(__('The excluded published course exam has been saved.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The excluded published course exam could not be saved. Please, try again.'));
        }
       $this->set(compact('excludedPublishedCourseExam'));
    }


    public function edit($id = null)
    {
        $excludedPublishedCourseExam = $this->ExcludedPublishedCourseExams->get($id, [
            'contain' => [],
        ]);
        if ($this->request->is(['patch', 'post', 'put'])) {
            $excludedPublishedCourseExam = $this->ExcludedPublishedCourseExams->patchEntity($excludedPublishedCourseExam, $this->request->getData());
            if ($this->ExcludedPublishedCourseExams->save($excludedPublishedCourseExam)) {
                $this->Flash->success(__('The excluded published course exam has been saved.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The excluded published course exam could not be saved. Please, try again.'));
        }
        $this->set(compact('excludedPublishedCourseExam'));
    }


    public function delete($id = null)
    {
        $this->request->allowMethod(['post', 'delete']);
        $excludedPublishedCourseExam = $this->ExcludedPublishedCourseExams->get($id);
        if ($this->ExcludedPublishedCourseExams->delete($excludedPublishedCourseExam)) {
            $this->Flash->success(__('The excluded published course exam has been deleted.'));
        } else {
            $this->Flash->error(__('The excluded published course exam could not be deleted. Please, try again.'));
        }

        return $this->redirect(['action' => 'index']);
    }
}
