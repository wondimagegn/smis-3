<?php
namespace App\Controller;

use App\Controller\AppController;

class StudentsCourseSplitSectionsController extends AppController
{

    public function index()
    {
        $this->paginate = [
            'contain' => ['CourseSplitSections', 'Students'],
        ];
        $studentsCourseSplitSections = $this->paginate($this->StudentsCourseSplitSections);

        $this->set(compact('studentsCourseSplitSections'));
    }

    public function view($id = null)
    {
        $studentsCourseSplitSection = $this->StudentsCourseSplitSections->get($id, [
            'contain' => ['CourseSplitSections', 'Students'],
        ]);

        $this->set('studentsCourseSplitSection', $studentsCourseSplitSection);
    }

    public function add()
    {
        $studentsCourseSplitSection = $this->StudentsCourseSplitSections->newEntity();
        if ($this->request->is('post')) {
            $studentsCourseSplitSection = $this->StudentsCourseSplitSections->patchEntity($studentsCourseSplitSection, $this->request->getData());
            if ($this->StudentsCourseSplitSections->save($studentsCourseSplitSection)) {
                $this->Flash->success(__('The students course split section has been saved.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The students course split section could not be saved. Please, try again.'));
        }
        $this->set(compact('studentsCourseSplitSection'));
    }


    public function edit($id = null)
    {
        $studentsCourseSplitSection = $this->StudentsCourseSplitSections->get($id, [
            'contain' => [],
        ]);
        if ($this->request->is(['patch', 'post', 'put'])) {
            $studentsCourseSplitSection = $this->StudentsCourseSplitSections->patchEntity($studentsCourseSplitSection, $this->request->getData());
            if ($this->StudentsCourseSplitSections->save($studentsCourseSplitSection)) {
                $this->Flash->success(__('The students course split section has been saved.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The students course split section could not be saved. Please, try again.'));
        }
        $this->set(compact('studentsCourseSplitSection'));
    }

    public function delete($id = null)
    {
        $this->request->allowMethod(['post', 'delete']);
        $studentsCourseSplitSection = $this->StudentsCourseSplitSections->get($id);
        if ($this->StudentsCourseSplitSections->delete($studentsCourseSplitSection)) {
            $this->Flash->success(__('The students course split section has been deleted.'));
        } else {
            $this->Flash->error(__('The students course split section could not be deleted. Please, try again.'));
        }

        return $this->redirect(['action' => 'index']);
    }
}
