<?php
namespace App\Controller;

use App\Controller\AppController;

class AttendancesController extends AppController
{

    public function index()
    {
        $this->paginate = [
            'contain' => ['Students', 'PublishedCourses'],
        ];
        $attendances = $this->paginate($this->Attendances);

        $this->set(compact('attendances'));
    }

    public function view($id = null)
    {
        $attendance = $this->Attendances->get($id, [
            'contain' => ['Students', 'PublishedCourses'],
        ]);

        $this->set('attendance', $attendance);
    }

    public function add()
    {
        $attendance = $this->Attendances->newEntity();
        if ($this->request->is('post')) {
            $attendance = $this->Attendances->patchEntity($attendance, $this->request->getData());
            if ($this->Attendances->save($attendance)) {
                $this->Flash->success(__('The attendance has been saved.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The attendance could not be saved. Please, try again.'));
        }

        $this->set(compact('attendance'));
    }

    public function edit($id = null)
    {
        $attendance = $this->Attendances->get($id, [
            'contain' => [],
        ]);
        if ($this->request->is(['patch', 'post', 'put'])) {
            $attendance = $this->Attendances->patchEntity($attendance, $this->request->getData());
            if ($this->Attendances->save($attendance)) {
                $this->Flash->success(__('The attendance has been saved.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The attendance could not be saved. Please, try again.'));
        }

        $this->set(compact('attendance'));
    }


    public function delete($id = null)
    {
        $this->request->allowMethod(['post', 'delete']);
        $attendance = $this->Attendances->get($id);
        if ($this->Attendances->delete($attendance)) {
            $this->Flash->success(__('The attendance has been deleted.'));
        } else {
            $this->Flash->error(__('The attendance could not be deleted. Please, try again.'));
        }

        return $this->redirect(['action' => 'index']);
    }
}
