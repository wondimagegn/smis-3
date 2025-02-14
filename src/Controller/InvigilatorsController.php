<?php
namespace App\Controller;

use App\Controller\AppController;

class InvigilatorsController extends AppController
{

    public function index()
    {
        $this->paginate = [
            'contain' => ['Staffs', 'StaffForExams', 'ExamSchedules'],
        ];
        $invigilators = $this->paginate($this->Invigilators);

        $this->set(compact('invigilators'));
    }

    public function view($id = null)
    {
        $invigilator = $this->Invigilators->get($id, [
            'contain' => ['Staffs', 'StaffForExams', 'ExamSchedules'],
        ]);

        $this->set('invigilator', $invigilator);
    }

    public function add()
    {
        $invigilator = $this->Invigilators->newEntity();
        if ($this->request->is('post')) {
            $invigilator = $this->Invigilators->patchEntity($invigilator, $this->request->getData());
            if ($this->Invigilators->save($invigilator)) {
                $this->Flash->success(__('The invigilator has been saved.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The invigilator could not be saved. Please, try again.'));
        }
        $this->set(compact('invigilator'));
    }


    public function edit($id = null)
    {
        $invigilator = $this->Invigilators->get($id, [
            'contain' => [],
        ]);
        if ($this->request->is(['patch', 'post', 'put'])) {
            $invigilator = $this->Invigilators->patchEntity($invigilator, $this->request->getData());
            if ($this->Invigilators->save($invigilator)) {
                $this->Flash->success(__('The invigilator has been saved.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The invigilator could not be saved. Please, try again.'));
        }
        $this->set(compact('invigilator'));
    }

    public function delete($id = null)
    {
        $this->request->allowMethod(['post', 'delete']);
        $invigilator = $this->Invigilators->get($id);
        if ($this->Invigilators->delete($invigilator)) {
            $this->Flash->success(__('The invigilator has been deleted.'));
        } else {
            $this->Flash->error(__('The invigilator could not be deleted. Please, try again.'));
        }

        return $this->redirect(['action' => 'index']);
    }
}
