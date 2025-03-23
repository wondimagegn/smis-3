<?php

namespace App\Controller;

use App\Controller\AppController;

use Cake\Event\Event;
use Cake\ORM\TableRegistry;
use Cake\Core\Configure;

class StudentsExamSplitSectionsController extends AppController
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
            'contain' => ['Students', 'ExamSplitSections'],
        ];
        $studentsExamSplitSections = $this->paginate($this->StudentsExamSplitSections);

        $this->set(compact('studentsExamSplitSections'));
    }

    public function view($id = null)
    {
        $studentsExamSplitSection = $this->StudentsExamSplitSections->get($id, [
            'contain' => ['Students', 'ExamSplitSections'],
        ]);

        $this->set('studentsExamSplitSection', $studentsExamSplitSection);
    }

    public function add()
    {
        $studentsExamSplitSection = $this->StudentsExamSplitSections->newEntity();
        if ($this->request->is('post')) {
            $studentsExamSplitSection = $this->StudentsExamSplitSections->patchEntity($studentsExamSplitSection, $this->request->getData());
            if ($this->StudentsExamSplitSections->save($studentsExamSplitSection)) {
                $this->Flash->success(__('The students exam split section has been saved.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The students exam split section could not be saved. Please, try again.'));
        }
        $this->set(compact('studentsExamSplitSection'));
    }

    public function edit($id = null)
    {
        $studentsExamSplitSection = $this->StudentsExamSplitSections->get($id, [
            'contain' => [],
        ]);
        if ($this->request->is(['patch', 'post', 'put'])) {
            $studentsExamSplitSection = $this->StudentsExamSplitSections->patchEntity($studentsExamSplitSection, $this->request->getData());
            if ($this->StudentsExamSplitSections->save($studentsExamSplitSection)) {
                $this->Flash->success(__('The students exam split section has been saved.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The students exam split section could not be saved. Please, try again.'));
        }
        $this->set(compact('studentsExamSplitSection'));
    }

    public function delete($id = null)
    {
        $this->request->allowMethod(['post', 'delete']);
        $studentsExamSplitSection = $this->StudentsExamSplitSections->get($id);
        if ($this->StudentsExamSplitSections->delete($studentsExamSplitSection)) {
            $this->Flash->success(__('The students exam split section has been deleted.'));
        } else {
            $this->Flash->error(__('The students exam split section could not be deleted. Please, try again.'));
        }

        return $this->redirect(['action' => 'index']);
    }
}
