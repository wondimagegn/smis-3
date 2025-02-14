<?php
namespace App\Controller;

use App\Controller\AppController;

class AlumniResponsesController extends AppController
{

    public function index()
    {
        $this->paginate = [
            'contain' => ['Alumnis', 'SurveyQuestions', 'SurveyQuestionAnswers'],
        ];
        $alumniResponses = $this->paginate($this->AlumniResponses);

        $this->set(compact('alumniResponses'));
    }


    public function view($id = null)
    {
        $alumniResponse = $this->AlumniResponses->get($id, [
            'contain' => ['Alumnis', 'SurveyQuestions', 'SurveyQuestionAnswers'],
        ]);

        $this->set('alumniResponse', $alumniResponse);
    }

    public function add()
    {
        $alumniResponse = $this->AlumniResponses->newEntity();
        if ($this->request->is('post')) {
            $alumniResponse = $this->AlumniResponses->patchEntity($alumniResponse, $this->request->getData());
            if ($this->AlumniResponses->save($alumniResponse)) {
                $this->Flash->success(__('The alumni response has been saved.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The alumni response could not be saved. Please, try again.'));
        }

        $surveyQuestions = $this->AlumniResponses->SurveyQuestions->find('list', ['limit' => 200]);
        $surveyQuestionAnswers = $this->AlumniResponses->SurveyQuestionAnswers->find('list', ['limit' => 200]);
        $this->set(compact('alumniResponse', 'alumnis', 'surveyQuestions', 'surveyQuestionAnswers'));
    }


    public function edit($id = null)
    {
        $alumniResponse = $this->AlumniResponses->get($id, [
            'contain' => [],
        ]);
        if ($this->request->is(['patch', 'post', 'put'])) {
            $alumniResponse = $this->AlumniResponses->patchEntity($alumniResponse, $this->request->getData());
            if ($this->AlumniResponses->save($alumniResponse)) {
                $this->Flash->success(__('The alumni response has been saved.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The alumni response could not be saved. Please, try again.'));
        }

        $surveyQuestions = $this->AlumniResponses->SurveyQuestions->find('list', ['limit' => 200]);
        $surveyQuestionAnswers = $this->AlumniResponses->SurveyQuestionAnswers->find('list', ['limit' => 200]);
        $this->set(compact('alumniResponse', 'alumnis', 'surveyQuestions', 'surveyQuestionAnswers'));
    }


    public function delete($id = null)
    {
        $this->request->allowMethod(['post', 'delete']);
        $alumniResponse = $this->AlumniResponses->get($id);
        if ($this->AlumniResponses->delete($alumniResponse)) {
            $this->Flash->success(__('The alumni response has been deleted.'));
        } else {
            $this->Flash->error(__('The alumni response could not be deleted. Please, try again.'));
        }

        return $this->redirect(['action' => 'index']);
    }
}
