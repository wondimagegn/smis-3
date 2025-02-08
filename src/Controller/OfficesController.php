<?php
namespace App\Controller;

use App\Controller\AppController;

class OfficesController extends AppController
{

    public function index()
    {
        $this->paginate = [
            'contain' => ['Staffs'],
        ];
        $offices = $this->paginate($this->Offices);

        $this->set(compact('offices'));
    }

    public function view($id = null)
    {
        $office = $this->Offices->get($id, [
            'contain' => ['Staffs', 'TakenProperties'],
        ]);

        $this->set('office', $office);
    }

    public function add()
    {
        $office = $this->Offices->newEntity();
        if ($this->request->is('post')) {
            $office = $this->Offices->patchEntity($office, $this->request->getData());
            if ($this->Offices->save($office)) {
                $this->Flash->success(__('The office has been saved.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The office could not be saved. Please, try again.'));
        }

        $this->set(compact('office'));
    }

    public function edit($id = null)
    {
        $office = $this->Offices->get($id, [
            'contain' => [],
        ]);
        if ($this->request->is(['patch', 'post', 'put'])) {
            $office = $this->Offices->patchEntity($office, $this->request->getData());
            if ($this->Offices->save($office)) {
                $this->Flash->success(__('The office has been saved.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The office could not be saved. Please, try again.'));
        }
        $this->set(compact('office'));
    }


    public function delete($id = null)
    {
        $this->request->allowMethod(['post', 'delete']);
        $office = $this->Offices->get($id);
        if ($this->Offices->delete($office)) {
            $this->Flash->success(__('The office has been deleted.'));
        } else {
            $this->Flash->error(__('The office could not be deleted. Please, try again.'));
        }

        return $this->redirect(['action' => 'index']);
    }
}
