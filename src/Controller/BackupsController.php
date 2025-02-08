<?php
namespace App\Controller;

use App\Controller\AppController;

class BackupsController extends AppController
{

    public function index()
    {
        $backups = $this->paginate($this->Backups);

        $this->set(compact('backups'));
    }


    public function view($id = null)
    {
        $backup = $this->Backups->get($id, [
            'contain' => [],
        ]);

        $this->set('backup', $backup);
    }

    public function add()
    {
        $backup = $this->Backups->newEntity();
        if ($this->request->is('post')) {
            $backup = $this->Backups->patchEntity($backup, $this->request->getData());
            if ($this->Backups->save($backup)) {
                $this->Flash->success(__('The backup has been saved.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The backup could not be saved. Please, try again.'));
        }
        $this->set(compact('backup'));
    }

    public function edit($id = null)
    {
        $backup = $this->Backups->get($id, [
            'contain' => [],
        ]);
        if ($this->request->is(['patch', 'post', 'put'])) {
            $backup = $this->Backups->patchEntity($backup, $this->request->getData());
            if ($this->Backups->save($backup)) {
                $this->Flash->success(__('The backup has been saved.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The backup could not be saved. Please, try again.'));
        }
        $this->set(compact('backup'));
    }

    public function delete($id = null)
    {
        $this->request->allowMethod(['post', 'delete']);
        $backup = $this->Backups->get($id);
        if ($this->Backups->delete($backup)) {
            $this->Flash->success(__('The backup has been deleted.'));
        } else {
            $this->Flash->error(__('The backup could not be deleted. Please, try again.'));
        }

        return $this->redirect(['action' => 'index']);
    }
}
