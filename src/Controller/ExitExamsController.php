<?php
namespace App\Controller;

use App\Controller\AppController;

class ExitExamsController extends AppController
{

    public function index()
    {
        $this->paginate = [
            'contain' => ['Students', 'Courses'],
        ];
        $exitExams = $this->paginate($this->ExitExams);

        $this->set(compact('exitExams'));
    }


    public function view($id = null)
    {
        $exitExam = $this->ExitExams->get($id, [
            'contain' => ['Students', 'Courses'],
        ]);

        $this->set('exitExam', $exitExam);
    }

    public function add()
    {
        $exitExam = $this->ExitExams->newEntity();
        if ($this->request->is('post')) {
            $exitExam = $this->ExitExams->patchEntity($exitExam, $this->request->getData());
            if ($this->ExitExams->save($exitExam)) {
                $this->Flash->success(__('The exit exam has been saved.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The exit exam could not be saved. Please, try again.'));
        }

        $this->set(compact('exitExam'));
    }


    public function edit($id = null)
    {
        $exitExam = $this->ExitExams->get($id, [
            'contain' => [],
        ]);
        if ($this->request->is(['patch', 'post', 'put'])) {
            $exitExam = $this->ExitExams->patchEntity($exitExam, $this->request->getData());
            if ($this->ExitExams->save($exitExam)) {
                $this->Flash->success(__('The exit exam has been saved.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The exit exam could not be saved. Please, try again.'));
        }

        $this->set(compact('exitExam'));
    }


    public function delete($id = null)
    {
        $this->request->allowMethod(['post', 'delete']);
        $exitExam = $this->ExitExams->get($id);
        if ($this->ExitExams->delete($exitExam)) {
            $this->Flash->success(__('The exit exam has been deleted.'));
        } else {
            $this->Flash->error(__('The exit exam could not be deleted. Please, try again.'));
        }

        return $this->redirect(['action' => 'index']);
    }
}
