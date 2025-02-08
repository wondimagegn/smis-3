<?php
namespace App\Controller;

use App\Controller\AppController;

class MealHallAssignmentsController extends AppController
{

    public function index()
    {
        $this->paginate = [
            'contain' => ['MealHalls', 'Students', 'AcceptedStudents'],
        ];
        $mealHallAssignments = $this->paginate($this->MealHallAssignments);

        $this->set(compact('mealHallAssignments'));
    }


    public function view($id = null)
    {
        $mealHallAssignment = $this->MealHallAssignments->get($id, [
            'contain' => ['MealHalls', 'Students', 'AcceptedStudents'],
        ]);

        $this->set('mealHallAssignment', $mealHallAssignment);
    }

    public function add()
    {
        $mealHallAssignment = $this->MealHallAssignments->newEntity();
        if ($this->request->is('post')) {
            $mealHallAssignment = $this->MealHallAssignments->patchEntity($mealHallAssignment, $this->request->getData());
            if ($this->MealHallAssignments->save($mealHallAssignment)) {
                $this->Flash->success(__('The meal hall assignment has been saved.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The meal hall assignment could not be saved. Please, try again.'));
        }
        $this->set(compact('mealHallAssignment'));
    }


    public function edit($id = null)
    {
        $mealHallAssignment = $this->MealHallAssignments->get($id, [
            'contain' => [],
        ]);
        if ($this->request->is(['patch', 'post', 'put'])) {
            $mealHallAssignment = $this->MealHallAssignments->patchEntity($mealHallAssignment, $this->request->getData());
            if ($this->MealHallAssignments->save($mealHallAssignment)) {
                $this->Flash->success(__('The meal hall assignment has been saved.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The meal hall assignment could not be saved. Please, try again.'));
        }
        $this->set(compact('mealHallAssignment'));
    }


    public function delete($id = null)
    {
        $this->request->allowMethod(['post', 'delete']);
        $mealHallAssignment = $this->MealHallAssignments->get($id);
        if ($this->MealHallAssignments->delete($mealHallAssignment)) {
            $this->Flash->success(__('The meal hall assignment has been deleted.'));
        } else {
            $this->Flash->error(__('The meal hall assignment could not be deleted. Please, try again.'));
        }

        return $this->redirect(['action' => 'index']);
    }
}
