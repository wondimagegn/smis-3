<?php
namespace App\Controller;

use App\Controller\AppController;

class GradeScaleDetailsController extends AppController
{

    public function index()
    {
        $this->paginate = [
            'contain' => ['GradeScales', 'Grades'],
        ];
        $gradeScaleDetails = $this->paginate($this->GradeScaleDetails);

        $this->set(compact('gradeScaleDetails'));
    }

    public function view($id = null)
    {
        $gradeScaleDetail = $this->GradeScaleDetails->get($id, [
            'contain' => ['GradeScales', 'Grades'],
        ]);

        $this->set('gradeScaleDetail', $gradeScaleDetail);
    }

    public function add()
    {
        $gradeScaleDetail = $this->GradeScaleDetails->newEntity();
        if ($this->request->is('post')) {
            $gradeScaleDetail = $this->GradeScaleDetails->patchEntity($gradeScaleDetail, $this->request->getData());
            if ($this->GradeScaleDetails->save($gradeScaleDetail)) {
                $this->Flash->success(__('The grade scale detail has been saved.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The grade scale detail could not be saved. Please, try again.'));
        }

        $this->set(compact('gradeScaleDetail'));
    }

    public function edit($id = null)
    {
        $gradeScaleDetail = $this->GradeScaleDetails->get($id, [
            'contain' => [],
        ]);
        if ($this->request->is(['patch', 'post', 'put'])) {
            $gradeScaleDetail = $this->GradeScaleDetails->patchEntity($gradeScaleDetail, $this->request->getData());
            if ($this->GradeScaleDetails->save($gradeScaleDetail)) {
                $this->Flash->success(__('The grade scale detail has been saved.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The grade scale detail could not be saved. Please, try again.'));
        }

        $this->set(compact('gradeScaleDetail', 'gradeScales', 'grades'));
    }

    public function delete($id = null)
    {
        $this->request->allowMethod(['post', 'delete']);
        $gradeScaleDetail = $this->GradeScaleDetails->get($id);
        if ($this->GradeScaleDetails->delete($gradeScaleDetail)) {
            $this->Flash->success(__('The grade scale detail has been deleted.'));
        } else {
            $this->Flash->error(__('The grade scale detail could not be deleted. Please, try again.'));
        }

        return $this->redirect(['action' => 'index']);
    }
}
