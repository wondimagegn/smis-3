<?php
namespace App\Controller;

use App\Controller\AppController;

class StaffForExamsController extends AppController
{
    public function index()
    {
        $this->paginate = [
            'contain' => ['Colleges', 'Staffs'],
        ];
        $staffForExams = $this->paginate($this->StaffForExams);

        $this->set(compact('staffForExams'));
    }


    public function view($id = null)
    {
        $staffForExam = $this->StaffForExams->get($id, [
            'contain' => ['Colleges', 'Staffs', 'InstructorExamExcludeDateConstraints', 'InstructorNumberOfExamConstraints', 'Invigilators'],
        ]);

        $this->set('staffForExam', $staffForExam);
    }

    public function add()
    {
        $staffForExam = $this->StaffForExams->newEntity();
        if ($this->request->is('post')) {
            $staffForExam = $this->StaffForExams->patchEntity($staffForExam, $this->request->getData());
            if ($this->StaffForExams->save($staffForExam)) {
                $this->Flash->success(__('The staff for exam has been saved.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The staff for exam could not be saved. Please, try again.'));
        }
        $this->set(compact('staffForExam'));
    }


    public function edit($id = null)
    {
        $staffForExam = $this->StaffForExams->get($id, [
            'contain' => [],
        ]);
        if ($this->request->is(['patch', 'post', 'put'])) {
            $staffForExam = $this->StaffForExams->patchEntity($staffForExam, $this->request->getData());
            if ($this->StaffForExams->save($staffForExam)) {
                $this->Flash->success(__('The staff for exam has been saved.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The staff for exam could not be saved. Please, try again.'));
        }
        $colleges = $this->StaffForExams->Colleges->find('list', ['limit' => 200]);
        $staffs = $this->StaffForExams->Staffs->find('list', ['limit' => 200]);
        $this->set(compact('staffForExam'));
    }

    public function delete($id = null)
    {
        $this->request->allowMethod(['post', 'delete']);
        $staffForExam = $this->StaffForExams->get($id);
        if ($this->StaffForExams->delete($staffForExam)) {
            $this->Flash->success(__('The staff for exam has been deleted.'));
        } else {
            $this->Flash->error(__('The staff for exam could not be deleted. Please, try again.'));
        }

        return $this->redirect(['action' => 'index']);
    }
}
