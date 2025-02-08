<?php
namespace App\Controller;

use App\Controller\AppController;

class DropOutsController extends AppController
{

    public function index()
    {
        $this->paginate = [
            'contain' => ['Students'],
        ];
        $dropOuts = $this->paginate($this->DropOuts);

        $this->set(compact('dropOuts'));
    }

    public function view($id = null)
    {
        $dropOut = $this->DropOuts->get($id, [
            'contain' => ['Students'],
        ]);

        $this->set('dropOut', $dropOut);
    }

    public function add()
    {
        $dropOut = $this->DropOuts->newEntity();
        if ($this->request->is('post')) {
            $dropOut = $this->DropOuts->patchEntity($dropOut, $this->request->getData());
            if ($this->DropOuts->save($dropOut)) {
                $this->Flash->success(__('The drop out has been saved.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The drop out could not be saved. Please, try again.'));
        }
        $this->set(compact('dropOut'));
    }


    public function edit($id = null)
    {
        $dropOut = $this->DropOuts->get($id, [
            'contain' => [],
        ]);
        if ($this->request->is(['patch', 'post', 'put'])) {
            $dropOut = $this->DropOuts->patchEntity($dropOut, $this->request->getData());
            if ($this->DropOuts->save($dropOut)) {
                $this->Flash->success(__('The drop out has been saved.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The drop out could not be saved. Please, try again.'));
        }

        $this->set(compact('dropOut'));
    }


    public function delete($id = null)
    {
        $this->request->allowMethod(['post', 'delete']);
        $dropOut = $this->DropOuts->get($id);
        if ($this->DropOuts->delete($dropOut)) {
            $this->Flash->success(__('The drop out has been deleted.'));
        } else {
            $this->Flash->error(__('The drop out could not be deleted. Please, try again.'));
        }

        return $this->redirect(['action' => 'index']);
    }
}
