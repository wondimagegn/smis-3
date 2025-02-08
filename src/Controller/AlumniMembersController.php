<?php
namespace App\Controller;

use App\Controller\AppController;

class AlumniMembersController extends AppController
{

    public function index()
    {
        $alumniMembers = $this->paginate($this->AlumniMembers);

        $this->set(compact('alumniMembers'));
    }


    public function view($id = null)
    {
        $alumniMember = $this->AlumniMembers->get($id, [
            'contain' => [],
        ]);

        $this->set('alumniMember', $alumniMember);
    }


    public function add()
    {
        $alumniMember = $this->AlumniMembers->newEntity();
        if ($this->request->is('post')) {
            $alumniMember = $this->AlumniMembers->patchEntity($alumniMember, $this->request->getData());
            if ($this->AlumniMembers->save($alumniMember)) {
                $this->Flash->success(__('The alumni member has been saved.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The alumni member could not be saved. Please, try again.'));
        }
        $this->set(compact('alumniMember'));
    }

    public function edit($id = null)
    {
        $alumniMember = $this->AlumniMembers->get($id, [
            'contain' => [],
        ]);
        if ($this->request->is(['patch', 'post', 'put'])) {
            $alumniMember = $this->AlumniMembers->patchEntity($alumniMember, $this->request->getData());
            if ($this->AlumniMembers->save($alumniMember)) {
                $this->Flash->success(__('The alumni member has been saved.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The alumni member could not be saved. Please, try again.'));
        }
        $this->set(compact('alumniMember'));
    }

    public function delete($id = null)
    {
        $this->request->allowMethod(['post', 'delete']);
        $alumniMember = $this->AlumniMembers->get($id);
        if ($this->AlumniMembers->delete($alumniMember)) {
            $this->Flash->success(__('The alumni member has been deleted.'));
        } else {
            $this->Flash->error(__('The alumni member could not be deleted. Please, try again.'));
        }

        return $this->redirect(['action' => 'index']);
    }
}
