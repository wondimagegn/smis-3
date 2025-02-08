<?php
namespace App\Controller;

use App\Controller\AppController;

class OnlineApplicantsController extends AppController
{

    public function index()
    {
        $this->paginate = [
            'contain' => ['Colleges', 'Departments', 'Programs', 'ProgramTypes'],
        ];
        $onlineApplicants = $this->paginate($this->OnlineApplicants);

        $this->set(compact('onlineApplicants'));
    }

    public function view($id = null)
    {
        $onlineApplicant = $this->OnlineApplicants->get($id, [
            'contain' => ['Colleges', 'Departments', 'Programs', 'ProgramTypes', 'AcceptedStudents', 'OnlineApplicantStatuses'],
        ]);

        $this->set('onlineApplicant', $onlineApplicant);
    }


    public function add()
    {
        $onlineApplicant = $this->OnlineApplicants->newEntity();
        if ($this->request->is('post')) {
            $onlineApplicant = $this->OnlineApplicants->patchEntity($onlineApplicant, $this->request->getData());
            if ($this->OnlineApplicants->save($onlineApplicant)) {
                $this->Flash->success(__('The online applicant has been saved.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The online applicant could not be saved. Please, try again.'));
        }
        $this->set(compact('onlineApplicant'));
    }

    public function edit($id = null)
    {
        $onlineApplicant = $this->OnlineApplicants->get($id, [
            'contain' => [],
        ]);
        if ($this->request->is(['patch', 'post', 'put'])) {
            $onlineApplicant = $this->OnlineApplicants->patchEntity($onlineApplicant, $this->request->getData());
            if ($this->OnlineApplicants->save($onlineApplicant)) {
                $this->Flash->success(__('The online applicant has been saved.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The online applicant could not be saved. Please, try again.'));
        }
        $this->set(compact('onlineApplicant'));
    }


    public function delete($id = null)
    {
        $this->request->allowMethod(['post', 'delete']);
        $onlineApplicant = $this->OnlineApplicants->get($id);
        if ($this->OnlineApplicants->delete($onlineApplicant)) {
            $this->Flash->success(__('The online applicant has been deleted.'));
        } else {
            $this->Flash->error(__('The online applicant could not be deleted. Please, try again.'));
        }

        return $this->redirect(['action' => 'index']);
    }
}
