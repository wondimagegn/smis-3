<?php
namespace App\Controller;

use App\Controller\AppController;

class CoursesStudentsController extends AppController
{

    public function index()
    {
        $this->paginate = [
            'contain' => ['Courses', 'Students'],
        ];
        $coursesStudents = $this->paginate($this->CoursesStudents);

        $this->set(compact('coursesStudents'));
    }


    public function view($id = null)
    {
        $coursesStudent = $this->CoursesStudents->get($id, [
            'contain' => ['Courses', 'Students'],
        ]);

        $this->set('coursesStudent', $coursesStudent);
    }


    public function add()
    {
        $coursesStudent = $this->CoursesStudents->newEntity();
        if ($this->request->is('post')) {
            $coursesStudent = $this->CoursesStudents->patchEntity($coursesStudent, $this->request->getData());
            if ($this->CoursesStudents->save($coursesStudent)) {
                $this->Flash->success(__('The courses student has been saved.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The courses student could not be saved. Please, try again.'));
        }
        $this->set(compact('coursesStudent'));
    }


    public function edit($id = null)
    {
        $coursesStudent = $this->CoursesStudents->get($id, [
            'contain' => [],
        ]);
        if ($this->request->is(['patch', 'post', 'put'])) {
            $coursesStudent = $this->CoursesStudents->patchEntity($coursesStudent, $this->request->getData());
            if ($this->CoursesStudents->save($coursesStudent)) {
                $this->Flash->success(__('The courses student has been saved.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The courses student could not be saved. Please, try again.'));
        }
        $this->set(compact('coursesStudent'));
    }


    public function delete($id = null)
    {
        $this->request->allowMethod(['post', 'delete']);
        $coursesStudent = $this->CoursesStudents->get($id);
        if ($this->CoursesStudents->delete($coursesStudent)) {
            $this->Flash->success(__('The courses student has been deleted.'));
        } else {
            $this->Flash->error(__('The courses student could not be deleted. Please, try again.'));
        }

        return $this->redirect(['action' => 'index']);
    }
}
