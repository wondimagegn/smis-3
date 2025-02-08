<?php
namespace App\Controller;

use App\Controller\AppController;

class CostSharesController extends AppController
{

    public function index()
    {
        $this->paginate = [
            'contain' => ['Students'],
        ];
        $costShares = $this->paginate($this->CostShares);

        $this->set(compact('costShares'));
    }


    public function view($id = null)
    {
        $costShare = $this->CostShares->get($id, [
            'contain' => ['Students'],
        ]);

        $this->set('costShare', $costShare);
    }


    public function add()
    {
        $costShare = $this->CostShares->newEntity();
        if ($this->request->is('post')) {
            $costShare = $this->CostShares->patchEntity($costShare, $this->request->getData());
            if ($this->CostShares->save($costShare)) {
                $this->Flash->success(__('The cost share has been saved.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The cost share could not be saved. Please, try again.'));
        }
        $this->set(compact('costShare'));
    }


    public function edit($id = null)
    {
        $costShare = $this->CostShares->get($id, [
            'contain' => [],
        ]);
        if ($this->request->is(['patch', 'post', 'put'])) {
            $costShare = $this->CostShares->patchEntity($costShare, $this->request->getData());
            if ($this->CostShares->save($costShare)) {
                $this->Flash->success(__('The cost share has been saved.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The cost share could not be saved. Please, try again.'));
        }

        $this->set(compact('costShare'));
    }


    public function delete($id = null)
    {
        $this->request->allowMethod(['post', 'delete']);
        $costShare = $this->CostShares->get($id);
        if ($this->CostShares->delete($costShare)) {
            $this->Flash->success(__('The cost share has been deleted.'));
        } else {
            $this->Flash->error(__('The cost share could not be deleted. Please, try again.'));
        }

        return $this->redirect(['action' => 'index']);
    }
}
