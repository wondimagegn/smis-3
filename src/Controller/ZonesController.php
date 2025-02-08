<?php
namespace App\Controller;

use App\Controller\AppController;

class ZonesController extends AppController
{
    public function index()
    {
        $this->paginate = [
            'contain' => ['Regions'],
        ];
        $zones = $this->paginate($this->Zones);

        $this->set(compact('zones'));
    }

    public function view($id = null)
    {
        $zone = $this->Zones->get($id, [
            'contain' => ['Regions', 'AcceptedStudents', 'Cities', 'Contacts', 'Staffs', 'Students', 'Woredas'],
        ]);

        $this->set('zone', $zone);
    }

    public function add()
    {
        $zone = $this->Zones->newEntity();
        if ($this->request->is('post')) {
            $zone = $this->Zones->patchEntity($zone, $this->request->getData());
            if ($this->Zones->save($zone)) {
                $this->Flash->success(__('The zone has been saved.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The zone could not be saved. Please, try again.'));
        }
        $regions = $this->Zones->Regions->find('list', ['limit' => 200]);
        $this->set(compact('zone', 'regions'));
    }

    public function edit($id = null)
    {
        $zone = $this->Zones->get($id, [
            'contain' => [],
        ]);
        if ($this->request->is(['patch', 'post', 'put'])) {
            $zone = $this->Zones->patchEntity($zone, $this->request->getData());
            if ($this->Zones->save($zone)) {
                $this->Flash->success(__('The zone has been saved.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The zone could not be saved. Please, try again.'));
        }
        $regions = $this->Zones->Regions->find('list', ['limit' => 200]);
        $this->set(compact('zone', 'regions'));
    }

    public function delete($id = null)
    {
        $this->request->allowMethod(['post', 'delete']);
        $zone = $this->Zones->get($id);
        if ($this->Zones->delete($zone)) {
            $this->Flash->success(__('The zone has been deleted.'));
        } else {
            $this->Flash->error(__('The zone could not be deleted. Please, try again.'));
        }

        return $this->redirect(['action' => 'index']);
    }
}
