<?php
namespace App\Controller;

use App\Controller\AppController;

class DisciplinesController extends AppController
{

    public function index()
    {
        $this->paginate = [
            'contain' => ['Students'],
        ];
        $disciplines = $this->paginate($this->Disciplines);

        $this->set(compact('disciplines'));
    }


    public function view($id = null)
    {
        $discipline = $this->Disciplines->get($id, [
            'contain' => ['Students'],
        ]);

        $this->set('discipline', $discipline);
    }

    public function add()
    {
        $discipline = $this->Disciplines->newEntity();
        if ($this->request->is('post')) {
            $discipline = $this->Disciplines->patchEntity($discipline, $this->request->getData());
            if ($this->Disciplines->save($discipline)) {
                $this->Flash->success(__('The discipline has been saved.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The discipline could not be saved. Please, try again.'));
        }

        $this->set(compact('discipline'));
    }


    public function edit($id = null)
    {
        $discipline = $this->Disciplines->get($id, [
            'contain' => [],
        ]);
        if ($this->request->is(['patch', 'post', 'put'])) {
            $discipline = $this->Disciplines->patchEntity($discipline, $this->request->getData());
            if ($this->Disciplines->save($discipline)) {
                $this->Flash->success(__('The discipline has been saved.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The discipline could not be saved. Please, try again.'));
        }

        $this->set(compact('discipline'));
    }


    public function delete($id = null)
    {
        $this->request->allowMethod(['post', 'delete']);
        $discipline = $this->Disciplines->get($id);
        if ($this->Disciplines->delete($discipline)) {
            $this->Flash->success(__('The discipline has been deleted.'));
        } else {
            $this->Flash->error(__('The discipline could not be deleted. Please, try again.'));
        }

        return $this->redirect(['action' => 'index']);
    }
}
