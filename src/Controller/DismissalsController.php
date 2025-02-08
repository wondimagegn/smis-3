<?php
namespace App\Controller;

use App\Controller\AppController;

class DismissalsController extends AppController
{

    public function index()
    {
        $this->paginate = [
            'contain' => ['Students'],
        ];
        $dismissals = $this->paginate($this->Dismissals);

        $this->set(compact('dismissals'));
    }


    public function view($id = null)
    {
        $dismissal = $this->Dismissals->get($id, [
            'contain' => ['Students'],
        ]);

        $this->set('dismissal', $dismissal);
    }

    public function add()
    {
        $dismissal = $this->Dismissals->newEntity();
        if ($this->request->is('post')) {
            $dismissal = $this->Dismissals->patchEntity($dismissal, $this->request->getData());
            if ($this->Dismissals->save($dismissal)) {
                $this->Flash->success(__('The dismissal has been saved.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The dismissal could not be saved. Please, try again.'));
        }

        $this->set(compact('dismissal'));
    }

    public function edit($id = null)
    {
        $dismissal = $this->Dismissals->get($id, [
            'contain' => [],
        ]);
        if ($this->request->is(['patch', 'post', 'put'])) {
            $dismissal = $this->Dismissals->patchEntity($dismissal, $this->request->getData());
            if ($this->Dismissals->save($dismissal)) {
                $this->Flash->success(__('The dismissal has been saved.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The dismissal could not be saved. Please, try again.'));
        }

        $this->set(compact('dismissal'));
    }


    public function delete($id = null)
    {
        $this->request->allowMethod(['post', 'delete']);
        $dismissal = $this->Dismissals->get($id);
        if ($this->Dismissals->delete($dismissal)) {
            $this->Flash->success(__('The dismissal has been deleted.'));
        } else {
            $this->Flash->error(__('The dismissal could not be deleted. Please, try again.'));
        }

        return $this->redirect(['action' => 'index']);
    }
}
