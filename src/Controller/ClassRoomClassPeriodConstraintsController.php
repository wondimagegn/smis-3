<?php
namespace App\Controller;

use App\Controller\AppController;

class ClassRoomClassPeriodConstraintsController extends AppController
{

    public function index()
    {
        $this->paginate = [
            'contain' => ['ClassRooms', 'ClassPeriods'],
        ];
        $classRoomClassPeriodConstraints = $this->paginate($this->ClassRoomClassPeriodConstraints);

        $this->set(compact('classRoomClassPeriodConstraints'));
    }

    public function view($id = null)
    {
        $classRoomClassPeriodConstraint = $this->ClassRoomClassPeriodConstraints->get($id, [
            'contain' => ['ClassRooms', 'ClassPeriods'],
        ]);

        $this->set('classRoomClassPeriodConstraint', $classRoomClassPeriodConstraint);
    }

    public function add()
    {
        $classRoomClassPeriodConstraint = $this->ClassRoomClassPeriodConstraints->newEntity();
        if ($this->request->is('post')) {
            $classRoomClassPeriodConstraint = $this->ClassRoomClassPeriodConstraints->patchEntity($classRoomClassPeriodConstraint, $this->request->getData());
            if ($this->ClassRoomClassPeriodConstraints->save($classRoomClassPeriodConstraint)) {
                $this->Flash->success(__('The class room class period constraint has been saved.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The class room class period constraint could not be saved. Please, try again.'));
        }
        $classRooms = $this->ClassRoomClassPeriodConstraints->ClassRooms->find('list', ['limit' => 200]);
        $classPeriods = $this->ClassRoomClassPeriodConstraints->ClassPeriods->find('list', ['limit' => 200]);
        $this->set(compact('classRoomClassPeriodConstraint', 'classRooms', 'classPeriods'));
    }


    public function edit($id = null)
    {
        $classRoomClassPeriodConstraint = $this->ClassRoomClassPeriodConstraints->get($id, [
            'contain' => [],
        ]);
        if ($this->request->is(['patch', 'post', 'put'])) {
            $classRoomClassPeriodConstraint = $this->ClassRoomClassPeriodConstraints->patchEntity($classRoomClassPeriodConstraint, $this->request->getData());
            if ($this->ClassRoomClassPeriodConstraints->save($classRoomClassPeriodConstraint)) {
                $this->Flash->success(__('The class room class period constraint has been saved.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The class room class period constraint could not be saved. Please, try again.'));
        }
        $classRooms = $this->ClassRoomClassPeriodConstraints->ClassRooms->find('list', ['limit' => 200]);
        $classPeriods = $this->ClassRoomClassPeriodConstraints->ClassPeriods->find('list', ['limit' => 200]);
        $this->set(compact('classRoomClassPeriodConstraint', 'classRooms', 'classPeriods'));
    }

    public function delete($id = null)
    {
        $this->request->allowMethod(['post', 'delete']);
        $classRoomClassPeriodConstraint = $this->ClassRoomClassPeriodConstraints->get($id);
        if ($this->ClassRoomClassPeriodConstraints->delete($classRoomClassPeriodConstraint)) {
            $this->Flash->success(__('The class room class period constraint has been deleted.'));
        } else {
            $this->Flash->error(__('The class room class period constraint could not be deleted. Please, try again.'));
        }

        return $this->redirect(['action' => 'index']);
    }
}
