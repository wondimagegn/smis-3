<?php
namespace App\Controller;

use App\Controller\AppController;

class ExamSchedulesController extends AppController
{

    public function index()
    {
        $this->paginate = [
            'contain' => ['ClassRooms', 'ExamSplitSections', 'PublishedCourses'],
        ];
        $examSchedules = $this->paginate($this->ExamSchedules);

        $this->set(compact('examSchedules'));
    }

    public function view($id = null)
    {
        $examSchedule = $this->ExamSchedules->get($id, [
            'contain' => ['ClassRooms', 'ExamSplitSections', 'PublishedCourses', 'Invigilators'],
        ]);

        $this->set('examSchedule', $examSchedule);
    }

    public function add()
    {
        $examSchedule = $this->ExamSchedules->newEntity();
        if ($this->request->is('post')) {
            $examSchedule = $this->ExamSchedules->patchEntity($examSchedule, $this->request->getData());
            if ($this->ExamSchedules->save($examSchedule)) {
                $this->Flash->success(__('The exam schedule has been saved.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The exam schedule could not be saved. Please, try again.'));
        }
      $this->set(compact('examSchedule'));
    }


    public function edit($id = null)
    {
        $examSchedule = $this->ExamSchedules->get($id, [
            'contain' => [],
        ]);
        if ($this->request->is(['patch', 'post', 'put'])) {
            $examSchedule = $this->ExamSchedules->patchEntity($examSchedule, $this->request->getData());
            if ($this->ExamSchedules->save($examSchedule)) {
                $this->Flash->success(__('The exam schedule has been saved.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The exam schedule could not be saved. Please, try again.'));
        }
       $this->set(compact('examSchedule'));
    }


    public function delete($id = null)
    {
        $this->request->allowMethod(['post', 'delete']);
        $examSchedule = $this->ExamSchedules->get($id);
        if ($this->ExamSchedules->delete($examSchedule)) {
            $this->Flash->success(__('The exam schedule has been deleted.'));
        } else {
            $this->Flash->error(__('The exam schedule could not be deleted. Please, try again.'));
        }

        return $this->redirect(['action' => 'index']);
    }
}
