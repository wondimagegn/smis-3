<?php
namespace App\Controller;

use App\Controller\AppController;

class HelpsController extends AppController
{

    public function index()
    {
        $helps = $this->paginate($this->Helps);

        $this->set(compact('helps'));
    }

    public function view($id = null)
    {
        $help = $this->Helps->get($id, [
            'contain' => [],
        ]);

        $this->set('help', $help);
    }

    public function add()
    {
        $help = $this->Helps->newEntity();
        if ($this->request->is('post')) {
            $help = $this->Helps->patchEntity($help, $this->request->getData());
            if ($this->Helps->save($help)) {
                $this->Flash->success(__('The help has been saved.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The help could not be saved. Please, try again.'));
        }
        $this->set(compact('help'));
    }

    public function edit($id = null)
    {
        $help = $this->Helps->get($id, [
            'contain' => [],
        ]);
        if ($this->request->is(['patch', 'post', 'put'])) {
            $help = $this->Helps->patchEntity($help, $this->request->getData());
            if ($this->Helps->save($help)) {
                $this->Flash->success(__('The help has been saved.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The help could not be saved. Please, try again.'));
        }
        $this->set(compact('help'));
    }


    public function delete($id = null)
    {
        $this->request->allowMethod(['post', 'delete']);
        $help = $this->Helps->get($id);
        if ($this->Helps->delete($help)) {
            $this->Flash->success(__('The help has been deleted.'));
        } else {
            $this->Flash->error(__('The help could not be deleted. Please, try again.'));
        }

        return $this->redirect(['action' => 'index']);
    }
}
