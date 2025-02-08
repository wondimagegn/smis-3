<?php
namespace App\Controller;

use App\Controller\AppController;

class DormitoryAssignmentsController extends AppController
{

    public function index()
    {
        $this->paginate = [
            'contain' => ['Dormitories', 'Students', 'AcceptedStudents'],
        ];
        $dormitoryAssignments = $this->paginate($this->DormitoryAssignments);

        $this->set(compact('dormitoryAssignments'));
    }


    public function view($id = null)
    {
        $dormitoryAssignment = $this->DormitoryAssignments->get($id, [
            'contain' => ['Dormitories', 'Students', 'AcceptedStudents'],
        ]);

        $this->set('dormitoryAssignment', $dormitoryAssignment);
    }


    public function add()
    {
        $dormitoryAssignment = $this->DormitoryAssignments->newEntity();
        if ($this->request->is('post')) {
            $dormitoryAssignment = $this->DormitoryAssignments->patchEntity($dormitoryAssignment, $this->request->getData());
            if ($this->DormitoryAssignments->save($dormitoryAssignment)) {
                $this->Flash->success(__('The dormitory assignment has been saved.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The dormitory assignment could not be saved. Please, try again.'));
        }
         $this->set(compact('dormitoryAssignment'));
    }


    public function edit($id = null)
    {
        $dormitoryAssignment = $this->DormitoryAssignments->get($id, [
            'contain' => [],
        ]);
        if ($this->request->is(['patch', 'post', 'put'])) {
            $dormitoryAssignment = $this->DormitoryAssignments->patchEntity($dormitoryAssignment, $this->request->getData());
            if ($this->DormitoryAssignments->save($dormitoryAssignment)) {
                $this->Flash->success(__('The dormitory assignment has been saved.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The dormitory assignment could not be saved. Please, try again.'));
        }
         $this->set(compact('dormitoryAssignment'));
    }


    public function delete($id = null)
    {
        $this->request->allowMethod(['post', 'delete']);
        $dormitoryAssignment = $this->DormitoryAssignments->get($id);
        if ($this->DormitoryAssignments->delete($dormitoryAssignment)) {
            $this->Flash->success(__('The dormitory assignment has been deleted.'));
        } else {
            $this->Flash->error(__('The dormitory assignment could not be deleted. Please, try again.'));
        }

        return $this->redirect(['action' => 'index']);
    }
}
