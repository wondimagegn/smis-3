<?php
namespace App\Controller;

use App\Controller\AppController;

class CourseSubstitutionRequestsController extends AppController
{

    public function index()
    {
        $this->paginate = [
            'contain' => ['Students', 'CourseForSubstitueds', 'CourseBeSubstitueds'],
        ];
        $courseSubstitutionRequests = $this->paginate($this->CourseSubstitutionRequests);

        $this->set(compact('courseSubstitutionRequests'));
    }


    public function view($id = null)
    {
        $courseSubstitutionRequest = $this->CourseSubstitutionRequests->get($id, [
            'contain' => ['Students', 'CourseForSubstitueds', 'CourseBeSubstitueds'],
        ]);

        $this->set('courseSubstitutionRequest', $courseSubstitutionRequest);
    }


    public function add()
    {
        $courseSubstitutionRequest = $this->CourseSubstitutionRequests->newEntity();
        if ($this->request->is('post')) {
            $courseSubstitutionRequest = $this->CourseSubstitutionRequests->patchEntity($courseSubstitutionRequest, $this->request->getData());
            if ($this->CourseSubstitutionRequests->save($courseSubstitutionRequest)) {
                $this->Flash->success(__('The course substitution request has been saved.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The course substitution request could not be saved. Please, try again.'));
        }
         $this->set(compact('courseSubstitutionRequest'));
    }


    public function edit($id = null)
    {
        $courseSubstitutionRequest = $this->CourseSubstitutionRequests->get($id, [
            'contain' => [],
        ]);
        if ($this->request->is(['patch', 'post', 'put'])) {
            $courseSubstitutionRequest = $this->CourseSubstitutionRequests->patchEntity($courseSubstitutionRequest, $this->request->getData());
            if ($this->CourseSubstitutionRequests->save($courseSubstitutionRequest)) {
                $this->Flash->success(__('The course substitution request has been saved.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The course substitution request could not be saved. Please, try again.'));
        }
          $this->set(compact('courseSubstitutionRequest'));
    }

    public function delete($id = null)
    {
        $this->request->allowMethod(['post', 'delete']);
        $courseSubstitutionRequest = $this->CourseSubstitutionRequests->get($id);
        if ($this->CourseSubstitutionRequests->delete($courseSubstitutionRequest)) {
            $this->Flash->success(__('The course substitution request has been deleted.'));
        } else {
            $this->Flash->error(__('The course substitution request could not be deleted. Please, try again.'));
        }

        return $this->redirect(['action' => 'index']);
    }
}
