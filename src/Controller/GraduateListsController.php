<?php
namespace App\Controller;

use App\Controller\AppController;

class GraduateListsController extends AppController
{

    public function index()
    {
        $this->paginate = [
            'contain' => ['Students'],
        ];
        $graduateLists = $this->paginate($this->GraduateLists);

        $this->set(compact('graduateLists'));
    }


    public function view($id = null)
    {
        $graduateList = $this->GraduateLists->get($id, [
            'contain' => ['Students'],
        ]);

        $this->set('graduateList', $graduateList);
    }

    public function add()
    {
        $graduateList = $this->GraduateLists->newEntity();
        if ($this->request->is('post')) {
            $graduateList = $this->GraduateLists->patchEntity($graduateList, $this->request->getData());
            if ($this->GraduateLists->save($graduateList)) {
                $this->Flash->success(__('The graduate list has been saved.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The graduate list could not be saved. Please, try again.'));
        }
        $this->set(compact('graduateList'));
    }


    public function edit($id = null)
    {
        $graduateList = $this->GraduateLists->get($id, [
            'contain' => [],
        ]);
        if ($this->request->is(['patch', 'post', 'put'])) {
            $graduateList = $this->GraduateLists->patchEntity($graduateList, $this->request->getData());
            if ($this->GraduateLists->save($graduateList)) {
                $this->Flash->success(__('The graduate list has been saved.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The graduate list could not be saved. Please, try again.'));
        }
        $this->set(compact('graduateList'));
    }


    public function delete($id = null)
    {
        $this->request->allowMethod(['post', 'delete']);
        $graduateList = $this->GraduateLists->get($id);
        if ($this->GraduateLists->delete($graduateList)) {
            $this->Flash->success(__('The graduate list has been deleted.'));
        } else {
            $this->Flash->error(__('The graduate list could not be deleted. Please, try again.'));
        }

        return $this->redirect(['action' => 'index']);
    }
}
