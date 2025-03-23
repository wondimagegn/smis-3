<?php

namespace App\Controller;

use App\Controller\AppController;

use Cake\Event\Event;
use Cake\ORM\TableRegistry;
use Cake\Core\Configure;
class UserDormAssignmentsController extends AppController
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
            'contain' => ['Users', 'DormitoryBlocks'],
        ];
        $userDormAssignments = $this->paginate($this->UserDormAssignments);

        $this->set(compact('userDormAssignments'));
    }

    public function view($id = null)
    {
        $userDormAssignment = $this->UserDormAssignments->get($id, [
            'contain' => ['Users', 'DormitoryBlocks'],
        ]);

        $this->set('userDormAssignment', $userDormAssignment);
    }

    public function add()
    {
        $userDormAssignment = $this->UserDormAssignments->newEntity();
        if ($this->request->is('post')) {
            $userDormAssignment = $this->UserDormAssignments->patchEntity($userDormAssignment, $this->request->getData());
            if ($this->UserDormAssignments->save($userDormAssignment)) {
                $this->Flash->success(__('The user dorm assignment has been saved.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The user dorm assignment could not be saved. Please, try again.'));
        }
        $this->set(compact('userDormAssignment'));
    }

    public function edit($id = null)
    {
        $userDormAssignment = $this->UserDormAssignments->get($id, [
            'contain' => [],
        ]);
        if ($this->request->is(['patch', 'post', 'put'])) {
            $userDormAssignment = $this->UserDormAssignments->patchEntity($userDormAssignment, $this->request->getData());
            if ($this->UserDormAssignments->save($userDormAssignment)) {
                $this->Flash->success(__('The user dorm assignment has been saved.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The user dorm assignment could not be saved. Please, try again.'));
        }
        $this->set(compact('userDormAssignment'));
    }

    public function delete($id = null)
    {
        $this->request->allowMethod(['post', 'delete']);
        $userDormAssignment = $this->UserDormAssignments->get($id);
        if ($this->UserDormAssignments->delete($userDormAssignment)) {
            $this->Flash->success(__('The user dorm assignment has been deleted.'));
        } else {
            $this->Flash->error(__('The user dorm assignment could not be deleted. Please, try again.'));
        }

        return $this->redirect(['action' => 'index']);
    }
}
