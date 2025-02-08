<?php
namespace App\Controller;

use App\Controller\AppController;

class GradesController extends AppController
{

    public function index()
    {
        $this->paginate = [
            'contain' => ['GradeTypes'],
        ];
        $grades = $this->paginate($this->Grades);

        $this->set(compact('grades'));
    }


    public function view($id = null)
    {
        $grade = $this->Grades->get($id, [
            'contain' => ['GradeTypes', 'GradeScaleDetails'],
        ]);

        $this->set('grade', $grade);
    }

    public function add()
    {
        $grade = $this->Grades->newEntity();
        if ($this->request->is('post')) {
            $grade = $this->Grades->patchEntity($grade, $this->request->getData());
            if ($this->Grades->save($grade)) {
                $this->Flash->success(__('The grade has been saved.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The grade could not be saved. Please, try again.'));
        }

        $this->set(compact('grade'));
    }


    public function edit($id = null)
    {
        $grade = $this->Grades->get($id, [
            'contain' => [],
        ]);
        if ($this->request->is(['patch', 'post', 'put'])) {
            $grade = $this->Grades->patchEntity($grade, $this->request->getData());
            if ($this->Grades->save($grade)) {
                $this->Flash->success(__('The grade has been saved.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The grade could not be saved. Please, try again.'));
        }

        $this->set(compact('grade'));
    }


    public function delete($id = null)
    {
        $this->request->allowMethod(['post', 'delete']);
        $grade = $this->Grades->get($id);
        if ($this->Grades->delete($grade)) {
            $this->Flash->success(__('The grade has been deleted.'));
        } else {
            $this->Flash->error(__('The grade could not be deleted. Please, try again.'));
        }

        return $this->redirect(['action' => 'index']);
    }
}
