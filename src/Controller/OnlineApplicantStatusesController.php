<?php
namespace App\Controller;

use App\Controller\AppController;

class OnlineApplicantStatusesController extends AppController
{

    public function index()
    {
        $this->paginate = [
            'contain' => ['OnlineApplicants'],
        ];
        $onlineApplicantStatuses = $this->paginate($this->OnlineApplicantStatuses);

        $this->set(compact('onlineApplicantStatuses'));
    }


    public function view($id = null)
    {
        $onlineApplicantStatus = $this->OnlineApplicantStatuses->get($id, [
            'contain' => ['OnlineApplicants'],
        ]);

        $this->set('onlineApplicantStatus', $onlineApplicantStatus);
    }

    public function add()
    {
        $onlineApplicantStatus = $this->OnlineApplicantStatuses->newEntity();
        if ($this->request->is('post')) {
            $onlineApplicantStatus = $this->OnlineApplicantStatuses->patchEntity($onlineApplicantStatus, $this->request->getData());
            if ($this->OnlineApplicantStatuses->save($onlineApplicantStatus)) {
                $this->Flash->success(__('The online applicant status has been saved.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The online applicant status could not be saved. Please, try again.'));
        }
        $this->set(compact('onlineApplicantStatus'));
    }


    public function edit($id = null)
    {
        $onlineApplicantStatus = $this->OnlineApplicantStatuses->get($id, [
            'contain' => [],
        ]);
        if ($this->request->is(['patch', 'post', 'put'])) {
            $onlineApplicantStatus = $this->OnlineApplicantStatuses->patchEntity($onlineApplicantStatus, $this->request->getData());
            if ($this->OnlineApplicantStatuses->save($onlineApplicantStatus)) {
                $this->Flash->success(__('The online applicant status has been saved.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The online applicant status could not be saved. Please, try again.'));
        }
        $this->set(compact('onlineApplicantStatus'));
    }

    public function delete($id = null)
    {
        $this->request->allowMethod(['post', 'delete']);
        $onlineApplicantStatus = $this->OnlineApplicantStatuses->get($id);
        if ($this->OnlineApplicantStatuses->delete($onlineApplicantStatus)) {
            $this->Flash->success(__('The online applicant status has been deleted.'));
        } else {
            $this->Flash->error(__('The online applicant status could not be deleted. Please, try again.'));
        }

        return $this->redirect(['action' => 'index']);
    }
}
