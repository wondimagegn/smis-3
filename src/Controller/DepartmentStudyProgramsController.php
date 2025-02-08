<?php
namespace App\Controller;

use App\Controller\AppController;

class DepartmentStudyProgramsController extends AppController
{

    public function index()
    {
        $this->paginate = [
            'contain' => ['Departments', 'StudyPrograms', 'ProgramModalities', 'Qualifications'],
        ];
        $departmentStudyPrograms = $this->paginate($this->DepartmentStudyPrograms);

        $this->set(compact('departmentStudyPrograms'));
    }

    public function view($id = null)
    {
        $departmentStudyProgram = $this->DepartmentStudyPrograms->get($id, [
            'contain' => ['Departments', 'StudyPrograms', 'ProgramModalities', 'Qualifications', 'Curriculums'],
        ]);

        $this->set('departmentStudyProgram', $departmentStudyProgram);
    }

    /**
     * Add method
     *
     * @return \Cake\Http\Response|null Redirects on successful add, renders view otherwise.
     */
    public function add()
    {
        $departmentStudyProgram = $this->DepartmentStudyPrograms->newEntity();
        if ($this->request->is('post')) {
            $departmentStudyProgram = $this->DepartmentStudyPrograms->patchEntity($departmentStudyProgram, $this->request->getData());
            if ($this->DepartmentStudyPrograms->save($departmentStudyProgram)) {
                $this->Flash->success(__('The department study program has been saved.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The department study program could not be saved. Please, try again.'));
        }
        $this->set(compact('departmentStudyProgram'));
    }


    public function edit($id = null)
    {
        $departmentStudyProgram = $this->DepartmentStudyPrograms->get($id, [
            'contain' => [],
        ]);
        if ($this->request->is(['patch', 'post', 'put'])) {
            $departmentStudyProgram = $this->DepartmentStudyPrograms->patchEntity($departmentStudyProgram, $this->request->getData());
            if ($this->DepartmentStudyPrograms->save($departmentStudyProgram)) {
                $this->Flash->success(__('The department study program has been saved.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The department study program could not be saved. Please, try again.'));
        }
         $this->set(compact('departmentStudyProgram'));
    }


    public function delete($id = null)
    {
        $this->request->allowMethod(['post', 'delete']);
        $departmentStudyProgram = $this->DepartmentStudyPrograms->get($id);
        if ($this->DepartmentStudyPrograms->delete($departmentStudyProgram)) {
            $this->Flash->success(__('The department study program has been deleted.'));
        } else {
            $this->Flash->error(__('The department study program could not be deleted. Please, try again.'));
        }

        return $this->redirect(['action' => 'index']);
    }
}
