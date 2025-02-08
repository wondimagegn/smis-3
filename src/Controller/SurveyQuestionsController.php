<?php
namespace App\Controller;

use App\Controller\AppController;

/**
 * SurveyQuestions Controller
 *
 * @property \App\Model\Table\SurveyQuestionsTable $SurveyQuestions
 *
 * @method \App\Model\Entity\SurveyQuestion[]|\Cake\Datasource\ResultSetInterface paginate($object = null, array $settings = [])
 */
class SurveyQuestionsController extends AppController
{

    public function index()
    {
        $surveyQuestions = $this->paginate($this->SurveyQuestions);

        $this->set(compact('surveyQuestions'));
    }

    public function view($id = null)
    {
        $surveyQuestion = $this->SurveyQuestions->get($id, [
            'contain' => ['AlumniResponses', 'SurveyQuestionAnswers'],
        ]);

        $this->set('surveyQuestion', $surveyQuestion);
    }

    public function add()
    {
        $surveyQuestion = $this->SurveyQuestions->newEntity();
        if ($this->request->is('post')) {
            $surveyQuestion = $this->SurveyQuestions->patchEntity($surveyQuestion, $this->request->getData());
            if ($this->SurveyQuestions->save($surveyQuestion)) {
                $this->Flash->success(__('The survey question has been saved.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The survey question could not be saved. Please, try again.'));
        }
        $this->set(compact('surveyQuestion'));
    }

    public function edit($id = null)
    {
        $surveyQuestion = $this->SurveyQuestions->get($id, [
            'contain' => [],
        ]);
        if ($this->request->is(['patch', 'post', 'put'])) {
            $surveyQuestion = $this->SurveyQuestions->patchEntity($surveyQuestion, $this->request->getData());
            if ($this->SurveyQuestions->save($surveyQuestion)) {
                $this->Flash->success(__('The survey question has been saved.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The survey question could not be saved. Please, try again.'));
        }
        $this->set(compact('surveyQuestion'));
    }

    public function delete($id = null)
    {
        $this->request->allowMethod(['post', 'delete']);
        $surveyQuestion = $this->SurveyQuestions->get($id);
        if ($this->SurveyQuestions->delete($surveyQuestion)) {
            $this->Flash->success(__('The survey question has been deleted.'));
        } else {
            $this->Flash->error(__('The survey question could not be deleted. Please, try again.'));
        }

        return $this->redirect(['action' => 'index']);
    }
}
