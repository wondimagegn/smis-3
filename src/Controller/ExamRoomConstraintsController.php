<?php
namespace App\Controller;

use App\Controller\AppController;

class ExamRoomConstraintsController extends AppController
{

    public function index()
    {
        $this->paginate = [
            'contain' => ['ClassRooms'],
        ];
        $examRoomConstraints = $this->paginate($this->ExamRoomConstraints);

        $this->set(compact('examRoomConstraints'));
    }


    public function view($id = null)
    {
        $examRoomConstraint = $this->ExamRoomConstraints->get($id, [
            'contain' => ['ClassRooms'],
        ]);

        $this->set('examRoomConstraint', $examRoomConstraint);
    }

    public function add()
    {
        $examRoomConstraint = $this->ExamRoomConstraints->newEntity();
        if ($this->request->is('post')) {
            $examRoomConstraint = $this->ExamRoomConstraints->patchEntity($examRoomConstraint, $this->request->getData());
            if ($this->ExamRoomConstraints->save($examRoomConstraint)) {
                $this->Flash->success(__('The exam room constraint has been saved.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The exam room constraint could not be saved. Please, try again.'));
        }
      $this->set(compact('examRoomConstraint'));
    }


    public function edit($id = null)
    {
        $examRoomConstraint = $this->ExamRoomConstraints->get($id, [
            'contain' => [],
        ]);
        if ($this->request->is(['patch', 'post', 'put'])) {
            $examRoomConstraint = $this->ExamRoomConstraints->patchEntity($examRoomConstraint, $this->request->getData());
            if ($this->ExamRoomConstraints->save($examRoomConstraint)) {
                $this->Flash->success(__('The exam room constraint has been saved.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The exam room constraint could not be saved. Please, try again.'));
        }

        $this->set(compact('examRoomConstraint'));
    }

    public function delete($id = null)
    {
        $this->request->allowMethod(['post', 'delete']);
        $examRoomConstraint = $this->ExamRoomConstraints->get($id);
        if ($this->ExamRoomConstraints->delete($examRoomConstraint)) {
            $this->Flash->success(__('The exam room constraint has been deleted.'));
        } else {
            $this->Flash->error(__('The exam room constraint could not be deleted. Please, try again.'));
        }

        return $this->redirect(['action' => 'index']);
    }
}
