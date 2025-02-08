<?php
namespace App\Controller;

use App\Controller\AppController;

class CitiesController extends AppController
{

    public function index()
    {
        $this->paginate = [
            'contain' => ['Regions', 'Zones'],
        ];
        $cities = $this->paginate($this->Cities);

        $this->set(compact('cities'));
    }


    public function view($id = null)
    {
        $city = $this->Cities->get($id, [
            'contain' => ['Regions', 'Zones', 'Contacts', 'Staffs', 'Students'],
        ]);

        $this->set('city', $city);
    }

    public function add()
    {
        $city = $this->Cities->newEntity();
        if ($this->request->is('post')) {
            $city = $this->Cities->patchEntity($city, $this->request->getData());
            if ($this->Cities->save($city)) {
                $this->Flash->success(__('The city has been saved.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The city could not be saved. Please, try again.'));
        }
        $regions = $this->Cities->Regions->find('list', ['limit' => 200]);
        $zones = $this->Cities->Zones->find('list', ['limit' => 200]);
        $this->set(compact('city', 'regions', 'zones'));
    }


    public function edit($id = null)
    {
        $city = $this->Cities->get($id, [
            'contain' => [],
        ]);
        if ($this->request->is(['patch', 'post', 'put'])) {
            $city = $this->Cities->patchEntity($city, $this->request->getData());
            if ($this->Cities->save($city)) {
                $this->Flash->success(__('The city has been saved.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The city could not be saved. Please, try again.'));
        }
        $regions = $this->Cities->Regions->find('list', ['limit' => 200]);
        $zones = $this->Cities->Zones->find('list', ['limit' => 200]);
        $this->set(compact('city', 'regions', 'zones'));
    }


    public function delete($id = null)
    {
        $this->request->allowMethod(['post', 'delete']);
        $city = $this->Cities->get($id);
        if ($this->Cities->delete($city)) {
            $this->Flash->success(__('The city has been deleted.'));
        } else {
            $this->Flash->error(__('The city could not be deleted. Please, try again.'));
        }

        return $this->redirect(['action' => 'index']);
    }
}
