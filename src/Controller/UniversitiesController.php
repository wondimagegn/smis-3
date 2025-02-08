<?php
namespace App\Controller;

use App\Controller\AppController;

class UniversitiesController extends AppController
{

    public function index()
    {
        $universities = $this->paginate($this->Universities);

        $this->set(compact('universities'));
    }

    public function view($id = null)
    {
        $university = $this->Universities->get($id, [
            'contain' => [],
        ]);

        $this->set('university', $university);
    }

    public function add()
    {
        $university = $this->Universities->newEntity();
        if ($this->request->is('post')) {
            $university = $this->Universities->patchEntity($university, $this->request->getData());
            if ($this->Universities->save($university)) {
                $this->Flash->success(__('The university has been saved.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The university could not be saved. Please, try again.'));
        }
        $this->set(compact('university'));
    }

    public function edit($id = null)
    {
        $university = $this->Universities->get($id, [
            'contain' => [],
        ]);
        if ($this->request->is(['patch', 'post', 'put'])) {
            $university = $this->Universities->patchEntity($university, $this->request->getData());
            if ($this->Universities->save($university)) {
                $this->Flash->success(__('The university has been saved.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The university could not be saved. Please, try again.'));
        }
        $this->set(compact('university'));
    }


    public function delete($id = null)
    {
        $this->request->allowMethod(['post', 'delete']);
        $university = $this->Universities->get($id);
        if ($this->Universities->delete($university)) {
            $this->Flash->success(__('The university has been deleted.'));
        } else {
            $this->Flash->error(__('The university could not be deleted. Please, try again.'));
        }

        return $this->redirect(['action' => 'index']);
    }
}
