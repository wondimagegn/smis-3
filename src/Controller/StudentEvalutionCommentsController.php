<?php
namespace App\Controller;

use App\Controller\AppController;

class StudentEvalutionCommentsController extends AppController
{

    public function index()
    {
        $this->paginate = [
            'contain' => ['InstructorEvalutionQuestions', 'Students', 'PublishedCourses'],
        ];
        $studentEvalutionComments = $this->paginate($this->StudentEvalutionComments);

        $this->set(compact('studentEvalutionComments'));
    }


    public function view($id = null)
    {
        $studentEvalutionComment = $this->StudentEvalutionComments->get($id, [
            'contain' => ['InstructorEvalutionQuestions', 'Students', 'PublishedCourses'],
        ]);

        $this->set('studentEvalutionComment', $studentEvalutionComment);
    }

    public function add()
    {
        $studentEvalutionComment = $this->StudentEvalutionComments->newEntity();
        if ($this->request->is('post')) {
            $studentEvalutionComment = $this->StudentEvalutionComments->patchEntity($studentEvalutionComment, $this->request->getData());
            if ($this->StudentEvalutionComments->save($studentEvalutionComment)) {
                $this->Flash->success(__('The student evalution comment has been saved.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The student evalution comment could not be saved. Please, try again.'));
        }
        $this->set(compact('studentEvalutionComment'));
    }

    public function edit($id = null)
    {
        $studentEvalutionComment = $this->StudentEvalutionComments->get($id, [
            'contain' => [],
        ]);
        if ($this->request->is(['patch', 'post', 'put'])) {
            $studentEvalutionComment = $this->StudentEvalutionComments->patchEntity($studentEvalutionComment, $this->request->getData());
            if ($this->StudentEvalutionComments->save($studentEvalutionComment)) {
                $this->Flash->success(__('The student evalution comment has been saved.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The student evalution comment could not be saved. Please, try again.'));
        }
        $this->set(compact('studentEvalutionComment'));
    }

    public function delete($id = null)
    {
        $this->request->allowMethod(['post', 'delete']);
        $studentEvalutionComment = $this->StudentEvalutionComments->get($id);
        if ($this->StudentEvalutionComments->delete($studentEvalutionComment)) {
            $this->Flash->success(__('The student evalution comment has been deleted.'));
        } else {
            $this->Flash->error(__('The student evalution comment could not be deleted. Please, try again.'));
        }

        return $this->redirect(['action' => 'index']);
    }
}
