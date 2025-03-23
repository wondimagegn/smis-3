<?php

namespace App\Controller;

use App\Controller\AppController;

use Cake\Event\Event;
use Cake\ORM\TableRegistry;
use Cake\Core\Configure;

class StudentNameHistoriesController extends AppController
{

    public $paginate = [];

    public function initialize()
    {

        parent::initialize();
        $this->loadComponent('Paginator'); // Ensure Paginator is loaded

    }

    public function beforeFilter(Event $event)
    {

        parent::beforeFilter($event);
    }
    public function index()
    {
        $this->paginate = [
            'contain' => ['Students'],
        ];
        $studentNameHistories = $this->paginate($this->StudentNameHistories);

        $this->set(compact('studentNameHistories'));
    }

    public function view($id = null)
    {
        $studentNameHistory = $this->StudentNameHistories->get($id, [
            'contain' => ['Students'],
        ]);

        $this->set('studentNameHistory', $studentNameHistory);
    }

    public function add()
    {
        $studentNameHistory = $this->StudentNameHistories->newEntity();
        if ($this->request->is('post')) {
            $studentNameHistory = $this->StudentNameHistories->patchEntity($studentNameHistory, $this->request->getData());
            if ($this->StudentNameHistories->save($studentNameHistory)) {
                $this->Flash->success(__('The student name history has been saved.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The student name history could not be saved. Please, try again.'));
        }
        $this->set(compact('studentNameHistory', 'students'));
    }

    public function edit($id = null)
    {
        $studentNameHistory = $this->StudentNameHistories->get($id, [
            'contain' => [],
        ]);
        if ($this->request->is(['patch', 'post', 'put'])) {
            $studentNameHistory = $this->StudentNameHistories->patchEntity($studentNameHistory, $this->request->getData());
            if ($this->StudentNameHistories->save($studentNameHistory)) {
                $this->Flash->success(__('The student name history has been saved.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The student name history could not be saved. Please, try again.'));
        }
        $this->set(compact('studentNameHistory'));
    }

    public function delete($id = null)
    {
        $this->request->allowMethod(['post', 'delete']);
        $studentNameHistory = $this->StudentNameHistories->get($id);
        if ($this->StudentNameHistories->delete($studentNameHistory)) {
            $this->Flash->success(__('The student name history has been deleted.'));
        } else {
            $this->Flash->error(__('The student name history could not be deleted. Please, try again.'));
        }

        return $this->redirect(['action' => 'index']);
    }
}
