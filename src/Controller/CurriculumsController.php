<?php
namespace App\Controller;

use App\Controller\AppController;

class CurriculumsController extends AppController
{

    public function index()
    {
        $this->paginate = [
            'contain' => ['Departments', 'Programs', 'ProgramTypes', 'DepartmentStudyPrograms'],
        ];
        $curriculums = $this->paginate($this->Curriculums);

        $this->set(compact('curriculums'));
    }


    public function view($id = null)
    {
        $curriculum = $this->Curriculums->get($id, [
            'contain' => ['Departments', 'Programs', 'ProgramTypes', 'DepartmentStudyPrograms', 'AcceptedStudents', 'CourseCategories', 'Courses', 'CurriculumAttachments', 'OtherAcademicRules', 'Sections', 'Students'],
        ]);

        $this->set('curriculum', $curriculum);
    }

    public function add()
    {
        $curriculum = $this->Curriculums->newEntity();
        if ($this->request->is('post')) {
            $curriculum = $this->Curriculums->patchEntity($curriculum, $this->request->getData());
            if ($this->Curriculums->save($curriculum)) {
                $this->Flash->success(__('The curriculum has been saved.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The curriculum could not be saved. Please, try again.'));
        }
      $this->set(compact('curriculum'));
    }


    public function edit($id = null)
    {
        $curriculum = $this->Curriculums->get($id, [
            'contain' => [],
        ]);
        if ($this->request->is(['patch', 'post', 'put'])) {
            $curriculum = $this->Curriculums->patchEntity($curriculum, $this->request->getData());
            if ($this->Curriculums->save($curriculum)) {
                $this->Flash->success(__('The curriculum has been saved.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The curriculum could not be saved. Please, try again.'));
        }
        $this->set(compact('curriculum'));
    }

    public function delete($id = null)
    {
        $this->request->allowMethod(['post', 'delete']);
        $curriculum = $this->Curriculums->get($id);
        if ($this->Curriculums->delete($curriculum)) {
            $this->Flash->success(__('The curriculum has been deleted.'));
        } else {
            $this->Flash->error(__('The curriculum could not be deleted. Please, try again.'));
        }

        return $this->redirect(['action' => 'index']);
    }
}
