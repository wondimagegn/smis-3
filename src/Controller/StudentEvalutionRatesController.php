<?php
namespace App\Controller;

use App\Controller\AppController;

class StudentEvalutionRatesController extends AppController
{

    public function index()
    {
        $this->paginate = [
            'contain' => ['InstructorEvalutionQuestions', 'Students', 'PublishedCourses'],
        ];
        $studentEvalutionRates = $this->paginate($this->StudentEvalutionRates);

        $this->set(compact('studentEvalutionRates'));
    }

    public function view($id = null)
    {
        $studentEvalutionRate = $this->StudentEvalutionRates->get($id, [
            'contain' => ['InstructorEvalutionQuestions', 'Students', 'PublishedCourses'],
        ]);

        $this->set('studentEvalutionRate', $studentEvalutionRate);
    }

    public function add()
    {
        $studentEvalutionRate = $this->StudentEvalutionRates->newEntity();
        if ($this->request->is('post')) {
            $studentEvalutionRate = $this->StudentEvalutionRates->patchEntity($studentEvalutionRate, $this->request->getData());
            if ($this->StudentEvalutionRates->save($studentEvalutionRate)) {
                $this->Flash->success(__('The student evalution rate has been saved.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The student evalution rate could not be saved. Please, try again.'));
        }

        $this->set(compact('studentEvalutionRate'));
    }


    public function edit($id = null)
    {
        $studentEvalutionRate = $this->StudentEvalutionRates->get($id, [
            'contain' => [],
        ]);
        if ($this->request->is(['patch', 'post', 'put'])) {
            $studentEvalutionRate = $this->StudentEvalutionRates->patchEntity($studentEvalutionRate, $this->request->getData());
            if ($this->StudentEvalutionRates->save($studentEvalutionRate)) {
                $this->Flash->success(__('The student evalution rate has been saved.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The student evalution rate could not be saved. Please, try again.'));
        }
        $this->set(compact('studentEvalutionRate'));
    }

    public function delete($id = null)
    {
        $this->request->allowMethod(['post', 'delete']);
        $studentEvalutionRate = $this->StudentEvalutionRates->get($id);
        if ($this->StudentEvalutionRates->delete($studentEvalutionRate)) {
            $this->Flash->success(__('The student evalution rate has been deleted.'));
        } else {
            $this->Flash->error(__('The student evalution rate could not be deleted. Please, try again.'));
        }

        return $this->redirect(['action' => 'index']);
    }
}
