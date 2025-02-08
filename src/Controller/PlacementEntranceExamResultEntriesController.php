<?php
namespace App\Controller;

use App\Controller\AppController;

class PlacementEntranceExamResultEntriesController extends AppController
{

    public function index()
    {
        $this->paginate = [
            'contain' => ['AcceptedStudents', 'Students', 'PlacementRoundParticipants'],
        ];
        $placementEntranceExamResultEntries = $this->paginate($this->PlacementEntranceExamResultEntries);

        $this->set(compact('placementEntranceExamResultEntries'));
    }

    public function view($id = null)
    {
        $placementEntranceExamResultEntry = $this->PlacementEntranceExamResultEntries->get($id, [
            'contain' => ['AcceptedStudents', 'Students', 'PlacementRoundParticipants'],
        ]);

        $this->set('placementEntranceExamResultEntry', $placementEntranceExamResultEntry);
    }

    public function add()
    {
        $placementEntranceExamResultEntry = $this->PlacementEntranceExamResultEntries->newEntity();
        if ($this->request->is('post')) {
            $placementEntranceExamResultEntry = $this->PlacementEntranceExamResultEntries->patchEntity($placementEntranceExamResultEntry, $this->request->getData());
            if ($this->PlacementEntranceExamResultEntries->save($placementEntranceExamResultEntry)) {
                $this->Flash->success(__('The placement entrance exam result entry has been saved.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The placement entrance exam result entry could not be saved. Please, try again.'));
        }
        $this->set(compact('placementEntranceExamResultEntry'));
    }


    public function edit($id = null)
    {
        $placementEntranceExamResultEntry = $this->PlacementEntranceExamResultEntries->get($id, [
            'contain' => [],
        ]);
        if ($this->request->is(['patch', 'post', 'put'])) {
            $placementEntranceExamResultEntry = $this->PlacementEntranceExamResultEntries->patchEntity($placementEntranceExamResultEntry, $this->request->getData());
            if ($this->PlacementEntranceExamResultEntries->save($placementEntranceExamResultEntry)) {
                $this->Flash->success(__('The placement entrance exam result entry has been saved.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The placement entrance exam result entry could not be saved. Please, try again.'));
        }
        $this->set(compact('placementEntranceExamResultEntry'));
    }

    public function delete($id = null)
    {
        $this->request->allowMethod(['post', 'delete']);
        $placementEntranceExamResultEntry = $this->PlacementEntranceExamResultEntries->get($id);
        if ($this->PlacementEntranceExamResultEntries->delete($placementEntranceExamResultEntry)) {
            $this->Flash->success(__('The placement entrance exam result entry has been deleted.'));
        } else {
            $this->Flash->error(__('The placement entrance exam result entry could not be deleted. Please, try again.'));
        }

        return $this->redirect(['action' => 'index']);
    }
}
