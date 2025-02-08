<?php
namespace App\Controller;

use App\Controller\AppController;

class ReservedPlacesController extends AppController
{

    public function index()
    {
        $this->paginate = [
            'contain' => ['PlacementsResultsCriterias', 'ParticipatingDepartments', 'Colleges'],
        ];
        $reservedPlaces = $this->paginate($this->ReservedPlaces);

        $this->set(compact('reservedPlaces'));
    }


    public function view($id = null)
    {
        $reservedPlace = $this->ReservedPlaces->get($id, [
            'contain' => ['PlacementsResultsCriterias', 'ParticipatingDepartments', 'Colleges'],
        ]);

        $this->set('reservedPlace', $reservedPlace);
    }

    public function add()
    {
        $reservedPlace = $this->ReservedPlaces->newEntity();
        if ($this->request->is('post')) {
            $reservedPlace = $this->ReservedPlaces->patchEntity($reservedPlace, $this->request->getData());
            if ($this->ReservedPlaces->save($reservedPlace)) {
                $this->Flash->success(__('The reserved place has been saved.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The reserved place could not be saved. Please, try again.'));
        }

        $this->set(compact('reservedPlace'));
    }


    public function edit($id = null)
    {
        $reservedPlace = $this->ReservedPlaces->get($id, [
            'contain' => [],
        ]);
        if ($this->request->is(['patch', 'post', 'put'])) {
            $reservedPlace = $this->ReservedPlaces->patchEntity($reservedPlace, $this->request->getData());
            if ($this->ReservedPlaces->save($reservedPlace)) {
                $this->Flash->success(__('The reserved place has been saved.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The reserved place could not be saved. Please, try again.'));
        }

        $this->set(compact('reservedPlace'));
    }

    public function delete($id = null)
    {
        $this->request->allowMethod(['post', 'delete']);
        $reservedPlace = $this->ReservedPlaces->get($id);
        if ($this->ReservedPlaces->delete($reservedPlace)) {
            $this->Flash->success(__('The reserved place has been deleted.'));
        } else {
            $this->Flash->error(__('The reserved place could not be deleted. Please, try again.'));
        }

        return $this->redirect(['action' => 'index']);
    }
}
