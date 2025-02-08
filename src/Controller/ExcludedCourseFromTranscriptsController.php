<?php
namespace App\Controller;

use App\Controller\AppController;

class ExcludedCourseFromTranscriptsController extends AppController
{

    public function index()
    {
        $this->paginate = [
            'contain' => ['CourseRegistrations', 'CourseExemptions'],
        ];
        $excludedCourseFromTranscripts = $this->paginate($this->ExcludedCourseFromTranscripts);

        $this->set(compact('excludedCourseFromTranscripts'));
    }

    public function view($id = null)
    {
        $excludedCourseFromTranscript = $this->ExcludedCourseFromTranscripts->get($id, [
            'contain' => ['CourseRegistrations', 'CourseExemptions'],
        ]);

        $this->set('excludedCourseFromTranscript', $excludedCourseFromTranscript);
    }

    public function add()
    {
        $excludedCourseFromTranscript = $this->ExcludedCourseFromTranscripts->newEntity();
        if ($this->request->is('post')) {
            $excludedCourseFromTranscript = $this->ExcludedCourseFromTranscripts->patchEntity($excludedCourseFromTranscript, $this->request->getData());
            if ($this->ExcludedCourseFromTranscripts->save($excludedCourseFromTranscript)) {
                $this->Flash->success(__('The excluded course from transcript has been saved.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The excluded course from transcript could not be saved. Please, try again.'));
        }

        $this->set(compact('excludedCourseFromTranscript'));
    }


    public function edit($id = null)
    {
        $excludedCourseFromTranscript = $this->ExcludedCourseFromTranscripts->get($id, [
            'contain' => [],
        ]);
        if ($this->request->is(['patch', 'post', 'put'])) {
            $excludedCourseFromTranscript = $this->ExcludedCourseFromTranscripts->patchEntity($excludedCourseFromTranscript, $this->request->getData());
            if ($this->ExcludedCourseFromTranscripts->save($excludedCourseFromTranscript)) {
                $this->Flash->success(__('The excluded course from transcript has been saved.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The excluded course from transcript could not be saved. Please, try again.'));
        }
       $this->set(compact('excludedCourseFromTranscript'));
    }


    public function delete($id = null)
    {
        $this->request->allowMethod(['post', 'delete']);
        $excludedCourseFromTranscript = $this->ExcludedCourseFromTranscripts->get($id);
        if ($this->ExcludedCourseFromTranscripts->delete($excludedCourseFromTranscript)) {
            $this->Flash->success(__('The excluded course from transcript has been deleted.'));
        } else {
            $this->Flash->error(__('The excluded course from transcript could not be deleted. Please, try again.'));
        }

        return $this->redirect(['action' => 'index']);
    }
}
