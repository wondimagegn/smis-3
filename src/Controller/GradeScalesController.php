<?php
namespace App\Controller;

use App\Controller\AppController;

class GradeScalesController extends AppController
{

    public function index()
    {
        $this->paginate = [
            'contain' => ['GradeTypes', 'Programs'],
        ];
        $gradeScales = $this->paginate($this->GradeScales);

        $this->set(compact('gradeScales'));
    }

    public function view($id = null)
    {
        $gradeScale = $this->GradeScales->get($id, [
            'contain' => ['GradeTypes', 'Programs', 'ExamGrades', 'GradeScaleDetails', 'GradeScalePublishedCourses', 'PublishedCourses', 'RejectedExamGrades'],
        ]);

        $this->set('gradeScale', $gradeScale);
    }

    public function add()
    {
        $gradeScale = $this->GradeScales->newEntity();
        if ($this->request->is('post')) {
            $gradeScale = $this->GradeScales->patchEntity($gradeScale, $this->request->getData());
            if ($this->GradeScales->save($gradeScale)) {
                $this->Flash->success(__('The grade scale has been saved.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The grade scale could not be saved. Please, try again.'));
        }

        $this->set(compact('gradeScale'));
    }


    public function edit($id = null)
    {
        $gradeScale = $this->GradeScales->get($id, [
            'contain' => [],
        ]);
        if ($this->request->is(['patch', 'post', 'put'])) {
            $gradeScale = $this->GradeScales->patchEntity($gradeScale, $this->request->getData());
            if ($this->GradeScales->save($gradeScale)) {
                $this->Flash->success(__('The grade scale has been saved.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The grade scale could not be saved. Please, try again.'));
        }

        $this->set(compact('gradeScale'));
    }

    public function delete($id = null)
    {
        $this->request->allowMethod(['post', 'delete']);
        $gradeScale = $this->GradeScales->get($id);
        if ($this->GradeScales->delete($gradeScale)) {
            $this->Flash->success(__('The grade scale has been deleted.'));
        } else {
            $this->Flash->error(__('The grade scale could not be deleted. Please, try again.'));
        }

        return $this->redirect(['action' => 'index']);
    }
}
