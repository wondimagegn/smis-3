<?php
namespace App\Controller;

use App\Controller\AppController;

class ExamTypesController extends AppController
{

    public function index()
    {
        $this->paginate = [
            'contain' => ['PublishedCourses', 'Sections'],
        ];
        $examTypes = $this->paginate($this->ExamTypes);

        $this->set(compact('examTypes'));
    }

    public function view($id = null)
    {
        $examType = $this->ExamTypes->get($id, [
            'contain' => ['PublishedCourses', 'Sections', 'ExamResults'],
        ]);

        $this->set('examType', $examType);
    }

    public function add()
    {
        $examType = $this->ExamTypes->newEntity();
        if ($this->request->is('post')) {
            $examType = $this->ExamTypes->patchEntity($examType, $this->request->getData());
            if ($this->ExamTypes->save($examType)) {
                $this->Flash->success(__('The exam type has been saved.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The exam type could not be saved. Please, try again.'));
        }

        $this->set(compact('examType'));
    }


    public function edit($id = null)
    {
        $examType = $this->ExamTypes->get($id, [
            'contain' => [],
        ]);
        if ($this->request->is(['patch', 'post', 'put'])) {
            $examType = $this->ExamTypes->patchEntity($examType, $this->request->getData());
            if ($this->ExamTypes->save($examType)) {
                $this->Flash->success(__('The exam type has been saved.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The exam type could not be saved. Please, try again.'));
        }

        $this->set(compact('examType'));
    }

    public function delete($id = null)
    {
        $this->request->allowMethod(['post', 'delete']);
        $examType = $this->ExamTypes->get($id);
        if ($this->ExamTypes->delete($examType)) {
            $this->Flash->success(__('The exam type has been deleted.'));
        } else {
            $this->Flash->error(__('The exam type could not be deleted. Please, try again.'));
        }

        return $this->redirect(['action' => 'index']);
    }
}
