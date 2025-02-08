<?php
namespace App\Controller;

use App\Controller\AppController;

class DormitoriesController extends AppController
{

    public function index()
    {
        $this->paginate = [
            'contain' => ['DormitoryBlocks'],
        ];
        $dormitories = $this->paginate($this->Dormitories);

        $this->set(compact('dormitories'));
    }


    public function view($id = null)
    {
        $dormitory = $this->Dormitories->get($id, [
            'contain' => ['DormitoryBlocks', 'DormitoryAssignments'],
        ]);

        $this->set('dormitory', $dormitory);
    }


    public function add()
    {
        $dormitory = $this->Dormitories->newEntity();
        if ($this->request->is('post')) {
            $dormitory = $this->Dormitories->patchEntity($dormitory, $this->request->getData());
            if ($this->Dormitories->save($dormitory)) {
                $this->Flash->success(__('The dormitory has been saved.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The dormitory could not be saved. Please, try again.'));
        }
        $dormitoryBlocks = $this->Dormitories->DormitoryBlocks->find('list', ['limit' => 200]);
        $this->set(compact('dormitory', 'dormitoryBlocks'));
    }


    public function edit($id = null)
    {
        $dormitory = $this->Dormitories->get($id, [
            'contain' => [],
        ]);
        if ($this->request->is(['patch', 'post', 'put'])) {
            $dormitory = $this->Dormitories->patchEntity($dormitory, $this->request->getData());
            if ($this->Dormitories->save($dormitory)) {
                $this->Flash->success(__('The dormitory has been saved.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The dormitory could not be saved. Please, try again.'));
        }

        $this->set(compact('dormitory'));
    }


    public function delete($id = null)
    {
        $this->request->allowMethod(['post', 'delete']);
        $dormitory = $this->Dormitories->get($id);
        if ($this->Dormitories->delete($dormitory)) {
            $this->Flash->success(__('The dormitory has been deleted.'));
        } else {
            $this->Flash->error(__('The dormitory could not be deleted. Please, try again.'));
        }

        return $this->redirect(['action' => 'index']);
    }
}
