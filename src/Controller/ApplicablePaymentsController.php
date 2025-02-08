<?php
namespace App\Controller;

use App\Controller\AppController;

class ApplicablePaymentsController extends AppController
{

    public function index()
    {
        $this->paginate = [
            'contain' => ['Students'],
        ];
        $applicablePayments = $this->paginate($this->ApplicablePayments);

        $this->set(compact('applicablePayments'));
    }


    public function view($id = null)
    {
        $applicablePayment = $this->ApplicablePayments->get($id, [
            'contain' => ['Students'],
        ]);

        $this->set('applicablePayment', $applicablePayment);
    }

    public function add()
    {
        $applicablePayment = $this->ApplicablePayments->newEntity();
        if ($this->request->is('post')) {
            $applicablePayment = $this->ApplicablePayments->patchEntity($applicablePayment, $this->request->getData());
            if ($this->ApplicablePayments->save($applicablePayment)) {
                $this->Flash->success(__('The applicable payment has been saved.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The applicable payment could not be saved. Please, try again.'));
        }

        $this->set(compact('applicablePayment'));
    }


    public function edit($id = null)
    {
        $applicablePayment = $this->ApplicablePayments->get($id, [
            'contain' => [],
        ]);
        if ($this->request->is(['patch', 'post', 'put'])) {
            $applicablePayment = $this->ApplicablePayments->patchEntity($applicablePayment, $this->request->getData());
            if ($this->ApplicablePayments->save($applicablePayment)) {
                $this->Flash->success(__('The applicable payment has been saved.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The applicable payment could not be saved. Please, try again.'));
        }

        $this->set(compact('applicablePayment'));
    }

    public function delete($id = null)
    {
        $this->request->allowMethod(['post', 'delete']);
        $applicablePayment = $this->ApplicablePayments->get($id);
        if ($this->ApplicablePayments->delete($applicablePayment)) {
            $this->Flash->success(__('The applicable payment has been deleted.'));
        } else {
            $this->Flash->error(__('The applicable payment could not be deleted. Please, try again.'));
        }

        return $this->redirect(['action' => 'index']);
    }
}
