<?php

namespace App\Controller;

use App\Controller\AppController;

use Cake\Event\Event;
use Cake\ORM\TableRegistry;
use Cake\Core\Configure;
class QualificationsController extends AppController
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
            'contain' => ['Programs'],
        ];
        $qualifications = $this->paginate($this->Qualifications);

        $this->set(compact('qualifications'));
    }

    public function view($id = null)
    {
        $qualification = $this->Qualifications->get($id, [
            'contain' => ['Programs', 'DepartmentStudyPrograms'],
        ]);

        $this->set('qualification', $qualification);
    }

    public function add()
    {
        $qualification = $this->Qualifications->newEntity();
        if ($this->request->is('post')) {
            $qualification = $this->Qualifications->patchEntity($qualification, $this->request->getData());
            if ($this->Qualifications->save($qualification)) {
                $this->Flash->success(__('The qualification has been saved.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The qualification could not be saved. Please, try again.'));
        }
        $this->set(compact('qualification'));
    }


    public function edit($id = null)
    {
        $qualification = $this->Qualifications->get($id, [
            'contain' => [],
        ]);
        if ($this->request->is(['patch', 'post', 'put'])) {
            $qualification = $this->Qualifications->patchEntity($qualification, $this->request->getData());
            if ($this->Qualifications->save($qualification)) {
                $this->Flash->success(__('The qualification has been saved.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The qualification could not be saved. Please, try again.'));
        }
        $this->set(compact('qualification'));
    }


    public function delete($id = null)
    {
        $this->request->allowMethod(['post', 'delete']);
        $qualification = $this->Qualifications->get($id);
        if ($this->Qualifications->delete($qualification)) {
            $this->Flash->success(__('The qualification has been deleted.'));
        } else {
            $this->Flash->error(__('The qualification could not be deleted. Please, try again.'));
        }

        return $this->redirect(['action' => 'index']);
    }
}
