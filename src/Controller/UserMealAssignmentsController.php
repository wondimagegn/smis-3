<?php

namespace App\Controller;

use Cake\Event\Event;

class UserMealAssignmentsController extends AppController
{

    public $paginate = [];

    public function initialize()
    {

        parent::initialize();
        $this->loadComponent('AcademicYear');
        $this->loadComponent('Paginator'); // Ensure Paginator is loaded

    }

    public function beforeFilter(Event $event)
    {

        parent::beforeFilter($event);
    }
    public function index()
    {
        $this->paginate = [
            'contain' => ['Users', 'MealHalls'],
        ];
        $userMealAssignments = $this->paginate($this->UserMealAssignments);

        $this->set(compact('userMealAssignments'));
    }


    public function view($id = null)
    {
        $userMealAssignment = $this->UserMealAssignments->get($id, [
            'contain' => ['Users', 'MealHalls'],
        ]);

        $this->set('userMealAssignment', $userMealAssignment);
    }

    public function add()
    {
        $userMealAssignment = $this->UserMealAssignments->newEntity();
        if ($this->request->is('post')) {
            $userMealAssignment = $this->UserMealAssignments->patchEntity($userMealAssignment, $this->request->getData());
            if ($this->UserMealAssignments->save($userMealAssignment)) {
                $this->Flash->success(__('The user meal assignment has been saved.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The user meal assignment could not be saved. Please, try again.'));
        }
        $this->set(compact('userMealAssignment'));
    }

    public function edit($id = null)
    {
        $userMealAssignment = $this->UserMealAssignments->get($id, [
            'contain' => [],
        ]);
        if ($this->request->is(['patch', 'post', 'put'])) {
            $userMealAssignment = $this->UserMealAssignments->patchEntity($userMealAssignment, $this->request->getData());
            if ($this->UserMealAssignments->save($userMealAssignment)) {
                $this->Flash->success(__('The user meal assignment has been saved.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The user meal assignment could not be saved. Please, try again.'));
        }
        $this->set(compact('userMealAssignment'));
    }

    public function delete($id = null)
    {
        $this->request->allowMethod(['post', 'delete']);
        $userMealAssignment = $this->UserMealAssignments->get($id);
        if ($this->UserMealAssignments->delete($userMealAssignment)) {
            $this->Flash->success(__('The user meal assignment has been deleted.'));
        } else {
            $this->Flash->error(__('The user meal assignment could not be deleted. Please, try again.'));
        }

        return $this->redirect(['action' => 'index']);
    }
}
