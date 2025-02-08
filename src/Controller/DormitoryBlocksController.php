<?php
namespace App\Controller;

use App\Controller\AppController;

class DormitoryBlocksController extends AppController
{

    public function index()
    {
        $this->paginate = [
            'contain' => ['Campuses'],
        ];
        $dormitoryBlocks = $this->paginate($this->DormitoryBlocks);

        $this->set(compact('dormitoryBlocks'));
    }


    public function view($id = null)
    {
        $dormitoryBlock = $this->DormitoryBlocks->get($id, [
            'contain' => ['Campuses', 'Dormitories', 'UserDormAssignments'],
        ]);

        $this->set('dormitoryBlock', $dormitoryBlock);
    }

    public function add()
    {
        $dormitoryBlock = $this->DormitoryBlocks->newEntity();
        if ($this->request->is('post')) {
            $dormitoryBlock = $this->DormitoryBlocks->patchEntity($dormitoryBlock, $this->request->getData());
            if ($this->DormitoryBlocks->save($dormitoryBlock)) {
                $this->Flash->success(__('The dormitory block has been saved.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The dormitory block could not be saved. Please, try again.'));
        }
        $this->set(compact('dormitoryBlock'));
    }

    public function edit($id = null)
    {
        $dormitoryBlock = $this->DormitoryBlocks->get($id, [
            'contain' => [],
        ]);
        if ($this->request->is(['patch', 'post', 'put'])) {
            $dormitoryBlock = $this->DormitoryBlocks->patchEntity($dormitoryBlock, $this->request->getData());
            if ($this->DormitoryBlocks->save($dormitoryBlock)) {
                $this->Flash->success(__('The dormitory block has been saved.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The dormitory block could not be saved. Please, try again.'));
        }

        $this->set(compact('dormitoryBlock'));
    }


    public function delete($id = null)
    {
        $this->request->allowMethod(['post', 'delete']);
        $dormitoryBlock = $this->DormitoryBlocks->get($id);
        if ($this->DormitoryBlocks->delete($dormitoryBlock)) {
            $this->Flash->success(__('The dormitory block has been deleted.'));
        } else {
            $this->Flash->error(__('The dormitory block could not be deleted. Please, try again.'));
        }

        return $this->redirect(['action' => 'index']);
    }
}
