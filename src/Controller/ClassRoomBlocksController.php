<?php
namespace App\Controller;

use App\Controller\AppController;

class ClassRoomBlocksController extends AppController
{

    public function index()
    {
        $this->paginate = [
            'contain' => ['Colleges', 'Campuses'],
        ];
        $classRoomBlocks = $this->paginate($this->ClassRoomBlocks);

        $this->set(compact('classRoomBlocks'));
    }


    public function view($id = null)
    {
        $classRoomBlock = $this->ClassRoomBlocks->get($id, [
            'contain' => ['Colleges', 'Campuses', 'ClassRooms'],
        ]);

        $this->set('classRoomBlock', $classRoomBlock);
    }


    public function add()
    {
        $classRoomBlock = $this->ClassRoomBlocks->newEntity();
        if ($this->request->is('post')) {
            $classRoomBlock = $this->ClassRoomBlocks->patchEntity($classRoomBlock, $this->request->getData());
            if ($this->ClassRoomBlocks->save($classRoomBlock)) {
                $this->Flash->success(__('The class room block has been saved.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The class room block could not be saved. Please, try again.'));
        }
        $colleges = $this->ClassRoomBlocks->Colleges->find('list', ['limit' => 200]);
        $campuses = $this->ClassRoomBlocks->Campuses->find('list', ['limit' => 200]);
        $this->set(compact('classRoomBlock', 'colleges', 'campuses'));
    }

    public function edit($id = null)
    {
        $classRoomBlock = $this->ClassRoomBlocks->get($id, [
            'contain' => [],
        ]);
        if ($this->request->is(['patch', 'post', 'put'])) {
            $classRoomBlock = $this->ClassRoomBlocks->patchEntity($classRoomBlock, $this->request->getData());
            if ($this->ClassRoomBlocks->save($classRoomBlock)) {
                $this->Flash->success(__('The class room block has been saved.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The class room block could not be saved. Please, try again.'));
        }
        $colleges = $this->ClassRoomBlocks->Colleges->find('list', ['limit' => 200]);
        $campuses = $this->ClassRoomBlocks->Campuses->find('list', ['limit' => 200]);
        $this->set(compact('classRoomBlock', 'colleges', 'campuses'));
    }


    public function delete($id = null)
    {
        $this->request->allowMethod(['post', 'delete']);
        $classRoomBlock = $this->ClassRoomBlocks->get($id);
        if ($this->ClassRoomBlocks->delete($classRoomBlock)) {
            $this->Flash->success(__('The class room block has been deleted.'));
        } else {
            $this->Flash->error(__('The class room block could not be deleted. Please, try again.'));
        }

        return $this->redirect(['action' => 'index']);
    }
}
