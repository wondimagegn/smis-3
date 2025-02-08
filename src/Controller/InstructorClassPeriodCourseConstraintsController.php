<?php
namespace App\Controller;

use App\Controller\AppController;

class InstructorClassPeriodCourseConstraintsController extends AppController
{

    public function index()
    {
        $this->paginate = [
            'contain' => ['Staffs', 'ClassPeriods', 'Colleges'],
        ];
        $instructorClassPeriodCourseConstraints = $this->paginate($this->InstructorClassPeriodCourseConstraints);

        $this->set(compact('instructorClassPeriodCourseConstraints'));
    }

    public function view($id = null)
    {
        $instructorClassPeriodCourseConstraint = $this->InstructorClassPeriodCourseConstraints->get($id, [
            'contain' => ['Staffs', 'ClassPeriods', 'Colleges'],
        ]);

        $this->set('instructorClassPeriodCourseConstraint', $instructorClassPeriodCourseConstraint);
    }

    public function add()
    {
        $instructorClassPeriodCourseConstraint = $this->InstructorClassPeriodCourseConstraints->newEntity();
        if ($this->request->is('post')) {
            $instructorClassPeriodCourseConstraint = $this->InstructorClassPeriodCourseConstraints->patchEntity($instructorClassPeriodCourseConstraint, $this->request->getData());
            if ($this->InstructorClassPeriodCourseConstraints->save($instructorClassPeriodCourseConstraint)) {
                $this->Flash->success(__('The instructor class period course constraint has been saved.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The instructor class period course constraint could not be saved. Please, try again.'));
        }
        $this->set(compact('instructorClassPeriodCourseConstraint'));
    }


    public function edit($id = null)
    {
        $instructorClassPeriodCourseConstraint = $this->InstructorClassPeriodCourseConstraints->get($id, [
            'contain' => [],
        ]);
        if ($this->request->is(['patch', 'post', 'put'])) {
            $instructorClassPeriodCourseConstraint = $this->InstructorClassPeriodCourseConstraints->patchEntity($instructorClassPeriodCourseConstraint, $this->request->getData());
            if ($this->InstructorClassPeriodCourseConstraints->save($instructorClassPeriodCourseConstraint)) {
                $this->Flash->success(__('The instructor class period course constraint has been saved.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The instructor class period course constraint could not be saved. Please, try again.'));
        }
        $this->set(compact('instructorClassPeriodCourseConstraint'));
    }


    public function delete($id = null)
    {
        $this->request->allowMethod(['post', 'delete']);
        $instructorClassPeriodCourseConstraint = $this->InstructorClassPeriodCourseConstraints->get($id);
        if ($this->InstructorClassPeriodCourseConstraints->delete($instructorClassPeriodCourseConstraint)) {
            $this->Flash->success(__('The instructor class period course constraint has been deleted.'));
        } else {
            $this->Flash->error(__('The instructor class period course constraint could not be deleted. Please, try again.'));
        }

        return $this->redirect(['action' => 'index']);
    }
}
