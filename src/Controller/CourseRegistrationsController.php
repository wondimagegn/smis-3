<?php
namespace App\Controller;

use App\Controller\AppController;

class CourseRegistrationsController extends AppController
{

    public function index()
    {
        $this->paginate = [
            'contain' => ['YearLevels', 'Sections', 'Students', 'PublishedCourses', 'AcademicCalendars'],
        ];
        $courseRegistrations = $this->paginate($this->CourseRegistrations);

        $this->set(compact('courseRegistrations'));
    }


    public function view($id = null)
    {
        $courseRegistration = $this->CourseRegistrations->get($id, [
            'contain' => ['YearLevels', 'Sections', 'Students', 'PublishedCourses', 'AcademicCalendars', 'CourseDrops', 'ExamGrades', 'ExamResults', 'ExcludedCourseFromTranscripts', 'FxResitRequest', 'HistoricalStudentCourseGradeExcludes', 'MakeupExams', 'RejectedExamGrades', 'ResultEntryAssignments'],
        ]);

        $this->set('courseRegistration', $courseRegistration);
    }

    public function add()
    {
        $courseRegistration = $this->CourseRegistrations->newEntity();
        if ($this->request->is('post')) {
            $courseRegistration = $this->CourseRegistrations->patchEntity($courseRegistration, $this->request->getData());
            if ($this->CourseRegistrations->save($courseRegistration)) {
                $this->Flash->success(__('The course registration has been saved.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The course registration could not be saved. Please, try again.'));
        }

        $this->set(compact('courseRegistration'));
    }


    public function edit($id = null)
    {
        $courseRegistration = $this->CourseRegistrations->get($id, [
            'contain' => [],
        ]);
        if ($this->request->is(['patch', 'post', 'put'])) {
            $courseRegistration = $this->CourseRegistrations->patchEntity($courseRegistration, $this->request->getData());
            if ($this->CourseRegistrations->save($courseRegistration)) {
                $this->Flash->success(__('The course registration has been saved.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The course registration could not be saved. Please, try again.'));
        }

        $this->set(compact('courseRegistration'));
    }


    public function delete($id = null)
    {
        $this->request->allowMethod(['post', 'delete']);
        $courseRegistration = $this->CourseRegistrations->get($id);
        if ($this->CourseRegistrations->delete($courseRegistration)) {
            $this->Flash->success(__('The course registration has been deleted.'));
        } else {
            $this->Flash->error(__('The course registration could not be deleted. Please, try again.'));
        }

        return $this->redirect(['action' => 'index']);
    }
}
