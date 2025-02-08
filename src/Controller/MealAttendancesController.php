<?php
namespace App\Controller;

use App\Controller\AppController;

class MealAttendancesController extends AppController
{

    public function index()
    {
        $this->paginate = [
            'contain' => ['MealTypes', 'Students'],
        ];
        $mealAttendances = $this->paginate($this->MealAttendances);

        $this->set(compact('mealAttendances'));
    }

    public function view($id = null)
    {
        $mealAttendance = $this->MealAttendances->get($id, [
            'contain' => ['MealTypes', 'Students'],
        ]);

        $this->set('mealAttendance', $mealAttendance);
    }

    public function add()
    {
        $mealAttendance = $this->MealAttendances->newEntity();
        if ($this->request->is('post')) {
            $mealAttendance = $this->MealAttendances->patchEntity($mealAttendance, $this->request->getData());
            if ($this->MealAttendances->save($mealAttendance)) {
                $this->Flash->success(__('The meal attendance has been saved.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The meal attendance could not be saved. Please, try again.'));
        }
        $this->set(compact('mealAttendance'));
    }

    public function edit($id = null)
    {
        $mealAttendance = $this->MealAttendances->get($id, [
            'contain' => [],
        ]);
        if ($this->request->is(['patch', 'post', 'put'])) {
            $mealAttendance = $this->MealAttendances->patchEntity($mealAttendance, $this->request->getData());
            if ($this->MealAttendances->save($mealAttendance)) {
                $this->Flash->success(__('The meal attendance has been saved.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The meal attendance could not be saved. Please, try again.'));
        }

        $this->set(compact('mealAttendance'));
    }


    public function delete($id = null)
    {
        $this->request->allowMethod(['post', 'delete']);
        $mealAttendance = $this->MealAttendances->get($id);
        if ($this->MealAttendances->delete($mealAttendance)) {
            $this->Flash->success(__('The meal attendance has been deleted.'));
        } else {
            $this->Flash->error(__('The meal attendance could not be deleted. Please, try again.'));
        }

        return $this->redirect(['action' => 'index']);
    }
}
