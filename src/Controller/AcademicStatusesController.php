<?php
namespace App\Controller;

use App\Controller\AppController;

class AcademicStatusesController extends AppController
{

    public function index()
    {
        $academicStatuses = $this->paginate($this->AcademicStatuses);

        $this->set(compact('academicStatuses'));
    }


    public function view($id = null)
    {
        $academicStatus = $this->AcademicStatuses->get($id, [
            'contain' => ['AcademicStands', 'HistoricalStudentExamStatuses', 'OtherAcademicRules', 'StudentExamStatuses'],
        ]);

        $this->set('academicStatus', $academicStatus);
    }


    public function add()
    {
        $academicStatus = $this->AcademicStatuses->newEntity();
        if ($this->request->is('post')) {
            $academicStatus = $this->AcademicStatuses->patchEntity($academicStatus, $this->request->getData());
            if ($this->AcademicStatuses->save($academicStatus)) {
                $this->Flash->success(__('The academic status has been saved.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The academic status could not be saved. Please, try again.'));
        }
        $this->set(compact('academicStatus'));
    }

    public function edit($id = null)
    {
        $academicStatus = $this->AcademicStatuses->get($id, [
            'contain' => [],
        ]);
        if ($this->request->is(['patch', 'post', 'put'])) {
            $academicStatus = $this->AcademicStatuses->patchEntity($academicStatus, $this->request->getData());
            if ($this->AcademicStatuses->save($academicStatus)) {
                $this->Flash->success(__('The academic status has been saved.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The academic status could not be saved. Please, try again.'));
        }
        $this->set(compact('academicStatus'));
    }

    public function delete($id = null)
    {
        $this->request->allowMethod(['post', 'delete']);
        $academicStatus = $this->AcademicStatuses->get($id);
        if ($this->AcademicStatuses->delete($academicStatus)) {
            $this->Flash->success(__('The academic status has been deleted.'));
        } else {
            $this->Flash->error(__('The academic status could not be deleted. Please, try again.'));
        }

        return $this->redirect(['action' => 'index']);
    }
}
