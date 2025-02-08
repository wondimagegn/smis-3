<?php
namespace App\Controller;

use App\Controller\AppController;

class ExamResultsController extends AppController
{

    public function index()
    {
        $this->paginate = [
            'contain' => ['ExamTypes', 'CourseRegistrations', 'CourseAdds', 'MakeupExams'],
        ];
        $examResults = $this->paginate($this->ExamResults);

        $this->set(compact('examResults'));
    }


    public function view($id = null)
    {
        $examResult = $this->ExamResults->get($id, [
            'contain' => ['ExamTypes', 'CourseRegistrations', 'CourseAdds', 'MakeupExams'],
        ]);

        $this->set('examResult', $examResult);
    }

    public function add()
    {
        $examResult = $this->ExamResults->newEntity();
        if ($this->request->is('post')) {
            $examResult = $this->ExamResults->patchEntity($examResult, $this->request->getData());
            if ($this->ExamResults->save($examResult)) {
                $this->Flash->success(__('The exam result has been saved.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The exam result could not be saved. Please, try again.'));
        }

        $this->set(compact('examResult'));
    }


    public function edit($id = null)
    {
        $examResult = $this->ExamResults->get($id, [
            'contain' => [],
        ]);
        if ($this->request->is(['patch', 'post', 'put'])) {
            $examResult = $this->ExamResults->patchEntity($examResult, $this->request->getData());
            if ($this->ExamResults->save($examResult)) {
                $this->Flash->success(__('The exam result has been saved.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The exam result could not be saved. Please, try again.'));
        }

        $this->set(compact('examResult'));
    }


    public function delete($id = null)
    {
        $this->request->allowMethod(['post', 'delete']);
        $examResult = $this->ExamResults->get($id);
        if ($this->ExamResults->delete($examResult)) {
            $this->Flash->success(__('The exam result has been deleted.'));
        } else {
            $this->Flash->error(__('The exam result could not be deleted. Please, try again.'));
        }

        return $this->redirect(['action' => 'index']);
    }
}
