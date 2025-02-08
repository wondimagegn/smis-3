<?php
namespace App\Controller;

use App\Controller\AppController;

class AcademicStandsController extends AppController
{

    public function index()
    {
        $this->paginate = [
            'contain' => ['Programs', 'YearLevels', 'AcademicStatuses'],
        ];
        $academicStands = $this->paginate($this->AcademicStands);

        $this->set(compact('academicStands'));
    }


    public function view($id = null)
    {
        $academicStand = $this->AcademicStands->get($id, [
            'contain' => ['Programs', 'YearLevels', 'AcademicStatuses', 'AcademicRules'],
        ]);

        $this->set('academicStand', $academicStand);
    }


    public function add()
    {
        $academicStand = $this->AcademicStands->newEntity();
        if ($this->request->is('post')) {
            $academicStand = $this->AcademicStands->patchEntity($academicStand, $this->request->getData());
            if ($this->AcademicStands->save($academicStand)) {
                $this->Flash->success(__('The academic stand has been saved.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The academic stand could not be saved. Please, try again.'));
        }
        $programs = $this->AcademicStands->Programs->find('list', ['limit' => 200]);
        $academicStatuses = $this->AcademicStands->AcademicStatuses->find('list', ['limit' => 200]);
        $academicRules = $this->AcademicStands->AcademicRules->find('list', ['limit' => 200]);
        $this->set(compact('academicStand', 'programs', 'yearLevels', 'academicStatuses', 'academicRules'));
    }


    public function edit($id = null)
    {
        $academicStand = $this->AcademicStands->get($id, [
            'contain' => ['AcademicRules'],
        ]);
        if ($this->request->is(['patch', 'post', 'put'])) {
            $academicStand = $this->AcademicStands->patchEntity($academicStand, $this->request->getData());
            if ($this->AcademicStands->save($academicStand)) {
                $this->Flash->success(__('The academic stand has been saved.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The academic stand could not be saved. Please, try again.'));
        }
        $programs = $this->AcademicStands->Programs->find('list', ['limit' => 200]);

        $academicStatuses = $this->AcademicStands->AcademicStatuses->find('list', ['limit' => 200]);
        $academicRules = $this->AcademicStands->AcademicRules->find('list', ['limit' => 200]);
        $this->set(compact('academicStand', 'programs', 'yearLevels', 'academicStatuses', 'academicRules'));
    }


    public function delete($id = null)
    {
        $this->request->allowMethod(['post', 'delete']);
        $academicStand = $this->AcademicStands->get($id);
        if ($this->AcademicStands->delete($academicStand)) {
            $this->Flash->success(__('The academic stand has been deleted.'));
        } else {
            $this->Flash->error(__('The academic stand could not be deleted. Please, try again.'));
        }

        return $this->redirect(['action' => 'index']);
    }
}
