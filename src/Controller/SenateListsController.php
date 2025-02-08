<?php
namespace App\Controller;

use App\Controller\AppController;

class SenateListsController extends AppController
{

    public function index()
    {
        $this->paginate = [
            'contain' => ['Students'],
        ];
        $senateLists = $this->paginate($this->SenateLists);

        $this->set(compact('senateLists'));
    }

    public function view($id = null)
    {
        $senateList = $this->SenateLists->get($id, [
            'contain' => ['Students'],
        ]);

        $this->set('senateList', $senateList);
    }

    public function add()
    {
        $senateList = $this->SenateLists->newEntity();
        if ($this->request->is('post')) {
            $senateList = $this->SenateLists->patchEntity($senateList, $this->request->getData());
            if ($this->SenateLists->save($senateList)) {
                $this->Flash->success(__('The senate list has been saved.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The senate list could not be saved. Please, try again.'));
        }
        $students = $this->SenateLists->Students->find('list', ['limit' => 200]);
        $this->set(compact('senateList', 'students'));
    }


    public function edit($id = null)
    {
        $senateList = $this->SenateLists->get($id, [
            'contain' => [],
        ]);
        if ($this->request->is(['patch', 'post', 'put'])) {
            $senateList = $this->SenateLists->patchEntity($senateList, $this->request->getData());
            if ($this->SenateLists->save($senateList)) {
                $this->Flash->success(__('The senate list has been saved.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The senate list could not be saved. Please, try again.'));
        }
        $this->set(compact('senateList'));
    }


    public function delete($id = null)
    {
        $this->request->allowMethod(['post', 'delete']);
        $senateList = $this->SenateLists->get($id);
        if ($this->SenateLists->delete($senateList)) {
            $this->Flash->success(__('The senate list has been deleted.'));
        } else {
            $this->Flash->error(__('The senate list could not be deleted. Please, try again.'));
        }

        return $this->redirect(['action' => 'index']);
    }
}
