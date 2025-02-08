<?php
namespace App\Controller;

use App\Controller\AppController;

class CourseExamConstraintsController extends AppController
{

    public function index()
    {
        $this->paginate = [
            'contain' => ['PublishedCourses'],
        ];
        $courseExamConstraints = $this->paginate($this->CourseExamConstraints);

        $this->set(compact('courseExamConstraints'));
    }

    public function view($id = null)
    {
        $courseExamConstraint = $this->CourseExamConstraints->get($id, [
            'contain' => ['PublishedCourses'],
        ]);

        $this->set('courseExamConstraint', $courseExamConstraint);
    }

    public function add()
    {
        $courseExamConstraint = $this->CourseExamConstraints->newEntity();
        if ($this->request->is('post')) {
            $courseExamConstraint = $this->CourseExamConstraints->patchEntity($courseExamConstraint, $this->request->getData());
            if ($this->CourseExamConstraints->save($courseExamConstraint)) {
                $this->Flash->success(__('The course exam constraint has been saved.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The course exam constraint could not be saved. Please, try again.'));
        }

        $this->set(compact('courseExamConstraint'));
    }


    public function edit($id = null)
    {
        $courseExamConstraint = $this->CourseExamConstraints->get($id, [
            'contain' => [],
        ]);
        if ($this->request->is(['patch', 'post', 'put'])) {
            $courseExamConstraint = $this->CourseExamConstraints->patchEntity($courseExamConstraint, $this->request->getData());
            if ($this->CourseExamConstraints->save($courseExamConstraint)) {
                $this->Flash->success(__('The course exam constraint has been saved.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The course exam constraint could not be saved. Please, try again.'));
        }

        $this->set(compact('courseExamConstraint'));
    }

    public function delete($id = null)
    {
        $this->request->allowMethod(['post', 'delete']);
        $courseExamConstraint = $this->CourseExamConstraints->get($id);
        if ($this->CourseExamConstraints->delete($courseExamConstraint)) {
            $this->Flash->success(__('The course exam constraint has been deleted.'));
        } else {
            $this->Flash->error(__('The course exam constraint could not be deleted. Please, try again.'));
        }

        return $this->redirect(['action' => 'index']);
    }
}
