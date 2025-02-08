<?php
namespace App\Controller;

use App\Controller\AppController;


class ClassRoomCourseConstraintsController extends AppController
{

    public function index()
    {
        $this->paginate = [
            'contain' => ['PublishedCourses', 'ClassRooms'],
        ];
        $classRoomCourseConstraints = $this->paginate($this->ClassRoomCourseConstraints);

        $this->set(compact('classRoomCourseConstraints'));
    }

    public function view($id = null)
    {
        $classRoomCourseConstraint = $this->ClassRoomCourseConstraints->get($id, [
            'contain' => ['PublishedCourses', 'ClassRooms'],
        ]);

        $this->set('classRoomCourseConstraint', $classRoomCourseConstraint);
    }

    public function add()
    {
        $classRoomCourseConstraint = $this->ClassRoomCourseConstraints->newEntity();
        if ($this->request->is('post')) {
            $classRoomCourseConstraint = $this->ClassRoomCourseConstraints->patchEntity($classRoomCourseConstraint, $this->request->getData());
            if ($this->ClassRoomCourseConstraints->save($classRoomCourseConstraint)) {
                $this->Flash->success(__('The class room course constraint has been saved.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The class room course constraint could not be saved. Please, try again.'));
        }
        $publishedCourses = $this->ClassRoomCourseConstraints->PublishedCourses->find('list', ['limit' => 200]);
        $classRooms = $this->ClassRoomCourseConstraints->ClassRooms->find('list', ['limit' => 200]);
        $this->set(compact('classRoomCourseConstraint', 'publishedCourses', 'classRooms'));
    }


    public function edit($id = null)
    {
        $classRoomCourseConstraint = $this->ClassRoomCourseConstraints->get($id, [
            'contain' => [],
        ]);
        if ($this->request->is(['patch', 'post', 'put'])) {
            $classRoomCourseConstraint = $this->ClassRoomCourseConstraints->patchEntity($classRoomCourseConstraint, $this->request->getData());
            if ($this->ClassRoomCourseConstraints->save($classRoomCourseConstraint)) {
                $this->Flash->success(__('The class room course constraint has been saved.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The class room course constraint could not be saved. Please, try again.'));
        }
        $publishedCourses = $this->ClassRoomCourseConstraints->PublishedCourses->find('list', ['limit' => 200]);
        $classRooms = $this->ClassRoomCourseConstraints->ClassRooms->find('list', ['limit' => 200]);
        $this->set(compact('classRoomCourseConstraint', 'publishedCourses', 'classRooms'));
    }

    public function delete($id = null)
    {
        $this->request->allowMethod(['post', 'delete']);
        $classRoomCourseConstraint = $this->ClassRoomCourseConstraints->get($id);
        if ($this->ClassRoomCourseConstraints->delete($classRoomCourseConstraint)) {
            $this->Flash->success(__('The class room course constraint has been deleted.'));
        } else {
            $this->Flash->error(__('The class room course constraint could not be deleted. Please, try again.'));
        }

        return $this->redirect(['action' => 'index']);
    }
}
