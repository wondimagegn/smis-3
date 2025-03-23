<?php

namespace App\Controller;

use Cake\Event\Event;

class EheeceResultsController extends AppController
{

    public $paginate = [];

    public function initialize()
    {

        parent::initialize();
        $this->loadComponent('AcademicYear');
        $this->loadComponent('Paginator'); // Ensure Paginator is loaded

        $this->viewBuilder()->setHelpers(['DatePicker']);
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
        $eheeceResults = $this->paginate($this->EheeceResults);

        $this->set(compact('eheeceResults'));
    }


    public function view($id = null)
    {
        $eheeceResult = $this->EheeceResults->get($id, [
            'contain' => ['Students'],
        ]);

        $this->set('eheeceResult', $eheeceResult);
    }


    public function add()
    {
        $eheeceResult = $this->EheeceResults->newEntity();
        if ($this->request->is('post')) {
            $eheeceResult = $this->EheeceResults->patchEntity($eheeceResult, $this->request->getData());
            if ($this->EheeceResults->save($eheeceResult)) {
                $this->Flash->success(__('The eheece result has been saved.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The eheece result could not be saved. Please, try again.'));
        }
        $this->set(compact('eheeceResult'));
    }

    public function edit($id = null)
    {
        $eheeceResult = $this->EheeceResults->get($id, [
            'contain' => [],
        ]);
        if ($this->request->is(['patch', 'post', 'put'])) {
            $eheeceResult = $this->EheeceResults->patchEntity($eheeceResult, $this->request->getData());
            if ($this->EheeceResults->save($eheeceResult)) {
                $this->Flash->success(__('The eheece result has been saved.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The eheece result could not be saved. Please, try again.'));
        }

        $this->set(compact('eheeceResult'));
    }


    public function delete($id = null)
    {
        $this->request->allowMethod(['post', 'delete']);
        $eheeceResult = $this->EheeceResults->get($id);
        if ($this->EheeceResults->delete($eheeceResult)) {
            $this->Flash->success(__('The eheece result has been deleted.'));
        } else {
            $this->Flash->error(__('The eheece result could not be deleted. Please, try again.'));
        }

        return $this->redirect(['action' => 'index']);
    }
}
