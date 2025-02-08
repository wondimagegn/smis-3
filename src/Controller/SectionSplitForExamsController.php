<?php
namespace App\Controller;

use App\Controller\AppController;

class SectionSplitForExamsController extends AppController
{

    public function index()
    {
        $this->paginate = [
            'contain' => ['Sections', 'PublishedCourses'],
        ];
        $sectionSplitForExams = $this->paginate($this->SectionSplitForExams);

        $this->set(compact('sectionSplitForExams'));
    }

    public function view($id = null)
    {
        $sectionSplitForExam = $this->SectionSplitForExams->get($id, [
            'contain' => ['Sections', 'PublishedCourses', 'ExamSplitSections'],
        ]);

        $this->set('sectionSplitForExam', $sectionSplitForExam);
    }

    public function add()
    {
        $sectionSplitForExam = $this->SectionSplitForExams->newEntity();
        if ($this->request->is('post')) {
            $sectionSplitForExam = $this->SectionSplitForExams->patchEntity($sectionSplitForExam, $this->request->getData());
            if ($this->SectionSplitForExams->save($sectionSplitForExam)) {
                $this->Flash->success(__('The section split for exam has been saved.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The section split for exam could not be saved. Please, try again.'));
        }
        $this->set(compact('sectionSplitForExam'));
    }

    public function edit($id = null)
    {
        $sectionSplitForExam = $this->SectionSplitForExams->get($id, [
            'contain' => [],
        ]);
        if ($this->request->is(['patch', 'post', 'put'])) {
            $sectionSplitForExam = $this->SectionSplitForExams->patchEntity($sectionSplitForExam, $this->request->getData());
            if ($this->SectionSplitForExams->save($sectionSplitForExam)) {
                $this->Flash->success(__('The section split for exam has been saved.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The section split for exam could not be saved. Please, try again.'));
        }

        $this->set(compact('sectionSplitForExam'));
    }


    public function delete($id = null)
    {
        $this->request->allowMethod(['post', 'delete']);
        $sectionSplitForExam = $this->SectionSplitForExams->get($id);
        if ($this->SectionSplitForExams->delete($sectionSplitForExam)) {
            $this->Flash->success(__('The section split for exam has been deleted.'));
        } else {
            $this->Flash->error(__('The section split for exam could not be deleted. Please, try again.'));
        }

        return $this->redirect(['action' => 'index']);
    }
}
