<?php
namespace App\Controller;

use App\Controller\AppController;

class CampusesController extends AppController
{

    public function index()
    {
        $campuses = $this->paginate($this->Campuses);

        $this->set(compact('campuses'));
    }

    public function view($id = null)
    {
        $campus = $this->Campuses->get($id, [
            'contain' => ['AcceptedStudents', 'ClassRoomBlocks', 'Colleges', 'DormitoryBlocks', 'MealHalls'],
        ]);

        $this->set('campus', $campus);
    }

    public function add()
    {
        $campus = $this->Campuses->newEntity();
        if ($this->request->is('post')) {
            $campus = $this->Campuses->patchEntity($campus, $this->request->getData());
            if ($this->Campuses->save($campus)) {
                $this->Flash->success(__('The campus has been saved.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The campus could not be saved. Please, try again.'));
        }
        $this->set(compact('campus'));
    }

    public function edit($id = null)
    {
        $campus = $this->Campuses->get($id, [
            'contain' => [],
        ]);
        if ($this->request->is(['patch', 'post', 'put'])) {
            $campus = $this->Campuses->patchEntity($campus, $this->request->getData());
            if ($this->Campuses->save($campus)) {
                $this->Flash->success(__('The campus has been saved.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The campus could not be saved. Please, try again.'));
        }
        $this->set(compact('campus'));
    }

    public function delete($id = null)
    {
        $this->request->allowMethod(['post', 'delete']);
        $campus = $this->Campuses->get($id);
        if ($this->Campuses->delete($campus)) {
            $this->Flash->success(__('The campus has been deleted.'));
        } else {
            $this->Flash->error(__('The campus could not be deleted. Please, try again.'));
        }

        return $this->redirect(['action' => 'index']);
    }
}
