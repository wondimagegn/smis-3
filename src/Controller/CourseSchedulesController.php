<?php
namespace App\Controller;

use App\Controller\AppController;

class CourseSchedulesController extends AppController
{

    public function index()
    {
        $this->paginate = [
            'contain' => ['ClassRooms', 'Sections', 'PublishedCourses', 'CourseSplitSections'],
        ];
        $courseSchedules = $this->paginate($this->CourseSchedules);

        $this->set(compact('courseSchedules'));
    }

    public function view($id = null)
    {
        $courseSchedule = $this->CourseSchedules->get($id, [
            'contain' => ['ClassRooms', 'Sections', 'PublishedCourses', 'CourseSplitSections', 'ClassPeriods'],
        ]);

        $this->set('courseSchedule', $courseSchedule);
    }

    public function add()
    {
        $courseSchedule = $this->CourseSchedules->newEntity();
        if ($this->request->is('post')) {
            $courseSchedule = $this->CourseSchedules->patchEntity($courseSchedule, $this->request->getData());
            if ($this->CourseSchedules->save($courseSchedule)) {
                $this->Flash->success(__('The course schedule has been saved.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The course schedule could not be saved. Please, try again.'));
        }
       $this->set(compact('courseSchedule'));
    }

    public function edit($id = null)
    {
        $courseSchedule = $this->CourseSchedules->get($id, [
            'contain' => ['ClassPeriods'],
        ]);
        if ($this->request->is(['patch', 'post', 'put'])) {
            $courseSchedule = $this->CourseSchedules->patchEntity($courseSchedule, $this->request->getData());
            if ($this->CourseSchedules->save($courseSchedule)) {
                $this->Flash->success(__('The course schedule has been saved.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The course schedule could not be saved. Please, try again.'));
        }
           $this->set(compact('courseSchedule'));
    }


    public function delete($id = null)
    {
        $this->request->allowMethod(['post', 'delete']);
        $courseSchedule = $this->CourseSchedules->get($id);
        if ($this->CourseSchedules->delete($courseSchedule)) {
            $this->Flash->success(__('The course schedule has been deleted.'));
        } else {
            $this->Flash->error(__('The course schedule could not be deleted. Please, try again.'));
        }

        return $this->redirect(['action' => 'index']);
    }
}
