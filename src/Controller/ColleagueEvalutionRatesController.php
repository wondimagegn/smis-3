<?php
namespace App\Controller;

use App\Controller\AppController;

class ColleagueEvalutionRatesController extends AppController
{

    public function index()
    {
        $this->paginate = [
            'contain' => ['InstructorEvalutionQuestions', 'Staffs', 'Evaluators'],
        ];
        $colleagueEvalutionRates = $this->paginate($this->ColleagueEvalutionRates);

        $this->set(compact('colleagueEvalutionRates'));
    }


    public function view($id = null)
    {
        $colleagueEvalutionRate = $this->ColleagueEvalutionRates->get($id, [
            'contain' => ['InstructorEvalutionQuestions', 'Staffs', 'Evaluators'],
        ]);

        $this->set('colleagueEvalutionRate', $colleagueEvalutionRate);
    }

    public function add()
    {
        $colleagueEvalutionRate = $this->ColleagueEvalutionRates->newEntity();
        if ($this->request->is('post')) {
            $colleagueEvalutionRate = $this->ColleagueEvalutionRates->patchEntity($colleagueEvalutionRate, $this->request->getData());
            if ($this->ColleagueEvalutionRates->save($colleagueEvalutionRate)) {
                $this->Flash->success(__('The colleague evalution rate has been saved.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The colleague evalution rate could not be saved. Please, try again.'));
        }
        $instructorEvalutionQuestions = $this->ColleagueEvalutionRates->InstructorEvalutionQuestions->find('list', ['limit' => 200]);

        $evaluators = $this->ColleagueEvalutionRates->Evaluators->find('list', ['limit' => 200]);
        $this->set(compact('colleagueEvalutionRate', 'instructorEvalutionQuestions', 'staffs', 'evaluators'));
    }


    public function edit($id = null)
    {
        $colleagueEvalutionRate = $this->ColleagueEvalutionRates->get($id, [
            'contain' => [],
        ]);
        if ($this->request->is(['patch', 'post', 'put'])) {
            $colleagueEvalutionRate = $this->ColleagueEvalutionRates->patchEntity($colleagueEvalutionRate, $this->request->getData());
            if ($this->ColleagueEvalutionRates->save($colleagueEvalutionRate)) {
                $this->Flash->success(__('The colleague evalution rate has been saved.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The colleague evalution rate could not be saved. Please, try again.'));
        }
        $instructorEvalutionQuestions = $this->ColleagueEvalutionRates->InstructorEvalutionQuestions->find('list', ['limit' => 200]);
        $staffs = $this->ColleagueEvalutionRates->Staffs->find('list', ['limit' => 200]);
        $evaluators = $this->ColleagueEvalutionRates->Evaluators->find('list', ['limit' => 200]);
        $this->set(compact('colleagueEvalutionRate', 'instructorEvalutionQuestions', 'staffs', 'evaluators'));
    }


    public function delete($id = null)
    {
        $this->request->allowMethod(['post', 'delete']);
        $colleagueEvalutionRate = $this->ColleagueEvalutionRates->get($id);
        if ($this->ColleagueEvalutionRates->delete($colleagueEvalutionRate)) {
            $this->Flash->success(__('The colleague evalution rate has been deleted.'));
        } else {
            $this->Flash->error(__('The colleague evalution rate could not be deleted. Please, try again.'));
        }

        return $this->redirect(['action' => 'index']);
    }
}
