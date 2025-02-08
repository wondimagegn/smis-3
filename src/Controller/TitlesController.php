<?php
namespace App\Controller;

use App\Controller\AppController;

class TitlesController extends AppController
{

    public function index()
    {
        $titles = $this->paginate($this->Titles);

        $this->set(compact('titles'));
    }

    public function view($id = null)
    {
        $title = $this->Titles->get($id, [
            'contain' => ['Staffs'],
        ]);

        $this->set('title', $title);
    }

    public function add()
    {
        $title = $this->Titles->newEntity();
        if ($this->request->is('post')) {
            $title = $this->Titles->patchEntity($title, $this->request->getData());
            if ($this->Titles->save($title)) {
                $this->Flash->success(__('The title has been saved.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The title could not be saved. Please, try again.'));
        }
        $this->set(compact('title'));
    }

    public function edit($id = null)
    {
        $title = $this->Titles->get($id, [
            'contain' => [],
        ]);
        if ($this->request->is(['patch', 'post', 'put'])) {
            $title = $this->Titles->patchEntity($title, $this->request->getData());
            if ($this->Titles->save($title)) {
                $this->Flash->success(__('The title has been saved.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The title could not be saved. Please, try again.'));
        }
        $this->set(compact('title'));
    }

  
    public function delete($id = null)
    {
        $this->request->allowMethod(['post', 'delete']);
        $title = $this->Titles->get($id);
        if ($this->Titles->delete($title)) {
            $this->Flash->success(__('The title has been deleted.'));
        } else {
            $this->Flash->error(__('The title could not be deleted. Please, try again.'));
        }

        return $this->redirect(['action' => 'index']);
    }
}
