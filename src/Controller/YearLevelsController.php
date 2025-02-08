<?php
namespace App\Controller;

use App\Controller\AppController;

class YearLevelsController extends AppController
{
    public function index()
    {
        $this->paginate = [
            'contain' => ['Departments'],
        ];
        $yearLevels = $this->paginate($this->YearLevels);

        $this->set(compact('yearLevels'));
    }

    public function view($id = null)
    {
        $yearLevel = $this->YearLevels->get($id, [
            'contain' => ['Departments', 'AcademicCalendars', 'AcademicStands', 'CourseAdds',
                'CourseDrops', 'CourseRegistrations', 'Courses', 'ExamPeriods', 'ExtendingAcademicCalendars', 'InstructorNumberOfExamConstraints', 'OtherAcademicRules', 'PublishedCourses', 'Sections'],
        ]);

        $this->set('yearLevel', $yearLevel);
    }

    public function add()
    {
        $yearLevel = $this->YearLevels->newEntity();
        if ($this->request->is('post')) {
            $yearLevel = $this->YearLevels->patchEntity($yearLevel, $this->request->getData());
            if ($this->YearLevels->save($yearLevel)) {
                $this->Flash->success(__('The year level has been saved.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The year level could not be saved. Please, try again.'));
        }
        $this->set(compact('yearLevel'));
    }


    public function edit($id = null)
    {
        $yearLevel = $this->YearLevels->get($id, [
            'contain' => [],
        ]);
        if ($this->request->is(['patch', 'post', 'put'])) {
            $yearLevel = $this->YearLevels->patchEntity($yearLevel, $this->request->getData());
            if ($this->YearLevels->save($yearLevel)) {
                $this->Flash->success(__('The year level has been saved.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The year level could not be saved. Please, try again.'));
        }
        $this->set(compact('yearLevel', 'departments'));
    }


    public function delete($id = null)
    {
        $this->request->allowMethod(['post', 'delete']);
        $yearLevel = $this->YearLevels->get($id);
        if ($this->YearLevels->delete($yearLevel)) {
            $this->Flash->success(__('The year level has been deleted.'));
        } else {
            $this->Flash->error(__('The year level could not be deleted. Please, try again.'));
        }

        return $this->redirect(['action' => 'index']);
    }
}
