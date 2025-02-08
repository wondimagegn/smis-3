<?php
namespace App\Controller;

use App\Controller\AppController;

class ExamRoomCourseConstraintsController extends AppController
{

    public function index()
    {
        $this->paginate = [
            'contain' => ['ClassRooms', 'PublishedCourses'],
        ];
        $examRoomCourseConstraints = $this->paginate($this->ExamRoomCourseConstraints);

        $this->set(compact('examRoomCourseConstraints'));
    }


    public function view($id = null)
    {
        $examRoomCourseConstraint = $this->ExamRoomCourseConstraints->get($id, [
            'contain' => ['ClassRooms', 'PublishedCourses'],
        ]);

        $this->set('examRoomCourseConstraint', $examRoomCourseConstraint);
    }

    public function add()
    {
        $examRoomCourseConstraint = $this->ExamRoomCourseConstraints->newEntity();
        if ($this->request->is('post')) {
            $examRoomCourseConstraint = $this->ExamRoomCourseConstraints->patchEntity($examRoomCourseConstraint, $this->request->getData());
            if ($this->ExamRoomCourseConstraints->save($examRoomCourseConstraint)) {
                $this->Flash->success(__('The exam room course constraint has been saved.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The exam room course constraint could not be saved. Please, try again.'));
        }
       $this->set(compact('examRoomCourseConstraint'));
    }


    public function edit($id = null)
    {
        $examRoomCourseConstraint = $this->ExamRoomCourseConstraints->get($id, [
            'contain' => [],
        ]);
        if ($this->request->is(['patch', 'post', 'put'])) {
            $examRoomCourseConstraint = $this->ExamRoomCourseConstraints->patchEntity($examRoomCourseConstraint, $this->request->getData());
            if ($this->ExamRoomCourseConstraints->save($examRoomCourseConstraint)) {
                $this->Flash->success(__('The exam room course constraint has been saved.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The exam room course constraint could not be saved. Please, try again.'));
        }
        $this->set(compact('examRoomCourseConstraint'));
    }


    public function delete($id = null)
    {
        $this->request->allowMethod(['post', 'delete']);
        $examRoomCourseConstraint = $this->ExamRoomCourseConstraints->get($id);
        if ($this->ExamRoomCourseConstraints->delete($examRoomCourseConstraint)) {
            $this->Flash->success(__('The exam room course constraint has been deleted.'));
        } else {
            $this->Flash->error(__('The exam room course constraint could not be deleted. Please, try again.'));
        }

        return $this->redirect(['action' => 'index']);
    }
}
