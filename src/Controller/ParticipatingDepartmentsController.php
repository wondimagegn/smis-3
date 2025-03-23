<?php

namespace App\Controller;

use App\Controller\AppController;

use Cake\Event\Event;
use Cake\ORM\TableRegistry;
use Cake\Core\Configure;

class ParticipatingDepartmentsController extends AppController
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
            'contain' => ['Colleges', 'Departments', 'DevelopingRegions'],
        ];
        $participatingDepartments = $this->paginate($this->ParticipatingDepartments);

        $this->set(compact('participatingDepartments'));
    }

    public function view($id = null)
    {
        $participatingDepartment = $this->ParticipatingDepartments->get($id, [
            'contain' => ['Colleges', 'Departments', 'DevelopingRegions', 'ReservedPlaces'],
        ]);

        $this->set('participatingDepartment', $participatingDepartment);
    }


    public function add()
    {
        $participatingDepartment = $this->ParticipatingDepartments->newEntity();
        if ($this->request->is('post')) {
            $participatingDepartment = $this->ParticipatingDepartments->patchEntity($participatingDepartment, $this->request->getData());
            if ($this->ParticipatingDepartments->save($participatingDepartment)) {
                $this->Flash->success(__('The participating department has been saved.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The participating department could not be saved. Please, try again.'));
        }
        $this->set(compact('participatingDepartment'));
    }


    public function edit($id = null)
    {
        $participatingDepartment = $this->ParticipatingDepartments->get($id, [
            'contain' => [],
        ]);
        if ($this->request->is(['patch', 'post', 'put'])) {
            $participatingDepartment = $this->ParticipatingDepartments->patchEntity($participatingDepartment, $this->request->getData());
            if ($this->ParticipatingDepartments->save($participatingDepartment)) {
                $this->Flash->success(__('The participating department has been saved.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The participating department could not be saved. Please, try again.'));
        }
        $this->set(compact('participatingDepartment'));
    }


    public function delete($id = null)
    {
        $this->request->allowMethod(['post', 'delete']);
        $participatingDepartment = $this->ParticipatingDepartments->get($id);
        if ($this->ParticipatingDepartments->delete($participatingDepartment)) {
            $this->Flash->success(__('The participating department has been deleted.'));
        } else {
            $this->Flash->error(__('The participating department could not be deleted. Please, try again.'));
        }

        return $this->redirect(['action' => 'index']);
    }
}
