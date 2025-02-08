<?php
namespace App\Controller;

use App\Controller\AppController;

class InstructorEvalutionQuestionsController extends AppController
{

    public function index()
    {
        $instructorEvalutionQuestions = $this->paginate($this->InstructorEvalutionQuestions);

        $this->set(compact('instructorEvalutionQuestions'));
    }

    public function view($id = null)
    {
        $instructorEvalutionQuestion = $this->InstructorEvalutionQuestions->get($id, [
            'contain' => ['ColleagueEvalutionRates', 'StudentEvalutionComments', 'StudentEvalutionRates'],
        ]);

        $this->set('instructorEvalutionQuestion', $instructorEvalutionQuestion);
    }

    public function add()
    {
        $instructorEvalutionQuestion = $this->InstructorEvalutionQuestions->newEntity();
        if ($this->request->is('post')) {
            $instructorEvalutionQuestion = $this->InstructorEvalutionQuestions->patchEntity($instructorEvalutionQuestion, $this->request->getData());
            if ($this->InstructorEvalutionQuestions->save($instructorEvalutionQuestion)) {
                $this->Flash->success(__('The instructor evalution question has been saved.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The instructor evalution question could not be saved. Please, try again.'));
        }
        $this->set(compact('instructorEvalutionQuestion'));
    }

    public function edit($id = null)
    {
        $instructorEvalutionQuestion = $this->InstructorEvalutionQuestions->get($id, [
            'contain' => [],
        ]);
        if ($this->request->is(['patch', 'post', 'put'])) {
            $instructorEvalutionQuestion = $this->InstructorEvalutionQuestions->patchEntity($instructorEvalutionQuestion, $this->request->getData());
            if ($this->InstructorEvalutionQuestions->save($instructorEvalutionQuestion)) {
                $this->Flash->success(__('The instructor evalution question has been saved.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The instructor evalution question could not be saved. Please, try again.'));
        }
        $this->set(compact('instructorEvalutionQuestion'));
    }

    public function delete($id = null)
    {
        $this->request->allowMethod(['post', 'delete']);
        $instructorEvalutionQuestion = $this->InstructorEvalutionQuestions->get($id);
        if ($this->InstructorEvalutionQuestions->delete($instructorEvalutionQuestion)) {
            $this->Flash->success(__('The instructor evalution question has been deleted.'));
        } else {
            $this->Flash->error(__('The instructor evalution question could not be deleted. Please, try again.'));
        }

        return $this->redirect(['action' => 'index']);
    }
}
