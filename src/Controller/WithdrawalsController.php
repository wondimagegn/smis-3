<?php
namespace App\Controller;

use App\Controller\AppController;

class WithdrawalsController extends AppController
{

    public function index()
    {
        $this->paginate = [
            'contain' => ['Students'],
        ];
        $withdrawals = $this->paginate($this->Withdrawals);

        $this->set(compact('withdrawals'));
    }

    public function view($id = null)
    {
        $withdrawal = $this->Withdrawals->get($id, [
            'contain' => ['Students'],
        ]);

        $this->set('withdrawal', $withdrawal);
    }

    public function add()
    {
        $withdrawal = $this->Withdrawals->newEntity();
        if ($this->request->is('post')) {
            $withdrawal = $this->Withdrawals->patchEntity($withdrawal, $this->request->getData());
            if ($this->Withdrawals->save($withdrawal)) {
                $this->Flash->success(__('The withdrawal has been saved.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The withdrawal could not be saved. Please, try again.'));
        }
        $this->set(compact('withdrawal'));
    }


    public function edit($id = null)
    {
        $withdrawal = $this->Withdrawals->get($id, [
            'contain' => [],
        ]);
        if ($this->request->is(['patch', 'post', 'put'])) {
            $withdrawal = $this->Withdrawals->patchEntity($withdrawal, $this->request->getData());
            if ($this->Withdrawals->save($withdrawal)) {
                $this->Flash->success(__('The withdrawal has been saved.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The withdrawal could not be saved. Please, try again.'));
        }
        $this->set(compact('withdrawal'));
    }

    public function delete($id = null)
    {
        $this->request->allowMethod(['post', 'delete']);
        $withdrawal = $this->Withdrawals->get($id);
        if ($this->Withdrawals->delete($withdrawal)) {
            $this->Flash->success(__('The withdrawal has been deleted.'));
        } else {
            $this->Flash->error(__('The withdrawal could not be deleted. Please, try again.'));
        }

        return $this->redirect(['action' => 'index']);
    }
}
