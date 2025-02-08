<?php
namespace App\Controller;

use App\Controller\AppController;

class CoursesController extends AppController
{

    public function index()
    {
        $this->paginate = [
            'contain' => ['Curriculums', 'Departments', 'CourseCategories', 'GradeTypes', 'YearLevels'],
        ];
        $courses = $this->paginate($this->Courses);

        $this->set(compact('courses'));
    }

    public function view($id = null)
    {
        $course = $this->Courses->get($id, [
            'contain' => ['Curriculums', 'Departments', 'CourseCategories', 'GradeTypes', 'YearLevels', 'Books', 'Journals', 'Weblinks', 'Staffs', 'Students', 'CourseExemptions', 'ExitExams', 'GraduationWorks', 'Prerequisites', 'PublishedCourses'],
        ]);

        $this->set('course', $course);
    }

    public function add()
    {
        $course = $this->Courses->newEntity();
        if ($this->request->is('post')) {
            $course = $this->Courses->patchEntity($course, $this->request->getData());
            if ($this->Courses->save($course)) {
                $this->Flash->success(__('The course has been saved.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The course could not be saved. Please, try again.'));
        }

        $this->set(compact('course'));
    }

    public function edit($id = null)
    {
        $course = $this->Courses->get($id, [
            'contain' => ['Books', 'Journals', 'Weblinks', 'Staffs', 'Students'],
        ]);
        if ($this->request->is(['patch', 'post', 'put'])) {
            $course = $this->Courses->patchEntity($course, $this->request->getData());
            if ($this->Courses->save($course)) {
                $this->Flash->success(__('The course has been saved.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The course could not be saved. Please, try again.'));
        }

        $this->set(compact('course'));
    }

    public function delete($id = null)
    {
        $this->request->allowMethod(['post', 'delete']);
        $course = $this->Courses->get($id);
        if ($this->Courses->delete($course)) {
            $this->Flash->success(__('The course has been deleted.'));
        } else {
            $this->Flash->error(__('The course could not be deleted. Please, try again.'));
        }

        return $this->redirect(['action' => 'index']);
    }
}
