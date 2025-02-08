<?php
namespace App\Controller;

use App\Controller\AppController;

class CourseInstructorAssignmentsController extends AppController
{

    public function index()
    {
        $this->paginate = [
            'contain' => ['Sections', 'Staffs', 'PublishedCourses', 'CourseSplitSections'],
        ];
        $courseInstructorAssignments = $this->paginate($this->CourseInstructorAssignments);

        $this->set(compact('courseInstructorAssignments'));
    }

    public function view($id = null)
    {
        $courseInstructorAssignment = $this->CourseInstructorAssignments->get($id, [
            'contain' => ['Sections', 'Staffs', 'PublishedCourses', 'CourseSplitSections', 'ExamGrades', 'RejectedExamGrades'],
        ]);

        $this->set('courseInstructorAssignment', $courseInstructorAssignment);
    }

    public function add()
    {
        $courseInstructorAssignment = $this->CourseInstructorAssignments->newEntity();
        if ($this->request->is('post')) {
            $courseInstructorAssignment = $this->CourseInstructorAssignments->patchEntity($courseInstructorAssignment, $this->request->getData());
            if ($this->CourseInstructorAssignments->save($courseInstructorAssignment)) {
                $this->Flash->success(__('The course instructor assignment has been saved.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The course instructor assignment could not be saved. Please, try again.'));
        }

        $this->set(compact('courseInstructorAssignment'));
    }


    public function edit($id = null)
    {
        $courseInstructorAssignment = $this->CourseInstructorAssignments->get($id, [
            'contain' => [],
        ]);
        if ($this->request->is(['patch', 'post', 'put'])) {
            $courseInstructorAssignment = $this->CourseInstructorAssignments->patchEntity($courseInstructorAssignment, $this->request->getData());
            if ($this->CourseInstructorAssignments->save($courseInstructorAssignment)) {
                $this->Flash->success(__('The course instructor assignment has been saved.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The course instructor assignment could not be saved. Please, try again.'));
        }

        $this->set(compact('courseInstructorAssignment'));
    }


    public function delete($id = null)
    {
        $this->request->allowMethod(['post', 'delete']);
        $courseInstructorAssignment = $this->CourseInstructorAssignments->get($id);
        if ($this->CourseInstructorAssignments->delete($courseInstructorAssignment)) {
            $this->Flash->success(__('The course instructor assignment has been deleted.'));
        } else {
            $this->Flash->error(__('The course instructor assignment could not be deleted. Please, try again.'));
        }

        return $this->redirect(['action' => 'index']);
    }
}
