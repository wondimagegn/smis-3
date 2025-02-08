<?php
namespace App\Controller;

use App\Controller\AppController;

class WoredasController extends AppController
{

    public function index()
    {
        $this->paginate = [
            'contain' => ['Zones'],
        ];
        $woredas = $this->paginate($this->Woredas);

        $this->set(compact('woredas'));
    }


    public function view($id = null)
    {
        $woreda = $this->Woredas->get($id, [
            'contain' => ['Zones', 'AcceptedStudents', 'Contacts', 'Staffs', 'Students'],
        ]);

        $this->set('woreda', $woreda);
    }

    public function add()
    {
        $woreda = $this->Woredas->newEntity();
        if ($this->request->is('post')) {
            $woreda = $this->Woredas->patchEntity($woreda, $this->request->getData());
            if ($this->Woredas->save($woreda)) {
                $this->Flash->success(__('The woreda has been saved.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The woreda could not be saved. Please, try again.'));
        }
        $this->set(compact('woreda'));
    }

    public function edit($id = null)
    {
        $woreda = $this->Woredas->get($id, [
            'contain' => [],
        ]);
        if ($this->request->is(['patch', 'post', 'put'])) {
            $woreda = $this->Woredas->patchEntity($woreda, $this->request->getData());
            if ($this->Woredas->save($woreda)) {
                $this->Flash->success(__('The woreda has been saved.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The woreda could not be saved. Please, try again.'));
        }
        $this->set(compact('woreda'));
    }


    public function delete($id = null)
    {
        $this->request->allowMethod(['post', 'delete']);
        $woreda = $this->Woredas->get($id);
        if ($this->Woredas->delete($woreda)) {
            $this->Flash->success(__('The woreda has been deleted.'));
        } else {
            $this->Flash->error(__('The woreda could not be deleted. Please, try again.'));
        }

        return $this->redirect(['action' => 'index']);
    }
}
