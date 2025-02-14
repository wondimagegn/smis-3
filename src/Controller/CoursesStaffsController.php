<?php
namespace App\Controller;

use App\Controller\AppController;

class CoursesStaffsController extends AppController
{

    public function index()
    {
        $this->paginate = [
            'contain' => ['Courses', 'Staffs'],
        ];
        $coursesStaffs = $this->paginate($this->CoursesStaffs);

        $this->set(compact('coursesStaffs'));
    }


    public function view($id = null)
    {
        $coursesStaff = $this->CoursesStaffs->get($id, [
            'contain' => ['Courses', 'Staffs'],
        ]);

        $this->set('coursesStaff', $coursesStaff);
    }

    public function add()
    {
        $coursesStaff = $this->CoursesStaffs->newEntity();
        if ($this->request->is('post')) {
            $coursesStaff = $this->CoursesStaffs->patchEntity($coursesStaff, $this->request->getData());
            if ($this->CoursesStaffs->save($coursesStaff)) {
                $this->Flash->success(__('The courses staff has been saved.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The courses staff could not be saved. Please, try again.'));
        }

        $this->set(compact('coursesStaff'));
    }


    public function edit($id = null)
    {
        $coursesStaff = $this->CoursesStaffs->get($id, [
            'contain' => [],
        ]);
        if ($this->request->is(['patch', 'post', 'put'])) {
            $coursesStaff = $this->CoursesStaffs->patchEntity($coursesStaff, $this->request->getData());
            if ($this->CoursesStaffs->save($coursesStaff)) {
                $this->Flash->success(__('The courses staff has been saved.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The courses staff could not be saved. Please, try again.'));
        }

        $this->set(compact('coursesStaff'));
    }

    public function delete($id = null)
    {
        $this->request->allowMethod(['post', 'delete']);
        $coursesStaff = $this->CoursesStaffs->get($id);
        if ($this->CoursesStaffs->delete($coursesStaff)) {
            $this->Flash->success(__('The courses staff has been deleted.'));
        } else {
            $this->Flash->error(__('The courses staff could not be deleted. Please, try again.'));
        }

        return $this->redirect(['action' => 'index']);
    }
}
