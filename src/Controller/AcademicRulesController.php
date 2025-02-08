<?php
namespace App\Controller;

use App\Controller\AppController;

class AcademicRulesController extends AppController
{

    public function index()
    {
        $academicRules = $this->paginate($this->AcademicRules);

        $this->set(compact('academicRules'));
    }


    public function view($id = null)
    {
        $academicRule = $this->AcademicRules->get($id, [
            'contain' => ['AcademicStands'],
        ]);

        $this->set('academicRule', $academicRule);
    }

    public function add()
    {
        $academicRule = $this->AcademicRules->newEntity();
        if ($this->request->is('post')) {
            $academicRule = $this->AcademicRules->patchEntity($academicRule, $this->request->getData());
            if ($this->AcademicRules->save($academicRule)) {
                $this->Flash->success(__('The academic rule has been saved.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The academic rule could not be saved. Please, try again.'));
        }
        $academicStands = $this->AcademicRules->AcademicStands->find('list', ['limit' => 200]);
        $this->set(compact('academicRule', 'academicStands'));
    }

    public function edit($id = null)
    {
        $academicRule = $this->AcademicRules->get($id, [
            'contain' => ['AcademicStands'],
        ]);
        if ($this->request->is(['patch', 'post', 'put'])) {
            $academicRule = $this->AcademicRules->patchEntity($academicRule, $this->request->getData());
            if ($this->AcademicRules->save($academicRule)) {
                $this->Flash->success(__('The academic rule has been saved.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The academic rule could not be saved. Please, try again.'));
        }
        $academicStands = $this->AcademicRules->AcademicStands->find('list', ['limit' => 200]);
        $this->set(compact('academicRule', 'academicStands'));
    }

    public function delete($id = null)
    {
        $this->request->allowMethod(['post', 'delete']);
        $academicRule = $this->AcademicRules->get($id);
        if ($this->AcademicRules->delete($academicRule)) {
            $this->Flash->success(__('The academic rule has been deleted.'));
        } else {
            $this->Flash->error(__('The academic rule could not be deleted. Please, try again.'));
        }

        return $this->redirect(['action' => 'index']);
    }
}
