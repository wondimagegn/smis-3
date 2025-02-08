<?php
namespace App\Controller;

use App\Controller\AppController;

class PlacementParticipatingStudentsController extends AppController
{
    public function index()
    {
        $this->paginate = [
            'contain' => ['AcceptedStudents', 'Students', 'Programs', 'ProgramTypes', 'PlacementRoundParticipants'],
        ];
        $placementParticipatingStudents = $this->paginate($this->PlacementParticipatingStudents);

        $this->set(compact('placementParticipatingStudents'));
    }


    public function view($id = null)
    {
        $placementParticipatingStudent = $this->PlacementParticipatingStudents->get($id, [
            'contain' => ['AcceptedStudents', 'Students', 'Programs', 'ProgramTypes', 'PlacementRoundParticipants'],
        ]);

        $this->set('placementParticipatingStudent', $placementParticipatingStudent);
    }

    public function add()
    {
        $placementParticipatingStudent = $this->PlacementParticipatingStudents->newEntity();
        if ($this->request->is('post')) {
            $placementParticipatingStudent = $this->PlacementParticipatingStudents->patchEntity($placementParticipatingStudent, $this->request->getData());
            if ($this->PlacementParticipatingStudents->save($placementParticipatingStudent)) {
                $this->Flash->success(__('The placement participating student has been saved.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The placement participating student could not be saved. Please, try again.'));
        }
       $this->set(compact('placementParticipatingStudent'));
    }

    public function edit($id = null)
    {
        $placementParticipatingStudent = $this->PlacementParticipatingStudents->get($id, [
            'contain' => [],
        ]);
        if ($this->request->is(['patch', 'post', 'put'])) {
            $placementParticipatingStudent = $this->PlacementParticipatingStudents->patchEntity($placementParticipatingStudent, $this->request->getData());
            if ($this->PlacementParticipatingStudents->save($placementParticipatingStudent)) {
                $this->Flash->success(__('The placement participating student has been saved.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The placement participating student could not be saved. Please, try again.'));
        }
      $this->set(compact('placementParticipatingStudent'));
    }

    public function delete($id = null)
    {
        $this->request->allowMethod(['post', 'delete']);
        $placementParticipatingStudent = $this->PlacementParticipatingStudents->get($id);
        if ($this->PlacementParticipatingStudents->delete($placementParticipatingStudent)) {
            $this->Flash->success(__('The placement participating student has been deleted.'));
        } else {
            $this->Flash->error(__('The placement participating student could not be deleted. Please, try again.'));
        }

        return $this->redirect(['action' => 'index']);
    }
}
