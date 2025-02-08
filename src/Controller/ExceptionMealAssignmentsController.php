<?php
namespace App\Controller;

use App\Controller\AppController;

class ExceptionMealAssignmentsController extends AppController
{

    public function index()
    {
        $this->paginate = [
            'contain' => ['Students', 'MealHalls'],
        ];
        $exceptionMealAssignments = $this->paginate($this->ExceptionMealAssignments);

        $this->set(compact('exceptionMealAssignments'));
    }


    public function view($id = null)
    {
        $exceptionMealAssignment = $this->ExceptionMealAssignments->get($id, [
            'contain' => ['Students', 'MealHalls'],
        ]);

        $this->set('exceptionMealAssignment', $exceptionMealAssignment);
    }

    public function add()
    {
        $exceptionMealAssignment = $this->ExceptionMealAssignments->newEntity();
        if ($this->request->is('post')) {
            $exceptionMealAssignment = $this->ExceptionMealAssignments->patchEntity($exceptionMealAssignment, $this->request->getData());
            if ($this->ExceptionMealAssignments->save($exceptionMealAssignment)) {
                $this->Flash->success(__('The exception meal assignment has been saved.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The exception meal assignment could not be saved. Please, try again.'));
        }

        $this->set(compact('exceptionMealAssignment'));
    }


    public function edit($id = null)
    {
        $exceptionMealAssignment = $this->ExceptionMealAssignments->get($id, [
            'contain' => [],
        ]);
        if ($this->request->is(['patch', 'post', 'put'])) {
            $exceptionMealAssignment = $this->ExceptionMealAssignments->patchEntity($exceptionMealAssignment, $this->request->getData());
            if ($this->ExceptionMealAssignments->save($exceptionMealAssignment)) {
                $this->Flash->success(__('The exception meal assignment has been saved.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The exception meal assignment could not be saved. Please, try again.'));
        }

        $this->set(compact('exceptionMealAssignment'));
    }


    public function delete($id = null)
    {
        $this->request->allowMethod(['post', 'delete']);
        $exceptionMealAssignment = $this->ExceptionMealAssignments->get($id);
        if ($this->ExceptionMealAssignments->delete($exceptionMealAssignment)) {
            $this->Flash->success(__('The exception meal assignment has been deleted.'));
        } else {
            $this->Flash->error(__('The exception meal assignment could not be deleted. Please, try again.'));
        }

        return $this->redirect(['action' => 'index']);
    }
}
