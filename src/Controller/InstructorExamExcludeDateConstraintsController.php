<?php
namespace App\Controller;

use App\Controller\AppController;

class InstructorExamExcludeDateConstraintsController extends AppController
{

    public function index()
    {
        $this->paginate = [
            'contain' => ['Staffs', 'StaffForExams'],
        ];
        $instructorExamExcludeDateConstraints = $this->paginate($this->InstructorExamExcludeDateConstraints);

        $this->set(compact('instructorExamExcludeDateConstraints'));
    }


    public function view($id = null)
    {
        $instructorExamExcludeDateConstraint = $this->InstructorExamExcludeDateConstraints->get($id, [
            'contain' => ['Staffs', 'StaffForExams'],
        ]);

        $this->set('instructorExamExcludeDateConstraint', $instructorExamExcludeDateConstraint);
    }

    public function add()
    {
        $instructorExamExcludeDateConstraint = $this->InstructorExamExcludeDateConstraints->newEntity();
        if ($this->request->is('post')) {
            $instructorExamExcludeDateConstraint = $this->InstructorExamExcludeDateConstraints->patchEntity($instructorExamExcludeDateConstraint, $this->request->getData());
            if ($this->InstructorExamExcludeDateConstraints->save($instructorExamExcludeDateConstraint)) {
                $this->Flash->success(__('The instructor exam exclude date constraint has been saved.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The instructor exam exclude date constraint could not be saved. Please, try again.'));
        }
       $this->set(compact('instructorExamExcludeDateConstraint'));
    }


    public function edit($id = null)
    {
        $instructorExamExcludeDateConstraint = $this->InstructorExamExcludeDateConstraints->get($id, [
            'contain' => [],
        ]);
        if ($this->request->is(['patch', 'post', 'put'])) {
            $instructorExamExcludeDateConstraint = $this->InstructorExamExcludeDateConstraints->patchEntity($instructorExamExcludeDateConstraint, $this->request->getData());
            if ($this->InstructorExamExcludeDateConstraints->save($instructorExamExcludeDateConstraint)) {
                $this->Flash->success(__('The instructor exam exclude date constraint has been saved.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The instructor exam exclude date constraint could not be saved. Please, try again.'));
        }
         $this->set(compact('instructorExamExcludeDateConstraint'));
    }

    public function delete($id = null)
    {
        $this->request->allowMethod(['post', 'delete']);
        $instructorExamExcludeDateConstraint = $this->InstructorExamExcludeDateConstraints->get($id);
        if ($this->InstructorExamExcludeDateConstraints->delete($instructorExamExcludeDateConstraint)) {
            $this->Flash->success(__('The instructor exam exclude date constraint has been deleted.'));
        } else {
            $this->Flash->error(__('The instructor exam exclude date constraint could not be deleted. Please, try again.'));
        }

        return $this->redirect(['action' => 'index']);
    }
}
