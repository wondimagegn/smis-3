<?php
namespace App\Controller;

use App\Controller\AppController;

class OfficialRequestStatusesController extends AppController
{

    public function index()
    {
        $this->paginate = [
            'contain' => ['OfficialTranscriptRequests'],
        ];
        $officialRequestStatuses = $this->paginate($this->OfficialRequestStatuses);

        $this->set(compact('officialRequestStatuses'));
    }

    public function view($id = null)
    {
        $officialRequestStatus = $this->OfficialRequestStatuses->get($id, [
            'contain' => ['OfficialTranscriptRequests'],
        ]);

        $this->set('officialRequestStatus', $officialRequestStatus);
    }

    public function add()
    {
        $officialRequestStatus = $this->OfficialRequestStatuses->newEntity();
        if ($this->request->is('post')) {
            $officialRequestStatus = $this->OfficialRequestStatuses->patchEntity($officialRequestStatus, $this->request->getData());
            if ($this->OfficialRequestStatuses->save($officialRequestStatus)) {
                $this->Flash->success(__('The official request status has been saved.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The official request status could not be saved. Please, try again.'));
        }
        $this->set(compact('officialRequestStatus'));
    }


    public function edit($id = null)
    {
        $officialRequestStatus = $this->OfficialRequestStatuses->get($id, [
            'contain' => [],
        ]);
        if ($this->request->is(['patch', 'post', 'put'])) {
            $officialRequestStatus = $this->OfficialRequestStatuses->patchEntity($officialRequestStatus, $this->request->getData());
            if ($this->OfficialRequestStatuses->save($officialRequestStatus)) {
                $this->Flash->success(__('The official request status has been saved.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The official request status could not be saved. Please, try again.'));
        }
      $this->set(compact('officialRequestStatus'));
    }


    public function delete($id = null)
    {
        $this->request->allowMethod(['post', 'delete']);
        $officialRequestStatus = $this->OfficialRequestStatuses->get($id);
        if ($this->OfficialRequestStatuses->delete($officialRequestStatus)) {
            $this->Flash->success(__('The official request status has been deleted.'));
        } else {
            $this->Flash->error(__('The official request status could not be deleted. Please, try again.'));
        }

        return $this->redirect(['action' => 'index']);
    }
}
