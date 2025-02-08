<?php
namespace App\Controller;

use App\Controller\AppController;

class ExamGradesController extends AppController
{

    public function index()
    {
        $this->paginate = [
            'contain' => ['CourseRegistrations', 'CourseAdds', 'CourseInstructorAssignments',
                'MakeupExams', 'GradeScales'],
        ];
        $examGrades = $this->paginate($this->ExamGrades);

        $this->set(compact('examGrades'));
    }

    public function view($id = null)
    {
        $examGrade = $this->ExamGrades->get($id, [
            'contain' => ['CourseRegistrations', 'CourseAdds', 'CourseInstructorAssignments', 'MakeupExams', 'GradeScales', 'ExamGradeChanges'],
        ]);

        $this->set('examGrade', $examGrade);
    }

    public function add()
    {
        $examGrade = $this->ExamGrades->newEntity();
        if ($this->request->is('post')) {
            $examGrade = $this->ExamGrades->patchEntity($examGrade, $this->request->getData());
            if ($this->ExamGrades->save($examGrade)) {
                $this->Flash->success(__('The exam grade has been saved.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The exam grade could not be saved. Please, try again.'));
        }

        $this->set(compact('examGrade'));
    }


    public function edit($id = null)
    {
        $examGrade = $this->ExamGrades->get($id, [
            'contain' => [],
        ]);
        if ($this->request->is(['patch', 'post', 'put'])) {
            $examGrade = $this->ExamGrades->patchEntity($examGrade, $this->request->getData());
            if ($this->ExamGrades->save($examGrade)) {
                $this->Flash->success(__('The exam grade has been saved.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The exam grade could not be saved. Please, try again.'));
        }

        $this->set(compact('examGrade'));
    }

    public function delete($id = null)
    {
        $this->request->allowMethod(['post', 'delete']);
        $examGrade = $this->ExamGrades->get($id);
        if ($this->ExamGrades->delete($examGrade)) {
            $this->Flash->success(__('The exam grade has been deleted.'));
        } else {
            $this->Flash->error(__('The exam grade could not be deleted. Please, try again.'));
        }

        return $this->redirect(['action' => 'index']);
    }
}
