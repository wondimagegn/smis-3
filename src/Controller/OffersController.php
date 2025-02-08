<?php
namespace App\Controller;

use App\Controller\AppController;

class OffersController extends AppController
{
    public function index()
    {
        $this->paginate = [
            'contain' => ['Departments', 'ProgramTypes'],
        ];
        $offers = $this->paginate($this->Offers);

        $this->set(compact('offers'));
    }


    public function view($id = null)
    {
        $offer = $this->Offers->get($id, [
            'contain' => ['Departments', 'ProgramTypes'],
        ]);

        $this->set('offer', $offer);
    }

    public function add()
    {
        $offer = $this->Offers->newEntity();
        if ($this->request->is('post')) {
            $offer = $this->Offers->patchEntity($offer, $this->request->getData());
            if ($this->Offers->save($offer)) {
                $this->Flash->success(__('The offer has been saved.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The offer could not be saved. Please, try again.'));
        }
        $this->set(compact('offer'));
    }

    public function edit($id = null)
    {
        $offer = $this->Offers->get($id, [
            'contain' => [],
        ]);
        if ($this->request->is(['patch', 'post', 'put'])) {
            $offer = $this->Offers->patchEntity($offer, $this->request->getData());
            if ($this->Offers->save($offer)) {
                $this->Flash->success(__('The offer has been saved.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The offer could not be saved. Please, try again.'));
        }

        $this->set(compact('offer'));
    }


    public function delete($id = null)
    {
        $this->request->allowMethod(['post', 'delete']);
        $offer = $this->Offers->get($id);
        if ($this->Offers->delete($offer)) {
            $this->Flash->success(__('The offer has been deleted.'));
        } else {
            $this->Flash->error(__('The offer could not be deleted. Please, try again.'));
        }

        return $this->redirect(['action' => 'index']);
    }
}
