<?php
namespace App\Controller;

use App\Controller\AppController;

class ClearancesController extends AppController
{

    public function index()
    {
        $this->paginate = [
            'contain' => ['Students'],
        ];
        $clearances = $this->paginate($this->Clearances);

        $this->set(compact('clearances'));
    }

    public function view($id = null)
    {
        $clearance = $this->Clearances->get($id, [
            'contain' => ['Students'],
        ]);

        $this->set('clearance', $clearance);
    }

    public function add()
    {
        $clearance = $this->Clearances->newEntity();
        if ($this->request->is('post')) {
            $clearance = $this->Clearances->patchEntity($clearance, $this->request->getData());
            if ($this->Clearances->save($clearance)) {
                $this->Flash->success(__('The clearance has been saved.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The clearance could not be saved. Please, try again.'));
        }
        $students = $this->Clearances->Students->find('list', ['limit' => 200]);
        $this->set(compact('clearance', 'students'));
    }

    public function edit($id = null)
    {
        $clearance = $this->Clearances->get($id, [
            'contain' => [],
        ]);
        if ($this->request->is(['patch', 'post', 'put'])) {
            $clearance = $this->Clearances->patchEntity($clearance, $this->request->getData());
            if ($this->Clearances->save($clearance)) {
                $this->Flash->success(__('The clearance has been saved.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The clearance could not be saved. Please, try again.'));
        }
        $students = $this->Clearances->Students->find('list', ['limit' => 200]);
        $this->set(compact('clearance', 'students'));
    }

    public function delete($id = null)
    {
        $this->request->allowMethod(['post', 'delete']);
        $clearance = $this->Clearances->get($id);
        if ($this->Clearances->delete($clearance)) {
            $this->Flash->success(__('The clearance has been deleted.'));
        } else {
            $this->Flash->error(__('The clearance could not be deleted. Please, try again.'));
        }

        return $this->redirect(['action' => 'index']);
    }
}
