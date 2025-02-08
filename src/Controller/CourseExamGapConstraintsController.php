<?php
namespace App\Controller;

use App\Controller\AppController;

class CourseExamGapConstraintsController extends AppController
{

    public function index()
    {
        $this->paginate = [
            'contain' => ['PublishedCourses'],
        ];
        $courseExamGapConstraints = $this->paginate($this->CourseExamGapConstraints);

        $this->set(compact('courseExamGapConstraints'));
    }

    public function view($id = null)
    {
        $courseExamGapConstraint = $this->CourseExamGapConstraints->get($id, [
            'contain' => ['PublishedCourses'],
        ]);

        $this->set('courseExamGapConstraint', $courseExamGapConstraint);
    }


    public function add()
    {
        $courseExamGapConstraint = $this->CourseExamGapConstraints->newEntity();
        if ($this->request->is('post')) {
            $courseExamGapConstraint = $this->CourseExamGapConstraints->patchEntity($courseExamGapConstraint, $this->request->getData());
            if ($this->CourseExamGapConstraints->save($courseExamGapConstraint)) {
                $this->Flash->success(__('The course exam gap constraint has been saved.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The course exam gap constraint could not be saved. Please, try again.'));
        }

        $this->set(compact('courseExamGapConstraint'));
    }

    public function edit($id = null)
    {
        $courseExamGapConstraint = $this->CourseExamGapConstraints->get($id, [
            'contain' => [],
        ]);
        if ($this->request->is(['patch', 'post', 'put'])) {
            $courseExamGapConstraint = $this->CourseExamGapConstraints->patchEntity($courseExamGapConstraint, $this->request->getData());
            if ($this->CourseExamGapConstraints->save($courseExamGapConstraint)) {
                $this->Flash->success(__('The course exam gap constraint has been saved.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The course exam gap constraint could not be saved. Please, try again.'));
        }

        $this->set(compact('courseExamGapConstraint'));
    }


    public function delete($id = null)
    {
        $this->request->allowMethod(['post', 'delete']);
        $courseExamGapConstraint = $this->CourseExamGapConstraints->get($id);
        if ($this->CourseExamGapConstraints->delete($courseExamGapConstraint)) {
            $this->Flash->success(__('The course exam gap constraint has been deleted.'));
        } else {
            $this->Flash->error(__('The course exam gap constraint could not be deleted. Please, try again.'));
        }

        return $this->redirect(['action' => 'index']);
    }
}
