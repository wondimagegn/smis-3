<?php
namespace App\Controller;

use App\Controller\AppController;

class ContactsController extends AppController
{

    public function index()
    {
        $this->paginate = [
            'contain' => ['Students', 'Staffs', 'Countries', 'Regions', 'Zones', 'Woredas', 'Cities'],
        ];
        $contacts = $this->paginate($this->Contacts);

        $this->set(compact('contacts'));
    }


    public function view($id = null)
    {
        $contact = $this->Contacts->get($id, [
            'contain' => ['Students', 'Staffs', 'Countries', 'Regions', 'Zones', 'Woredas', 'Cities'],
        ]);

        $this->set('contact', $contact);
    }


    public function add()
    {
        $contact = $this->Contacts->newEntity();
        if ($this->request->is('post')) {
            $contact = $this->Contacts->patchEntity($contact, $this->request->getData());
            if ($this->Contacts->save($contact)) {
                $this->Flash->success(__('The contact has been saved.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The contact could not be saved. Please, try again.'));
        }

        $this->set(compact('contact'));
    }


    public function edit($id = null)
    {
        $contact = $this->Contacts->get($id, [
            'contain' => [],
        ]);
        if ($this->request->is(['patch', 'post', 'put'])) {
            $contact = $this->Contacts->patchEntity($contact, $this->request->getData());
            if ($this->Contacts->save($contact)) {
                $this->Flash->success(__('The contact has been saved.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The contact could not be saved. Please, try again.'));
        }

        $this->set(compact('contact'));
    }


    public function delete($id = null)
    {
        $this->request->allowMethod(['post', 'delete']);
        $contact = $this->Contacts->get($id);
        if ($this->Contacts->delete($contact)) {
            $this->Flash->success(__('The contact has been deleted.'));
        } else {
            $this->Flash->error(__('The contact could not be deleted. Please, try again.'));
        }

        return $this->redirect(['action' => 'index']);
    }
}
