<?php
namespace App\Controller;

use App\Controller\AppController;

class MedicalHistoriesController extends AppController
{

    public function index()
    {
        $this->paginate = [
            'contain' => ['Students', 'Users'],
        ];
        $medicalHistories = $this->paginate($this->MedicalHistories);

        $this->set(compact('medicalHistories'));
    }


    public function view($id = null)
    {
        $medicalHistory = $this->MedicalHistories->get($id, [
            'contain' => ['Students', 'Users'],
        ]);

        $this->set('medicalHistory', $medicalHistory);
    }


    public function add()
    {
        $medicalHistory = $this->MedicalHistories->newEntity();
        if ($this->request->is('post')) {
            $medicalHistory = $this->MedicalHistories->patchEntity($medicalHistory, $this->request->getData());
            if ($this->MedicalHistories->save($medicalHistory)) {
                $this->Flash->success(__('The medical history has been saved.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The medical history could not be saved. Please, try again.'));
        }

        $this->set(compact('medicalHistory'));
    }


    public function edit($id = null)
    {
        $medicalHistory = $this->MedicalHistories->get($id, [
            'contain' => [],
        ]);
        if ($this->request->is(['patch', 'post', 'put'])) {
            $medicalHistory = $this->MedicalHistories->patchEntity($medicalHistory, $this->request->getData());
            if ($this->MedicalHistories->save($medicalHistory)) {
                $this->Flash->success(__('The medical history has been saved.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The medical history could not be saved. Please, try again.'));
        }
        $this->set(compact('medicalHistory'));
    }


    public function delete($id = null)
    {
        $this->request->allowMethod(['post', 'delete']);
        $medicalHistory = $this->MedicalHistories->get($id);
        if ($this->MedicalHistories->delete($medicalHistory)) {
            $this->Flash->success(__('The medical history has been deleted.'));
        } else {
            $this->Flash->error(__('The medical history could not be deleted. Please, try again.'));
        }

        return $this->redirect(['action' => 'index']);
    }
}
