<?php
namespace App\Controller;

use App\Controller\AppController;

class InstructorNumberOfExamConstraintsController extends AppController
{

    public function index()
    {
        $this->paginate = [
            'contain' => ['Staffs', 'StaffForExams', 'Colleges', 'YearLevels'],
        ];
        $instructorNumberOfExamConstraints = $this->paginate($this->InstructorNumberOfExamConstraints);

        $this->set(compact('instructorNumberOfExamConstraints'));
    }

    public function view($id = null)
    {
        $instructorNumberOfExamConstraint = $this->InstructorNumberOfExamConstraints->get($id, [
            'contain' => ['Staffs', 'StaffForExams', 'Colleges', 'YearLevels'],
        ]);

        $this->set('instructorNumberOfExamConstraint', $instructorNumberOfExamConstraint);
    }

    public function add()
    {
        $instructorNumberOfExamConstraint = $this->InstructorNumberOfExamConstraints->newEntity();
        if ($this->request->is('post')) {
            $instructorNumberOfExamConstraint = $this->InstructorNumberOfExamConstraints->patchEntity($instructorNumberOfExamConstraint, $this->request->getData());
            if ($this->InstructorNumberOfExamConstraints->save($instructorNumberOfExamConstraint)) {
                $this->Flash->success(__('The instructor number of exam constraint has been saved.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The instructor number of exam constraint could not be saved. Please, try again.'));
        }
       $this->set(compact('instructorNumberOfExamConstraint'));
    }

    public function edit($id = null)
    {
        $instructorNumberOfExamConstraint = $this->InstructorNumberOfExamConstraints->get($id, [
            'contain' => [],
        ]);
        if ($this->request->is(['patch', 'post', 'put'])) {
            $instructorNumberOfExamConstraint = $this->InstructorNumberOfExamConstraints->patchEntity($instructorNumberOfExamConstraint, $this->request->getData());
            if ($this->InstructorNumberOfExamConstraints->save($instructorNumberOfExamConstraint)) {
                $this->Flash->success(__('The instructor number of exam constraint has been saved.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The instructor number of exam constraint could not be saved. Please, try again.'));
        }
        $this->set(compact('instructorNumberOfExamConstraint'));
    }

    public function delete($id = null)
    {
        $this->request->allowMethod(['post', 'delete']);
        $instructorNumberOfExamConstraint = $this->InstructorNumberOfExamConstraints->get($id);
        if ($this->InstructorNumberOfExamConstraints->delete($instructorNumberOfExamConstraint)) {
            $this->Flash->success(__('The instructor number of exam constraint has been deleted.'));
        } else {
            $this->Flash->error(__('The instructor number of exam constraint could not be deleted. Please, try again.'));
        }

        return $this->redirect(['action' => 'index']);
    }
}
