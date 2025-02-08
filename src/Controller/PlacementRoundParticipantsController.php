<?php
namespace App\Controller;

use App\Controller\AppController;

class PlacementRoundParticipantsController extends AppController
{

    public function index()
    {
        $this->paginate = [
            'contain' => ['Programs', 'ProgramTypes'],
        ];
        $placementRoundParticipants = $this->paginate($this->PlacementRoundParticipants);

        $this->set(compact('placementRoundParticipants'));
    }

    public function view($id = null)
    {
        $placementRoundParticipant = $this->PlacementRoundParticipants->get($id, [
            'contain' => ['Programs', 'ProgramTypes', 'PlacementEntranceExamResultEntries', 'PlacementParticipatingStudents', 'PlacementPreferences'],
        ]);

        $this->set('placementRoundParticipant', $placementRoundParticipant);
    }

    public function add()
    {
        $placementRoundParticipant = $this->PlacementRoundParticipants->newEntity();
        if ($this->request->is('post')) {
            $placementRoundParticipant = $this->PlacementRoundParticipants->patchEntity($placementRoundParticipant, $this->request->getData());
            if ($this->PlacementRoundParticipants->save($placementRoundParticipant)) {
                $this->Flash->success(__('The placement round participant has been saved.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The placement round participant could not be saved. Please, try again.'));
        }
        $this->set(compact('placementRoundParticipant'));
    }

    public function edit($id = null)
    {
        $placementRoundParticipant = $this->PlacementRoundParticipants->get($id, [
            'contain' => [],
        ]);
        if ($this->request->is(['patch', 'post', 'put'])) {
            $placementRoundParticipant = $this->PlacementRoundParticipants->patchEntity($placementRoundParticipant, $this->request->getData());
            if ($this->PlacementRoundParticipants->save($placementRoundParticipant)) {
                $this->Flash->success(__('The placement round participant has been saved.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The placement round participant could not be saved. Please, try again.'));
        }


        $this->set(compact('placementRoundParticipant', 'programs', 'programTypes'));
    }

    public function delete($id = null)
    {
        $this->request->allowMethod(['post', 'delete']);
        $placementRoundParticipant = $this->PlacementRoundParticipants->get($id);
        if ($this->PlacementRoundParticipants->delete($placementRoundParticipant)) {
            $this->Flash->success(__('The placement round participant has been deleted.'));
        } else {
            $this->Flash->error(__('The placement round participant could not be deleted. Please, try again.'));
        }

        return $this->redirect(['action' => 'index']);
    }
}
