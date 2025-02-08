<?php
namespace App\Controller;

use App\Controller\AppController;

class QuotasController extends AppController
{

    public function index()
    {
        $this->paginate = [
            'contain' => ['Colleges', 'DevelopingRegions'],
        ];
        $quotas = $this->paginate($this->Quotas);

        $this->set(compact('quotas'));
    }

    public function view($id = null)
    {
        $quota = $this->Quotas->get($id, [
            'contain' => ['Colleges', 'DevelopingRegions'],
        ]);

        $this->set('quota', $quota);
    }

    public function add()
    {
        $quota = $this->Quotas->newEntity();
        if ($this->request->is('post')) {
            $quota = $this->Quotas->patchEntity($quota, $this->request->getData());
            if ($this->Quotas->save($quota)) {
                $this->Flash->success(__('The quota has been saved.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The quota could not be saved. Please, try again.'));
        }
        $this->set(compact('quota'));
    }

    public function edit($id = null)
    {
        $quota = $this->Quotas->get($id, [
            'contain' => [],
        ]);
        if ($this->request->is(['patch', 'post', 'put'])) {
            $quota = $this->Quotas->patchEntity($quota, $this->request->getData());
            if ($this->Quotas->save($quota)) {
                $this->Flash->success(__('The quota has been saved.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The quota could not be saved. Please, try again.'));
        }
        $this->set(compact('quota'));
    }


    public function delete($id = null)
    {
        $this->request->allowMethod(['post', 'delete']);
        $quota = $this->Quotas->get($id);
        if ($this->Quotas->delete($quota)) {
            $this->Flash->success(__('The quota has been deleted.'));
        } else {
            $this->Flash->error(__('The quota could not be deleted. Please, try again.'));
        }

        return $this->redirect(['action' => 'index']);
    }
}
