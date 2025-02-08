<?php
namespace App\Controller;

use App\Controller\AppController;

class TakenPropertiesController extends AppController
{

    public function index()
    {
        $this->paginate = [
            'contain' => ['Students', 'Offices', 'Colleges', 'Departments'],
        ];
        $takenProperties = $this->paginate($this->TakenProperties);

        $this->set(compact('takenProperties'));
    }

    public function view($id = null)
    {
        $takenProperty = $this->TakenProperties->get($id, [
            'contain' => ['Students', 'Offices', 'Colleges', 'Departments'],
        ]);

        $this->set('takenProperty', $takenProperty);
    }


    public function add()
    {
        $takenProperty = $this->TakenProperties->newEntity();
        if ($this->request->is('post')) {
            $takenProperty = $this->TakenProperties->patchEntity($takenProperty, $this->request->getData());
            if ($this->TakenProperties->save($takenProperty)) {
                $this->Flash->success(__('The taken property has been saved.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The taken property could not be saved. Please, try again.'));
        }
        $this->set(compact('takenProperty'));
    }

    public function edit($id = null)
    {
        $takenProperty = $this->TakenProperties->get($id, [
            'contain' => [],
        ]);
        if ($this->request->is(['patch', 'post', 'put'])) {
            $takenProperty = $this->TakenProperties->patchEntity($takenProperty, $this->request->getData());
            if ($this->TakenProperties->save($takenProperty)) {
                $this->Flash->success(__('The taken property has been saved.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The taken property could not be saved. Please, try again.'));
        }
        $this->set(compact('takenProperty'));
    }

    public function delete($id = null)
    {
        $this->request->allowMethod(['post', 'delete']);
        $takenProperty = $this->TakenProperties->get($id);
        if ($this->TakenProperties->delete($takenProperty)) {
            $this->Flash->success(__('The taken property has been deleted.'));
        } else {
            $this->Flash->error(__('The taken property could not be deleted. Please, try again.'));
        }

        return $this->redirect(['action' => 'index']);
    }
}
