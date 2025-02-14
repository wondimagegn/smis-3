<?php
namespace App\Controller;

use App\Controller\AppController;

class DisabilitiesController extends AppController
{

    public function index()
    {
        $disabilities = $this->paginate($this->Disabilities);

        $this->set(compact('disabilities'));
    }

    public function view($id = null)
    {
        $disability = $this->Disabilities->get($id, [
            'contain' => ['AcceptedStudents'],
        ]);

        $this->set('disability', $disability);
    }

    public function add()
    {
        $disability = $this->Disabilities->newEntity();
        if ($this->request->is('post')) {
            $disability = $this->Disabilities->patchEntity($disability, $this->request->getData());
            if ($this->Disabilities->save($disability)) {
                $this->Flash->success(__('The disability has been saved.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The disability could not be saved. Please, try again.'));
        }
        $this->set(compact('disability'));
    }

    public function edit($id = null)
    {
        $disability = $this->Disabilities->get($id, [
            'contain' => [],
        ]);
        if ($this->request->is(['patch', 'post', 'put'])) {
            $disability = $this->Disabilities->patchEntity($disability, $this->request->getData());
            if ($this->Disabilities->save($disability)) {
                $this->Flash->success(__('The disability has been saved.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The disability could not be saved. Please, try again.'));
        }
        $this->set(compact('disability'));
    }


    public function delete($id = null)
    {
        $this->request->allowMethod(['post', 'delete']);
        $disability = $this->Disabilities->get($id);
        if ($this->Disabilities->delete($disability)) {
            $this->Flash->success(__('The disability has been deleted.'));
        } else {
            $this->Flash->error(__('The disability could not be deleted. Please, try again.'));
        }

        return $this->redirect(['action' => 'index']);
    }
}
