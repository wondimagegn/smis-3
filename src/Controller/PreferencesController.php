<?php
namespace App\Controller;

use App\Controller\AppController;

class PreferencesController extends AppController
{

    public function index()
    {
        $this->paginate = [
            'contain' => ['AcceptedStudents', 'Colleges', 'Departments', 'Users'],
        ];
        $preferences = $this->paginate($this->Preferences);

        $this->set(compact('preferences'));
    }


    public function view($id = null)
    {
        $preference = $this->Preferences->get($id, [
            'contain' => ['AcceptedStudents', 'Colleges', 'Departments', 'Users'],
        ]);

        $this->set('preference', $preference);
    }

    public function add()
    {
        $preference = $this->Preferences->newEntity();
        if ($this->request->is('post')) {
            $preference = $this->Preferences->patchEntity($preference, $this->request->getData());
            if ($this->Preferences->save($preference)) {
                $this->Flash->success(__('The preference has been saved.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The preference could not be saved. Please, try again.'));
        }

        $this->set(compact('preference'));
    }


    public function edit($id = null)
    {
        $preference = $this->Preferences->get($id, [
            'contain' => [],
        ]);
        if ($this->request->is(['patch', 'post', 'put'])) {
            $preference = $this->Preferences->patchEntity($preference, $this->request->getData());
            if ($this->Preferences->save($preference)) {
                $this->Flash->success(__('The preference has been saved.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The preference could not be saved. Please, try again.'));
        }
        $acceptedStudents = $this->Preferences->AcceptedStudents->find('list', ['limit' => 200]);
        $colleges = $this->Preferences->Colleges->find('list', ['limit' => 200]);
        $departments = $this->Preferences->Departments->find('list', ['limit' => 200]);
        $users = $this->Preferences->Users->find('list', ['limit' => 200]);
        $this->set(compact('preference'));
    }

    public function delete($id = null)
    {
        $this->request->allowMethod(['post', 'delete']);
        $preference = $this->Preferences->get($id);
        if ($this->Preferences->delete($preference)) {
            $this->Flash->success(__('The preference has been deleted.'));
        } else {
            $this->Flash->error(__('The preference could not be deleted. Please, try again.'));
        }

        return $this->redirect(['action' => 'index']);
    }
}
