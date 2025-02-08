<?php
namespace App\Controller;

use App\Controller\AppController;

class ExamGradeChangesController extends AppController
{

    public function index()
    {
        $this->paginate = [
            'contain' => ['ExamGrades', 'MakeupExams'],
        ];
        $examGradeChanges = $this->paginate($this->ExamGradeChanges);

        $this->set(compact('examGradeChanges'));
    }

    public function view($id = null)
    {
        $examGradeChange = $this->ExamGradeChanges->get($id, [
            'contain' => ['ExamGrades', 'MakeupExams'],
        ]);

        $this->set('examGradeChange', $examGradeChange);
    }

    public function add()
    {
        $examGradeChange = $this->ExamGradeChanges->newEntity();
        if ($this->request->is('post')) {
            $examGradeChange = $this->ExamGradeChanges->patchEntity($examGradeChange, $this->request->getData());
            if ($this->ExamGradeChanges->save($examGradeChange)) {
                $this->Flash->success(__('The exam grade change has been saved.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The exam grade change could not be saved. Please, try again.'));
        }
        $this->set(compact('examGradeChange'));
    }


    public function edit($id = null)
    {
        $examGradeChange = $this->ExamGradeChanges->get($id, [
            'contain' => [],
        ]);
        if ($this->request->is(['patch', 'post', 'put'])) {
            $examGradeChange = $this->ExamGradeChanges->patchEntity($examGradeChange, $this->request->getData());
            if ($this->ExamGradeChanges->save($examGradeChange)) {
                $this->Flash->success(__('The exam grade change has been saved.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The exam grade change could not be saved. Please, try again.'));
        }

        $this->set(compact('examGradeChange'));
    }

    public function delete($id = null)
    {
        $this->request->allowMethod(['post', 'delete']);
        $examGradeChange = $this->ExamGradeChanges->get($id);
        if ($this->ExamGradeChanges->delete($examGradeChange)) {
            $this->Flash->success(__('The exam grade change has been deleted.'));
        } else {
            $this->Flash->error(__('The exam grade change could not be deleted. Please, try again.'));
        }

        return $this->redirect(['action' => 'index']);
    }
}
