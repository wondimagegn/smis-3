<?php
namespace App\Controller;

use App\Controller\AppController;

class OtherAcademicRulesController extends AppController
{

    public function index()
    {
        $this->paginate = [
            'contain' => ['Departments', 'Programs', 'ProgramTypes', 'YearLevels', 'Curriculums', 'CourseCategories', 'AcademicStatuses'],
        ];
        $otherAcademicRules = $this->paginate($this->OtherAcademicRules);

        $this->set(compact('otherAcademicRules'));
    }

    public function view($id = null)
    {
        $otherAcademicRule = $this->OtherAcademicRules->get($id, [
            'contain' => ['Departments', 'Programs', 'ProgramTypes', 'YearLevels', 'Curriculums', 'CourseCategories', 'AcademicStatuses'],
        ]);

        $this->set('otherAcademicRule', $otherAcademicRule);
    }

    public function add()
    {
        $otherAcademicRule = $this->OtherAcademicRules->newEntity();
        if ($this->request->is('post')) {
            $otherAcademicRule = $this->OtherAcademicRules->patchEntity($otherAcademicRule, $this->request->getData());
            if ($this->OtherAcademicRules->save($otherAcademicRule)) {
                $this->Flash->success(__('The other academic rule has been saved.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The other academic rule could not be saved. Please, try again.'));
        }

        $this->set(compact('otherAcademicRule'));
    }

    public function edit($id = null)
    {
        $otherAcademicRule = $this->OtherAcademicRules->get($id, [
            'contain' => [],
        ]);
        if ($this->request->is(['patch', 'post', 'put'])) {
            $otherAcademicRule = $this->OtherAcademicRules->patchEntity($otherAcademicRule, $this->request->getData());
            if ($this->OtherAcademicRules->save($otherAcademicRule)) {
                $this->Flash->success(__('The other academic rule has been saved.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The other academic rule could not be saved. Please, try again.'));
        }
        $this->set(compact('otherAcademicRule'));
    }

    public function delete($id = null)
    {
        $this->request->allowMethod(['post', 'delete']);
        $otherAcademicRule = $this->OtherAcademicRules->get($id);
        if ($this->OtherAcademicRules->delete($otherAcademicRule)) {
            $this->Flash->success(__('The other academic rule has been deleted.'));
        } else {
            $this->Flash->error(__('The other academic rule could not be deleted. Please, try again.'));
        }

        return $this->redirect(['action' => 'index']);
    }
}
