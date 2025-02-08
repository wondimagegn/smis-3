<?php
namespace App\Controller;

use App\Controller\AppController;

class ExamExcludedDateAndSessionsController extends AppController
{

    public function index()
    {
        $this->paginate = [
            'contain' => ['ExamPeriods'],
        ];
        $examExcludedDateAndSessions = $this->paginate($this->ExamExcludedDateAndSessions);

        $this->set(compact('examExcludedDateAndSessions'));
    }


    public function view($id = null)
    {
        $examExcludedDateAndSession = $this->ExamExcludedDateAndSessions->get($id, [
            'contain' => ['ExamPeriods'],
        ]);

        $this->set('examExcludedDateAndSession', $examExcludedDateAndSession);
    }


    public function add()
    {
        $examExcludedDateAndSession = $this->ExamExcludedDateAndSessions->newEntity();
        if ($this->request->is('post')) {
            $examExcludedDateAndSession = $this->ExamExcludedDateAndSessions->patchEntity($examExcludedDateAndSession, $this->request->getData());
            if ($this->ExamExcludedDateAndSessions->save($examExcludedDateAndSession)) {
                $this->Flash->success(__('The exam excluded date and session has been saved.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The exam excluded date and session could not be saved. Please, try again.'));
        }
        $this->set(compact('examExcludedDateAndSession'));
    }


    public function edit($id = null)
    {
        $examExcludedDateAndSession = $this->ExamExcludedDateAndSessions->get($id, [
            'contain' => [],
        ]);
        if ($this->request->is(['patch', 'post', 'put'])) {
            $examExcludedDateAndSession = $this->ExamExcludedDateAndSessions->patchEntity($examExcludedDateAndSession, $this->request->getData());
            if ($this->ExamExcludedDateAndSessions->save($examExcludedDateAndSession)) {
                $this->Flash->success(__('The exam excluded date and session has been saved.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The exam excluded date and session could not be saved. Please, try again.'));
        }

        $this->set(compact('examExcludedDateAndSession'));
    }


    public function delete($id = null)
    {
        $this->request->allowMethod(['post', 'delete']);
        $examExcludedDateAndSession = $this->ExamExcludedDateAndSessions->get($id);
        if ($this->ExamExcludedDateAndSessions->delete($examExcludedDateAndSession)) {
            $this->Flash->success(__('The exam excluded date and session has been deleted.'));
        } else {
            $this->Flash->error(__('The exam excluded date and session could not be deleted. Please, try again.'));
        }

        return $this->redirect(['action' => 'index']);
    }
}
