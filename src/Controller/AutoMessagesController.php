<?php
namespace App\Controller;

use App\Controller\AppController;

class AutoMessagesController extends AppController
{

    public function index()
    {
        $this->paginate = [
            'contain' => ['Users'],
        ];
        $autoMessages = $this->paginate($this->AutoMessages);

        $this->set(compact('autoMessages'));
    }

    public function view($id = null)
    {
        $autoMessage = $this->AutoMessages->get($id, [
            'contain' => ['Users'],
        ]);

        $this->set('autoMessage', $autoMessage);
    }

    public function add()
    {
        $autoMessage = $this->AutoMessages->newEntity();
        if ($this->request->is('post')) {
            $autoMessage = $this->AutoMessages->patchEntity($autoMessage, $this->request->getData());
            if ($this->AutoMessages->save($autoMessage)) {
                $this->Flash->success(__('The auto message has been saved.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The auto message could not be saved. Please, try again.'));
        }

        $this->set(compact('autoMessage'));
    }


    public function edit($id = null)
    {
        $autoMessage = $this->AutoMessages->get($id, [
            'contain' => [],
        ]);
        if ($this->request->is(['patch', 'post', 'put'])) {
            $autoMessage = $this->AutoMessages->patchEntity($autoMessage, $this->request->getData());
            if ($this->AutoMessages->save($autoMessage)) {
                $this->Flash->success(__('The auto message has been saved.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The auto message could not be saved. Please, try again.'));
        }
        $this->set(compact('autoMessage'));
    }


    public function delete($id = null)
    {
        $this->request->allowMethod(['post', 'delete']);
        $autoMessage = $this->AutoMessages->get($id);
        if ($this->AutoMessages->delete($autoMessage)) {
            $this->Flash->success(__('The auto message has been deleted.'));
        } else {
            $this->Flash->error(__('The auto message could not be deleted. Please, try again.'));
        }

        return $this->redirect(['action' => 'index']);
    }
}
