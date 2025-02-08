<?php
namespace App\Controller;

use App\Controller\AppController;

class PlacementsResultsCriteriasController extends AppController
{

    public function index()
    {
        $this->paginate = [
            'contain' => ['Colleges'],
        ];
        $placementsResultsCriterias = $this->paginate($this->PlacementsResultsCriterias);

        $this->set(compact('placementsResultsCriterias'));
    }


    public function view($id = null)
    {
        $placementsResultsCriteria = $this->PlacementsResultsCriterias->get($id, [
            'contain' => ['Colleges', 'ReservedPlaces'],
        ]);

        $this->set('placementsResultsCriteria', $placementsResultsCriteria);
    }

    public function add()
    {
        $placementsResultsCriteria = $this->PlacementsResultsCriterias->newEntity();
        if ($this->request->is('post')) {
            $placementsResultsCriteria = $this->PlacementsResultsCriterias->patchEntity($placementsResultsCriteria, $this->request->getData());
            if ($this->PlacementsResultsCriterias->save($placementsResultsCriteria)) {
                $this->Flash->success(__('The placements results criteria has been saved.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The placements results criteria could not be saved. Please, try again.'));
        }
        $colleges = $this->PlacementsResultsCriterias->Colleges->find('list', ['limit' => 200]);
        $this->set(compact('placementsResultsCriteria', 'colleges'));
    }


    public function edit($id = null)
    {
        $placementsResultsCriteria = $this->PlacementsResultsCriterias->get($id, [
            'contain' => [],
        ]);
        if ($this->request->is(['patch', 'post', 'put'])) {
            $placementsResultsCriteria = $this->PlacementsResultsCriterias->patchEntity($placementsResultsCriteria, $this->request->getData());
            if ($this->PlacementsResultsCriterias->save($placementsResultsCriteria)) {
                $this->Flash->success(__('The placements results criteria has been saved.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The placements results criteria could not be saved. Please, try again.'));
        }
        $this->set(compact('placementsResultsCriteria', 'colleges'));
    }

    public function delete($id = null)
    {
        $this->request->allowMethod(['post', 'delete']);
        $placementsResultsCriteria = $this->PlacementsResultsCriterias->get($id);
        if ($this->PlacementsResultsCriterias->delete($placementsResultsCriteria)) {
            $this->Flash->success(__('The placements results criteria has been deleted.'));
        } else {
            $this->Flash->error(__('The placements results criteria could not be deleted. Please, try again.'));
        }

        return $this->redirect(['action' => 'index']);
    }
}
