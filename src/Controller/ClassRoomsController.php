<?php
namespace App\Controller;

use App\Controller\AppController;

class ClassRoomsController extends AppController
{

    public function index()
    {
        $this->paginate = [
            'contain' => ['ClassRoomBlocks'],
        ];
        $classRooms = $this->paginate($this->ClassRooms);

        $this->set(compact('classRooms'));
    }


    public function view($id = null)
    {
        $classRoom = $this->ClassRooms->get($id, [
            'contain' => ['ClassRoomBlocks', 'ClassRoomClassPeriodConstraints', 'ClassRoomCourseConstraints', 'CourseSchedules', 'ExamRoomConstraints', 'ExamRoomCourseConstraints', 'ExamRoomNumberOfInvigilators', 'ExamSchedules', 'ProgramProgramTypeClassRooms'],
        ]);

        $this->set('classRoom', $classRoom);
    }

    public function add()
    {
        $classRoom = $this->ClassRooms->newEntity();
        if ($this->request->is('post')) {
            $classRoom = $this->ClassRooms->patchEntity($classRoom, $this->request->getData());
            if ($this->ClassRooms->save($classRoom)) {
                $this->Flash->success(__('The class room has been saved.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The class room could not be saved. Please, try again.'));
        }
        $classRoomBlocks = $this->ClassRooms->ClassRoomBlocks->find('list', ['limit' => 200]);
        $this->set(compact('classRoom', 'classRoomBlocks'));
    }

    public function edit($id = null)
    {
        $classRoom = $this->ClassRooms->get($id, [
            'contain' => [],
        ]);
        if ($this->request->is(['patch', 'post', 'put'])) {
            $classRoom = $this->ClassRooms->patchEntity($classRoom, $this->request->getData());
            if ($this->ClassRooms->save($classRoom)) {
                $this->Flash->success(__('The class room has been saved.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The class room could not be saved. Please, try again.'));
        }
        $classRoomBlocks = $this->ClassRooms->ClassRoomBlocks->find('list', ['limit' => 200]);
        $this->set(compact('classRoom', 'classRoomBlocks'));
    }

    public function delete($id = null)
    {
        $this->request->allowMethod(['post', 'delete']);
        $classRoom = $this->ClassRooms->get($id);
        if ($this->ClassRooms->delete($classRoom)) {
            $this->Flash->success(__('The class room has been deleted.'));
        } else {
            $this->Flash->error(__('The class room could not be deleted. Please, try again.'));
        }

        return $this->redirect(['action' => 'index']);
    }
}
