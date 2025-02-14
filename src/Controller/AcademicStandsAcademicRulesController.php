<?php
namespace App\Controller;

use App\Controller\AppController;

class AcademicStandsAcademicRulesController extends AppController
{

    public function index()
    {
        $this->paginate = [
            'contain' => ['AcademicStands', 'AcademicRules'],
        ];
        $academicStandsAcademicRules = $this->paginate($this->AcademicStandsAcademicRules);

        $this->set(compact('academicStandsAcademicRules'));
    }


    public function view($id = null)
    {
        $academicStandsAcademicRule = $this->AcademicStandsAcademicRules->get($id, [
            'contain' => ['AcademicStands', 'AcademicRules'],
        ]);

        $this->set('academicStandsAcademicRule', $academicStandsAcademicRule);
    }

    public function add()
    {
        $academicStandsAcademicRule = $this->AcademicStandsAcademicRules->newEntity();
        if ($this->request->is('post')) {
            $academicStandsAcademicRule = $this->AcademicStandsAcademicRules->patchEntity($academicStandsAcademicRule, $this->request->getData());
            if ($this->AcademicStandsAcademicRules->save($academicStandsAcademicRule)) {
                $this->Flash->success(__('The academic stands academic rule has been saved.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The academic stands academic rule could not be saved. Please, try again.'));
        }
        $academicStands = $this->AcademicStandsAcademicRules->AcademicStands->find('list', ['limit' => 200]);
        $academicRules = $this->AcademicStandsAcademicRules->AcademicRules->find('list', ['limit' => 200]);
        $this->set(compact('academicStandsAcademicRule', 'academicStands', 'academicRules'));
    }

    public function edit($id = null)
    {
        $academicStandsAcademicRule = $this->AcademicStandsAcademicRules->get($id, [
            'contain' => [],
        ]);
        if ($this->request->is(['patch', 'post', 'put'])) {
            $academicStandsAcademicRule = $this->AcademicStandsAcademicRules->patchEntity($academicStandsAcademicRule, $this->request->getData());
            if ($this->AcademicStandsAcademicRules->save($academicStandsAcademicRule)) {
                $this->Flash->success(__('The academic stands academic rule has been saved.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The academic stands academic rule could not be saved. Please, try again.'));
        }
        $academicStands = $this->AcademicStandsAcademicRules->AcademicStands->find('list', ['limit' => 200]);
        $academicRules = $this->AcademicStandsAcademicRules->AcademicRules->find('list', ['limit' => 200]);
        $this->set(compact('academicStandsAcademicRule', 'academicStands', 'academicRules'));
    }


    public function delete($id = null)
    {
        $this->request->allowMethod(['post', 'delete']);
        $academicStandsAcademicRule = $this->AcademicStandsAcademicRules->get($id);
        if ($this->AcademicStandsAcademicRules->delete($academicStandsAcademicRule)) {
            $this->Flash->success(__('The academic stands academic rule has been deleted.'));
        } else {
            $this->Flash->error(__('The academic stands academic rule could not be deleted. Please, try again.'));
        }

        return $this->redirect(['action' => 'index']);
    }
}
