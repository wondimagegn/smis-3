<?php
namespace App\Controller;

use App\Controller\AppController;

class ExamPeriodsController extends AppController
{

    public function index()
    {
        $this->paginate = [
            'contain' => ['Colleges', 'Programs', 'ProgramTypes', 'YearLevels'],
        ];
        $examPeriods = $this->paginate($this->ExamPeriods);

        $this->set(compact('examPeriods'));
    }

    public function view($id = null)
    {
        $examPeriod = $this->ExamPeriods->get($id, [
            'contain' => ['Colleges', 'Programs', 'ProgramTypes', 'YearLevels', 'ExamExcludedDateAndSessions'],
        ]);

        $this->set('examPeriod', $examPeriod);
    }

    public function add()
    {
        $examPeriod = $this->ExamPeriods->newEntity();
        if ($this->request->is('post')) {
            $examPeriod = $this->ExamPeriods->patchEntity($examPeriod, $this->request->getData());
            if ($this->ExamPeriods->save($examPeriod)) {
                $this->Flash->success(__('The exam period has been saved.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The exam period could not be saved. Please, try again.'));
        }

        $this->set(compact('examPeriod'));
    }


    public function edit($id = null)
    {
        $examPeriod = $this->ExamPeriods->get($id, [
            'contain' => [],
        ]);
        if ($this->request->is(['patch', 'post', 'put'])) {
            $examPeriod = $this->ExamPeriods->patchEntity($examPeriod, $this->request->getData());
            if ($this->ExamPeriods->save($examPeriod)) {
                $this->Flash->success(__('The exam period has been saved.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The exam period could not be saved. Please, try again.'));
        }

        $this->set(compact('examPeriod'));
    }


    public function delete($id = null)
    {
        $this->request->allowMethod(['post', 'delete']);
        $examPeriod = $this->ExamPeriods->get($id);
        if ($this->ExamPeriods->delete($examPeriod)) {
            $this->Flash->success(__('The exam period has been deleted.'));
        } else {
            $this->Flash->error(__('The exam period could not be deleted. Please, try again.'));
        }

        return $this->redirect(['action' => 'index']);
    }
}
