<?php
namespace App\Controller;

use App\Controller\AppController;

class MealHallsController extends AppController
{

    public function index()
    {
        $this->paginate = [
            'contain' => ['Campuses'],
        ];
        $mealHalls = $this->paginate($this->MealHalls);

        $this->set(compact('mealHalls'));
    }


    public function view($id = null)
    {
        $mealHall = $this->MealHalls->get($id, [
            'contain' => ['Campuses', 'ExceptionMealAssignments', 'MealHallAssignments', 'UserMealAssignments'],
        ]);

        $this->set('mealHall', $mealHall);
    }

    public function add()
    {
        $mealHall = $this->MealHalls->newEntity();
        if ($this->request->is('post')) {
            $mealHall = $this->MealHalls->patchEntity($mealHall, $this->request->getData());
            if ($this->MealHalls->save($mealHall)) {
                $this->Flash->success(__('The meal hall has been saved.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The meal hall could not be saved. Please, try again.'));
        }
        $this->set(compact('mealHall'));
    }

    public function edit($id = null)
    {
        $mealHall = $this->MealHalls->get($id, [
            'contain' => [],
        ]);
        if ($this->request->is(['patch', 'post', 'put'])) {
            $mealHall = $this->MealHalls->patchEntity($mealHall, $this->request->getData());
            if ($this->MealHalls->save($mealHall)) {
                $this->Flash->success(__('The meal hall has been saved.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The meal hall could not be saved. Please, try again.'));
        }
        $this->set(compact('mealHall'));
    }

    public function delete($id = null)
    {
        $this->request->allowMethod(['post', 'delete']);
        $mealHall = $this->MealHalls->get($id);
        if ($this->MealHalls->delete($mealHall)) {
            $this->Flash->success(__('The meal hall has been deleted.'));
        } else {
            $this->Flash->error(__('The meal hall could not be deleted. Please, try again.'));
        }

        return $this->redirect(['action' => 'index']);
    }
}
