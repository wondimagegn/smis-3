<?php
namespace App\Controller;

use App\Controller\AppController;

class OfficialTranscriptRequestsController extends AppController
{

    public function index()
    {
        $officialTranscriptRequests = $this->paginate($this->OfficialTranscriptRequests);

        $this->set(compact('officialTranscriptRequests'));
    }

    public function view($id = null)
    {
        $officialTranscriptRequest = $this->OfficialTranscriptRequests->get($id, [
            'contain' => ['OfficialRequestStatuses'],
        ]);

        $this->set('officialTranscriptRequest', $officialTranscriptRequest);
    }

    public function add()
    {
        $officialTranscriptRequest = $this->OfficialTranscriptRequests->newEntity();
        if ($this->request->is('post')) {
            $officialTranscriptRequest = $this->OfficialTranscriptRequests->patchEntity($officialTranscriptRequest, $this->request->getData());
            if ($this->OfficialTranscriptRequests->save($officialTranscriptRequest)) {
                $this->Flash->success(__('The official transcript request has been saved.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The official transcript request could not be saved. Please, try again.'));
        }
        $this->set(compact('officialTranscriptRequest'));
    }


    public function edit($id = null)
    {
        $officialTranscriptRequest = $this->OfficialTranscriptRequests->get($id, [
            'contain' => [],
        ]);
        if ($this->request->is(['patch', 'post', 'put'])) {
            $officialTranscriptRequest = $this->OfficialTranscriptRequests->patchEntity($officialTranscriptRequest, $this->request->getData());
            if ($this->OfficialTranscriptRequests->save($officialTranscriptRequest)) {
                $this->Flash->success(__('The official transcript request has been saved.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The official transcript request could not be saved. Please, try again.'));
        }
        $this->set(compact('officialTranscriptRequest'));
    }

    public function delete($id = null)
    {
        $this->request->allowMethod(['post', 'delete']);
        $officialTranscriptRequest = $this->OfficialTranscriptRequests->get($id);
        if ($this->OfficialTranscriptRequests->delete($officialTranscriptRequest)) {
            $this->Flash->success(__('The official transcript request has been deleted.'));
        } else {
            $this->Flash->error(__('The official transcript request could not be deleted. Please, try again.'));
        }

        return $this->redirect(['action' => 'index']);
    }
}
