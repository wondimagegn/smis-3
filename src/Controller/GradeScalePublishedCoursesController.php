<?php
namespace App\Controller;

use App\Controller\AppController;


class GradeScalePublishedCoursesController extends AppController
{

    public function index()
    {
        $this->paginate = [
            'contain' => ['GradeScales', 'PublishedCourses'],
        ];
        $gradeScalePublishedCourses = $this->paginate($this->GradeScalePublishedCourses);

        $this->set(compact('gradeScalePublishedCourses'));
    }

    public function view($id = null)
    {
        $gradeScalePublishedCourse = $this->GradeScalePublishedCourses->get($id, [
            'contain' => ['GradeScales', 'PublishedCourses'],
        ]);

        $this->set('gradeScalePublishedCourse', $gradeScalePublishedCourse);
    }

    public function add()
    {
        $gradeScalePublishedCourse = $this->GradeScalePublishedCourses->newEntity();
        if ($this->request->is('post')) {
            $gradeScalePublishedCourse = $this->GradeScalePublishedCourses->patchEntity($gradeScalePublishedCourse, $this->request->getData());
            if ($this->GradeScalePublishedCourses->save($gradeScalePublishedCourse)) {
                $this->Flash->success(__('The grade scale published course has been saved.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The grade scale published course could not be saved. Please, try again.'));
        }
        $this->set(compact('gradeScalePublishedCourse'));
    }

    public function edit($id = null)
    {
        $gradeScalePublishedCourse = $this->GradeScalePublishedCourses->get($id, [
            'contain' => [],
        ]);
        if ($this->request->is(['patch', 'post', 'put'])) {
            $gradeScalePublishedCourse = $this->GradeScalePublishedCourses->patchEntity($gradeScalePublishedCourse, $this->request->getData());
            if ($this->GradeScalePublishedCourses->save($gradeScalePublishedCourse)) {
                $this->Flash->success(__('The grade scale published course has been saved.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The grade scale published course could not be saved. Please, try again.'));
        }
       $this->set(compact('gradeScalePublishedCourse'));
    }


    public function delete($id = null)
    {
        $this->request->allowMethod(['post', 'delete']);
        $gradeScalePublishedCourse = $this->GradeScalePublishedCourses->get($id);
        if ($this->GradeScalePublishedCourses->delete($gradeScalePublishedCourse)) {
            $this->Flash->success(__('The grade scale published course has been deleted.'));
        } else {
            $this->Flash->error(__('The grade scale published course could not be deleted. Please, try again.'));
        }

        return $this->redirect(['action' => 'index']);
    }
}
