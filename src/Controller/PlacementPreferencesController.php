<?php
namespace App\Controller;

use App\Controller\AppController;

class PlacementPreferencesController extends AppController
{

    public function index()
    {
        $this->paginate = [
            'contain' => ['AcceptedStudents', 'Students', 'PlacementRoundParticipants', 'Users'],
        ];
        $placementPreferences = $this->paginate($this->PlacementPreferences);

        $this->set(compact('placementPreferences'));
    }

    public function view($id = null)
    {
        $placementPreference = $this->PlacementPreferences->get($id, [
            'contain' => ['AcceptedStudents', 'Students', 'PlacementRoundParticipants', 'Users'],
        ]);

        $this->set('placementPreference', $placementPreference);
    }

    public function add()
    {
        $placementPreference = $this->PlacementPreferences->newEntity();
        if ($this->request->is('post')) {
            $placementPreference = $this->PlacementPreferences->patchEntity($placementPreference, $this->request->getData());
            if ($this->PlacementPreferences->save($placementPreference)) {
                $this->Flash->success(__('The placement preference has been saved.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The placement preference could not be saved. Please, try again.'));
        }
        $this->set(compact('placementPreference'));
    }


    public function edit($id = null)
    {
        $placementPreference = $this->PlacementPreferences->get($id, [
            'contain' => [],
        ]);
        if ($this->request->is(['patch', 'post', 'put'])) {
            $placementPreference = $this->PlacementPreferences->patchEntity($placementPreference, $this->request->getData());
            if ($this->PlacementPreferences->save($placementPreference)) {
                $this->Flash->success(__('The placement preference has been saved.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The placement preference could not be saved. Please, try again.'));
        }
       $this->set(compact('placementPreference'));
    }

    public function delete($id = null)
    {
        $this->request->allowMethod(['post', 'delete']);
        $placementPreference = $this->PlacementPreferences->get($id);
        if ($this->PlacementPreferences->delete($placementPreference)) {
            $this->Flash->success(__('The placement preference has been deleted.'));
        } else {
            $this->Flash->error(__('The placement preference could not be deleted. Please, try again.'));
        }

        return $this->redirect(['action' => 'index']);
    }
}
