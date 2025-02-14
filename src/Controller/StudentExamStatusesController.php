<?php
namespace App\Controller;

use App\Controller\AppController;

class StudentExamStatusesController extends AppController
{

    public function index()
    {
        $this->paginate = [
            'contain' => ['Students', 'AcademicStatuses'],
        ];
        $studentExamStatuses = $this->paginate($this->StudentExamStatuses);

        $this->set(compact('studentExamStatuses'));
    }

    public function view($id = null)
    {
        $studentExamStatus = $this->StudentExamStatuses->get($id, [
            'contain' => ['Students', 'AcademicStatuses'],
        ]);

        $this->set('studentExamStatus', $studentExamStatus);
    }

    public function add()
    {
        $studentExamStatus = $this->StudentExamStatuses->newEntity();
        if ($this->request->is('post')) {
            $studentExamStatus = $this->StudentExamStatuses->patchEntity($studentExamStatus, $this->request->getData());
            if ($this->StudentExamStatuses->save($studentExamStatus)) {
                $this->Flash->success(__('The student exam status has been saved.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The student exam status could not be saved. Please, try again.'));
        }
        $this->set(compact('studentExamStatus'));
    }

    public function edit($id = null)
    {
        $studentExamStatus = $this->StudentExamStatuses->get($id, [
            'contain' => [],
        ]);
        if ($this->request->is(['patch', 'post', 'put'])) {
            $studentExamStatus = $this->StudentExamStatuses->patchEntity($studentExamStatus, $this->request->getData());
            if ($this->StudentExamStatuses->save($studentExamStatus)) {
                $this->Flash->success(__('The student exam status has been saved.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The student exam status could not be saved. Please, try again.'));
        }
        $this->set(compact('studentExamStatus'));
    }

    public function delete($id = null)
    {
        $this->request->allowMethod(['post', 'delete']);
        $studentExamStatus = $this->StudentExamStatuses->get($id);
        if ($this->StudentExamStatuses->delete($studentExamStatus)) {
            $this->Flash->success(__('The student exam status has been deleted.'));
        } else {
            $this->Flash->error(__('The student exam status could not be deleted. Please, try again.'));
        }

        return $this->redirect(['action' => 'index']);
    }
}
