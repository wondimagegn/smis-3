<?php
namespace App\Controller;

use App\Controller\AppController;

class RegionsController extends AppController
{

    public function index()
    {
        $this->paginate = [
            'contain' => ['Countries'],
        ];
        $regions = $this->paginate($this->Regions);

        $this->set(compact('regions'));
    }

    public function view($id = null)
    {
        $region = $this->Regions->get($id, [
            'contain' => ['Countries', 'AcceptedStudents', 'Cities', 'Contacts', 'HighSchoolEducationBackgrounds', 'Staffs', 'Students', 'Zones'],
        ]);

        $this->set('region', $region);
    }

    public function add()
    {
        $region = $this->Regions->newEntity();
        if ($this->request->is('post')) {
            $region = $this->Regions->patchEntity($region, $this->request->getData());
            if ($this->Regions->save($region)) {
                $this->Flash->success(__('The region has been saved.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The region could not be saved. Please, try again.'));
        }
        $countries = $this->Regions->Countries->find('list', ['limit' => 200]);
        $this->set(compact('region', 'countries'));
    }

    public function edit($id = null)
    {
        $region = $this->Regions->get($id, [
            'contain' => [],
        ]);
        if ($this->request->is(['patch', 'post', 'put'])) {
            $region = $this->Regions->patchEntity($region, $this->request->getData());
            if ($this->Regions->save($region)) {
                $this->Flash->success(__('The region has been saved.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The region could not be saved. Please, try again.'));
        }
        $countries = $this->Regions->Countries->find('list', ['limit' => 200]);
        $this->set(compact('region', 'countries'));
    }

    public function delete($id = null)
    {
        $this->request->allowMethod(['post', 'delete']);
        $region = $this->Regions->get($id);
        if ($this->Regions->delete($region)) {
            $this->Flash->success(__('The region has been deleted.'));
        } else {
            $this->Flash->error(__('The region could not be deleted. Please, try again.'));
        }

        return $this->redirect(['action' => 'index']);
    }
}
