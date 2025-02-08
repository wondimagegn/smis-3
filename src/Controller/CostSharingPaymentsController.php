<?php
namespace App\Controller;

use App\Controller\AppController;

class CostSharingPaymentsController extends AppController
{

    public function index()
    {
        $this->paginate = [
            'contain' => ['Students'],
        ];
        $costSharingPayments = $this->paginate($this->CostSharingPayments);

        $this->set(compact('costSharingPayments'));
    }


    public function view($id = null)
    {
        $costSharingPayment = $this->CostSharingPayments->get($id, [
            'contain' => ['Students'],
        ]);

        $this->set('costSharingPayment', $costSharingPayment);
    }

    public function add()
    {
        $costSharingPayment = $this->CostSharingPayments->newEntity();
        if ($this->request->is('post')) {
            $costSharingPayment = $this->CostSharingPayments->patchEntity($costSharingPayment, $this->request->getData());
            if ($this->CostSharingPayments->save($costSharingPayment)) {
                $this->Flash->success(__('The cost sharing payment has been saved.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The cost sharing payment could not be saved. Please, try again.'));
        }

        $this->set(compact('costSharingPayment'));
    }


    public function edit($id = null)
    {
        $costSharingPayment = $this->CostSharingPayments->get($id, [
            'contain' => [],
        ]);
        if ($this->request->is(['patch', 'post', 'put'])) {
            $costSharingPayment = $this->CostSharingPayments->patchEntity($costSharingPayment, $this->request->getData());
            if ($this->CostSharingPayments->save($costSharingPayment)) {
                $this->Flash->success(__('The cost sharing payment has been saved.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The cost sharing payment could not be saved. Please, try again.'));
        }

        $this->set(compact('costSharingPayment'));
    }


    public function delete($id = null)
    {
        $this->request->allowMethod(['post', 'delete']);
        $costSharingPayment = $this->CostSharingPayments->get($id);
        if ($this->CostSharingPayments->delete($costSharingPayment)) {
            $this->Flash->success(__('The cost sharing payment has been deleted.'));
        } else {
            $this->Flash->error(__('The cost sharing payment could not be deleted. Please, try again.'));
        }

        return $this->redirect(['action' => 'index']);
    }
}
