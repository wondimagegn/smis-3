<?php
namespace App\Controller;

use App\Controller\AppController;

class ReadmissionsController extends AppController
{

    public function index()
    {
        $this->paginate = [
            'contain' => ['Students'],
        ];
        $readmissions = $this->paginate($this->Readmissions);

        $this->set(compact('readmissions'));
    }

    public function view($id = null)
    {
        $readmission = $this->Readmissions->get($id, [
            'contain' => ['Students'],
        ]);

        $this->set('readmission', $readmission);
    }

    public function add()
    {
        $readmission = $this->Readmissions->newEntity();
        if ($this->request->is('post')) {
            $readmission = $this->Readmissions->patchEntity($readmission, $this->request->getData());
            if ($this->Readmissions->save($readmission)) {
                $this->Flash->success(__('The readmission has been saved.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The readmission could not be saved. Please, try again.'));
        }
        $this->set(compact('readmission', 'students'));
    }

    public function edit($id = null)
    {
        $readmission = $this->Readmissions->get($id, [
            'contain' => [],
        ]);
        if ($this->request->is(['patch', 'post', 'put'])) {
            $readmission = $this->Readmissions->patchEntity($readmission, $this->request->getData());
            if ($this->Readmissions->save($readmission)) {
                $this->Flash->success(__('The readmission has been saved.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The readmission could not be saved. Please, try again.'));
        }

        $this->set(compact('readmission', 'students'));
    }


    public function delete($id = null)
    {
        $this->request->allowMethod(['post', 'delete']);
        $readmission = $this->Readmissions->get($id);
        if ($this->Readmissions->delete($readmission)) {
            $this->Flash->success(__('The readmission has been deleted.'));
        } else {
            $this->Flash->error(__('The readmission could not be deleted. Please, try again.'));
        }

        return $this->redirect(['action' => 'index']);
    }
}
