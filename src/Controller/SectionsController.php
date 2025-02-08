<?php
namespace App\Controller;

use App\Controller\AppController;

class SectionsController extends AppController
{

    public function index()
    {
        $this->paginate = [
            'contain' => ['Colleges', 'Departments', 'YearLevels', 'Programs', 'ProgramTypes', 'Curriculums'],
        ];
        $sections = $this->paginate($this->Sections);

        $this->set(compact('sections'));
    }


    public function view($id = null)
    {
        $section = $this->Sections->get($id, [
            'contain' => ['Colleges', 'Departments', 'YearLevels', 'Programs', 'ProgramTypes', 'Curriculums',
                'Students', 'MergedSectionsCourses', 'CourseInstructorAssignments', 'CourseRegistrations',
                'CourseSchedules', 'ExamTypes', 'MergedSectionsExams', 'PublishedCourses',
                'SectionSplitForExams', 'SectionSplitForPublishedCourses'],
        ]);

        $this->set('section', $section);
    }


    public function add()
    {
        $section = $this->Sections->newEntity();
        if ($this->request->is('post')) {
            $section = $this->Sections->patchEntity($section, $this->request->getData());
            if ($this->Sections->save($section)) {
                $this->Flash->success(__('The section has been saved.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The section could not be saved. Please, try again.'));
        }

        $this->set(compact('section'));
    }


    public function edit($id = null)
    {
        $section = $this->Sections->get($id, [
            'contain' => ['Students', 'MergedSectionsCourses'],
        ]);
        if ($this->request->is(['patch', 'post', 'put'])) {
            $section = $this->Sections->patchEntity($section, $this->request->getData());
            if ($this->Sections->save($section)) {
                $this->Flash->success(__('The section has been saved.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The section could not be saved. Please, try again.'));
        }

        $this->set(compact('section'));
    }

    public function delete($id = null)
    {
        $this->request->allowMethod(['post', 'delete']);
        $section = $this->Sections->get($id);
        if ($this->Sections->delete($section)) {
            $this->Flash->success(__('The section has been deleted.'));
        } else {
            $this->Flash->error(__('The section could not be deleted. Please, try again.'));
        }

        return $this->redirect(['action' => 'index']);
    }
}
