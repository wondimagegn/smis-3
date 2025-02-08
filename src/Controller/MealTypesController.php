<?php
namespace App\Controller;

use App\Controller\AppController;

class MealTypesController extends AppController
{

    public function index()
    {
        $mealTypes = $this->paginate($this->MealTypes);

        $this->set(compact('mealTypes'));
    }

    public function view($id = null)
    {
        $mealType = $this->MealTypes->get($id, [
            'contain' => ['MealAttendances'],
        ]);

        $this->set('mealType', $mealType);
    }

    public function add()
    {
        $mealType = $this->MealTypes->newEntity();
        if ($this->request->is('post')) {
            $mealType = $this->MealTypes->patchEntity($mealType, $this->request->getData());
            if ($this->MealTypes->save($mealType)) {
                $this->Flash->success(__('The meal type has been saved.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The meal type could not be saved. Please, try again.'));
        }
        $this->set(compact('mealType'));
    }


    public function edit($id = null)
    {
        $mealType = $this->MealTypes->get($id, [
            'contain' => [],
        ]);
        if ($this->request->is(['patch', 'post', 'put'])) {
            $mealType = $this->MealTypes->patchEntity($mealType, $this->request->getData());
            if ($this->MealTypes->save($mealType)) {
                $this->Flash->success(__('The meal type has been saved.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The meal type could not be saved. Please, try again.'));
        }
        $this->set(compact('mealType'));
    }


    public function delete($id = null)
    {
        $this->request->allowMethod(['post', 'delete']);
        $mealType = $this->MealTypes->get($id);
        if ($this->MealTypes->delete($mealType)) {
            $this->Flash->success(__('The meal type has been deleted.'));
        } else {
            $this->Flash->error(__('The meal type could not be deleted. Please, try again.'));
        }

        return $this->redirect(['action' => 'index']);
    }
}
