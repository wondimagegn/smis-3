<?php
namespace App\Controller;

use App\Controller\AppController;

class ExamRoomNumberOfInvigilatorsController extends AppController
{

    public function index()
    {
        $this->paginate = [
            'contain' => ['ClassRooms'],
        ];
        $examRoomNumberOfInvigilators = $this->paginate($this->ExamRoomNumberOfInvigilators);

        $this->set(compact('examRoomNumberOfInvigilators'));
    }


    public function view($id = null)
    {
        $examRoomNumberOfInvigilator = $this->ExamRoomNumberOfInvigilators->get($id, [
            'contain' => ['ClassRooms'],
        ]);

        $this->set('examRoomNumberOfInvigilator', $examRoomNumberOfInvigilator);
    }

    public function add()
    {
        $examRoomNumberOfInvigilator = $this->ExamRoomNumberOfInvigilators->newEntity();
        if ($this->request->is('post')) {
            $examRoomNumberOfInvigilator = $this->ExamRoomNumberOfInvigilators->patchEntity($examRoomNumberOfInvigilator, $this->request->getData());
            if ($this->ExamRoomNumberOfInvigilators->save($examRoomNumberOfInvigilator)) {
                $this->Flash->success(__('The exam room number of invigilator has been saved.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The exam room number of invigilator could not be saved. Please, try again.'));
        }
        $this->set(compact('examRoomNumberOfInvigilator'));
    }


    public function edit($id = null)
    {
        $examRoomNumberOfInvigilator = $this->ExamRoomNumberOfInvigilators->get($id, [
            'contain' => [],
        ]);
        if ($this->request->is(['patch', 'post', 'put'])) {
            $examRoomNumberOfInvigilator = $this->ExamRoomNumberOfInvigilators->patchEntity($examRoomNumberOfInvigilator, $this->request->getData());
            if ($this->ExamRoomNumberOfInvigilators->save($examRoomNumberOfInvigilator)) {
                $this->Flash->success(__('The exam room number of invigilator has been saved.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The exam room number of invigilator could not be saved. Please, try again.'));
        }
       $this->set(compact('examRoomNumberOfInvigilator'));
    }


    public function delete($id = null)
    {
        $this->request->allowMethod(['post', 'delete']);
        $examRoomNumberOfInvigilator = $this->ExamRoomNumberOfInvigilators->get($id);
        if ($this->ExamRoomNumberOfInvigilators->delete($examRoomNumberOfInvigilator)) {
            $this->Flash->success(__('The exam room number of invigilator has been deleted.'));
        } else {
            $this->Flash->error(__('The exam room number of invigilator could not be deleted. Please, try again.'));
        }

        return $this->redirect(['action' => 'index']);
    }
}
