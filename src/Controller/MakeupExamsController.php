<?php
namespace App\Controller;

use App\Controller\AppController;

class MakeupExamsController extends AppController
{

    public function index()
    {
        $this->paginate = [
            'contain' => ['Students', 'PublishedCourses', 'CourseRegistrations', 'CourseAdds'],
        ];
        $makeupExams = $this->paginate($this->MakeupExams);

        $this->set(compact('makeupExams'));
    }

    public function view($id = null)
    {
        $makeupExam = $this->MakeupExams->get($id, [
            'contain' => ['Students', 'PublishedCourses', 'CourseRegistrations', 'CourseAdds', 'ExamGradeChanges', 'ExamGrades', 'ExamResults', 'RejectedExamGrades'],
        ]);

        $this->set('makeupExam', $makeupExam);
    }


    public function add()
    {
        $makeupExam = $this->MakeupExams->newEntity();
        if ($this->request->is('post')) {
            $makeupExam = $this->MakeupExams->patchEntity($makeupExam, $this->request->getData());
            if ($this->MakeupExams->save($makeupExam)) {
                $this->Flash->success(__('The makeup exam has been saved.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The makeup exam could not be saved. Please, try again.'));
        }
        $this->set(compact('makeupExam'));
    }


    public function edit($id = null)
    {
        $makeupExam = $this->MakeupExams->get($id, [
            'contain' => [],
        ]);
        if ($this->request->is(['patch', 'post', 'put'])) {
            $makeupExam = $this->MakeupExams->patchEntity($makeupExam, $this->request->getData());
            if ($this->MakeupExams->save($makeupExam)) {
                $this->Flash->success(__('The makeup exam has been saved.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The makeup exam could not be saved. Please, try again.'));
        }
        $this->set(compact('makeupExam'));
    }


    public function delete($id = null)
    {
        $this->request->allowMethod(['post', 'delete']);
        $makeupExam = $this->MakeupExams->get($id);
        if ($this->MakeupExams->delete($makeupExam)) {
            $this->Flash->success(__('The makeup exam has been deleted.'));
        } else {
            $this->Flash->error(__('The makeup exam could not be deleted. Please, try again.'));
        }

        return $this->redirect(['action' => 'index']);
    }
}
