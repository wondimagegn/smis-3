<?php
namespace App\Controller;

use App\Controller\AppController;

class StudyProgramsController extends AppController
{

    public function index()
    {
        $studyPrograms = $this->paginate($this->StudyPrograms);

        $this->set(compact('studyPrograms'));
    }

    public function view($id = null)
    {
        $studyProgram = $this->StudyPrograms->get($id, [
            'contain' => ['DepartmentStudyPrograms'],
        ]);

        $this->set('studyProgram', $studyProgram);
    }

    public function add()
    {
        $studyProgram = $this->StudyPrograms->newEntity();
        if ($this->request->is('post')) {
            $studyProgram = $this->StudyPrograms->patchEntity($studyProgram, $this->request->getData());
            if ($this->StudyPrograms->save($studyProgram)) {
                $this->Flash->success(__('The study program has been saved.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The study program could not be saved. Please, try again.'));
        }
        $this->set(compact('studyProgram'));
    }

    public function edit($id = null)
    {
        $studyProgram = $this->StudyPrograms->get($id, [
            'contain' => [],
        ]);
        if ($this->request->is(['patch', 'post', 'put'])) {
            $studyProgram = $this->StudyPrograms->patchEntity($studyProgram, $this->request->getData());
            if ($this->StudyPrograms->save($studyProgram)) {
                $this->Flash->success(__('The study program has been saved.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The study program could not be saved. Please, try again.'));
        }
        $this->set(compact('studyProgram'));
    }

    public function delete($id = null)
    {
        $this->request->allowMethod(['post', 'delete']);
        $studyProgram = $this->StudyPrograms->get($id);
        if ($this->StudyPrograms->delete($studyProgram)) {
            $this->Flash->success(__('The study program has been deleted.'));
        } else {
            $this->Flash->error(__('The study program could not be deleted. Please, try again.'));
        }

        return $this->redirect(['action' => 'index']);
    }
}
