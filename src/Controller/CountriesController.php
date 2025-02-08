<?php
namespace App\Controller;

use App\Controller\AppController;

class CountriesController extends AppController
{

    public function index()
    {
        $countries = $this->paginate($this->Countries);

        $this->set(compact('countries'));
    }


    public function view($id = null)
    {
        $country = $this->Countries->get($id, [
            'contain' => ['Contacts', 'Regions', 'StaffStudies', 'Staffs', 'Students'],
        ]);

        $this->set('country', $country);
    }


    public function add()
    {
        $country = $this->Countries->newEntity();
        if ($this->request->is('post')) {
            $country = $this->Countries->patchEntity($country, $this->request->getData());
            if ($this->Countries->save($country)) {
                $this->Flash->success(__('The country has been saved.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The country could not be saved. Please, try again.'));
        }
        $this->set(compact('country'));
    }

    public function edit($id = null)
    {
        $country = $this->Countries->get($id, [
            'contain' => [],
        ]);
        if ($this->request->is(['patch', 'post', 'put'])) {
            $country = $this->Countries->patchEntity($country, $this->request->getData());
            if ($this->Countries->save($country)) {
                $this->Flash->success(__('The country has been saved.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The country could not be saved. Please, try again.'));
        }
        $this->set(compact('country'));
    }

    public function delete($id = null)
    {
        $this->request->allowMethod(['post', 'delete']);
        $country = $this->Countries->get($id);
        if ($this->Countries->delete($country)) {
            $this->Flash->success(__('The country has been deleted.'));
        } else {
            $this->Flash->error(__('The country could not be deleted. Please, try again.'));
        }

        return $this->redirect(['action' => 'index']);
    }
}
