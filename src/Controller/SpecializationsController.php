<?php
namespace App\Controller;

use App\Controller\AppController;

class SpecializationsController extends AppController
{

    public function index()
    {
        $this->paginate = [
            'contain' => ['Departments'],
        ];
        $specializations = $this->paginate($this->Specializations);

        $this->set(compact('specializations'));
    }

    public function view($id = null)
    {
        $specialization = $this->Specializations->get($id, [
            'contain' => ['Departments', 'AcceptedStudents', 'Students'],
        ]);

        $this->set('specialization', $specialization);
    }

    public function add()
    {
        $specialization = $this->Specializations->newEntity();
        if ($this->request->is('post')) {
            $specialization = $this->Specializations->patchEntity($specialization, $this->request->getData());
            if ($this->Specializations->save($specialization)) {
                $this->Flash->success(__('The specialization has been saved.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The specialization could not be saved. Please, try again.'));
        }
        $this->set(compact('specialization'));
    }

    public function edit($id = null)
    {
        $specialization = $this->Specializations->get($id, [
            'contain' => [],
        ]);
        if ($this->request->is(['patch', 'post', 'put'])) {
            $specialization = $this->Specializations->patchEntity($specialization, $this->request->getData());
            if ($this->Specializations->save($specialization)) {
                $this->Flash->success(__('The specialization has been saved.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The specialization could not be saved. Please, try again.'));
        }
        $this->set(compact('specialization'));
    }

    public function delete($id = null)
    {
        $this->request->allowMethod(['post', 'delete']);
        $specialization = $this->Specializations->get($id);
        if ($this->Specializations->delete($specialization)) {
            $this->Flash->success(__('The specialization has been deleted.'));
        } else {
            $this->Flash->error(__('The specialization could not be deleted. Please, try again.'));
        }

        return $this->redirect(['action' => 'index']);
    }
}
