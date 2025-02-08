<?php
namespace App\Controller;

use App\Controller\AppController;

class ExamSplitSectionsController extends AppController
{

    public function index()
    {
        $this->paginate = [
            'contain' => ['SectionSplitForExams'],
        ];
        $examSplitSections = $this->paginate($this->ExamSplitSections);

        $this->set(compact('examSplitSections'));
    }


    public function view($id = null)
    {
        $examSplitSection = $this->ExamSplitSections->get($id, [
            'contain' => ['SectionSplitForExams', 'Students', 'ExamSchedules'],
        ]);

        $this->set('examSplitSection', $examSplitSection);
    }

    public function add()
    {
        $examSplitSection = $this->ExamSplitSections->newEntity();
        if ($this->request->is('post')) {
            $examSplitSection = $this->ExamSplitSections->patchEntity($examSplitSection, $this->request->getData());
            if ($this->ExamSplitSections->save($examSplitSection)) {
                $this->Flash->success(__('The exam split section has been saved.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The exam split section could not be saved. Please, try again.'));
        }
         $this->set(compact('examSplitSection'));
    }


    public function edit($id = null)
    {
        $examSplitSection = $this->ExamSplitSections->get($id, [
            'contain' => ['Students'],
        ]);
        if ($this->request->is(['patch', 'post', 'put'])) {
            $examSplitSection = $this->ExamSplitSections->patchEntity($examSplitSection, $this->request->getData());
            if ($this->ExamSplitSections->save($examSplitSection)) {
                $this->Flash->success(__('The exam split section has been saved.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The exam split section could not be saved. Please, try again.'));
        }

        $this->set(compact('examSplitSection'));
    }


    public function delete($id = null)
    {
        $this->request->allowMethod(['post', 'delete']);
        $examSplitSection = $this->ExamSplitSections->get($id);
        if ($this->ExamSplitSections->delete($examSplitSection)) {
            $this->Flash->success(__('The exam split section has been deleted.'));
        } else {
            $this->Flash->error(__('The exam split section could not be deleted. Please, try again.'));
        }

        return $this->redirect(['action' => 'index']);
    }
}
