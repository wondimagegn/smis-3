<?php
namespace App\Controller;

use App\Controller\AppController;

class AnnouncementsController extends AppController
{

    public function index()
    {
        $this->paginate = [
            'contain' => ['Users'],
        ];
        $announcements = $this->paginate($this->Announcements);

        $this->set(compact('announcements'));
    }


    public function view($id = null)
    {
        $announcement = $this->Announcements->get($id, [
            'contain' => ['Users'],
        ]);

        $this->set('announcement', $announcement);
    }

    public function add()
    {
        $announcement = $this->Announcements->newEntity();
        if ($this->request->is('post')) {
            $announcement = $this->Announcements->patchEntity($announcement, $this->request->getData());
            if ($this->Announcements->save($announcement)) {
                $this->Flash->success(__('The announcement has been saved.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The announcement could not be saved. Please, try again.'));
        }

    }

    public function edit($id = null)
    {
        $announcement = $this->Announcements->get($id, [
            'contain' => [],
        ]);
        if ($this->request->is(['patch', 'post', 'put'])) {
            $announcement = $this->Announcements->patchEntity($announcement, $this->request->getData());
            if ($this->Announcements->save($announcement)) {
                $this->Flash->success(__('The announcement has been saved.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The announcement could not be saved. Please, try again.'));
        }

    }

    public function delete($id = null)
    {
        $this->request->allowMethod(['post', 'delete']);
        $announcement = $this->Announcements->get($id);
        if ($this->Announcements->delete($announcement)) {
            $this->Flash->success(__('The announcement has been deleted.'));
        } else {
            $this->Flash->error(__('The announcement could not be deleted. Please, try again.'));
        }

        return $this->redirect(['action' => 'index']);
    }
}
