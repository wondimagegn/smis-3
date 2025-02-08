<?php
namespace App\Controller;

use App\Controller\AppController;

class AttachmentsController extends AppController
{

    public function index()
    {
        $attachments = $this->paginate($this->Attachments);

        $this->set(compact('attachments'));
    }


    public function view($id = null)
    {
        $attachment = $this->Attachments->get($id, [
            'contain' => [],
        ]);

        $this->set('attachment', $attachment);
    }

    public function add()
    {
        $attachment = $this->Attachments->newEntity();
        if ($this->request->is('post')) {
            $attachment = $this->Attachments->patchEntity($attachment, $this->request->getData());
            if ($this->Attachments->save($attachment)) {
                $this->Flash->success(__('The attachment has been saved.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The attachment could not be saved. Please, try again.'));
        }
        $this->set(compact('attachment'));
    }

    public function edit($id = null)
    {
        $attachment = $this->Attachments->get($id, [
            'contain' => [],
        ]);
        if ($this->request->is(['patch', 'post', 'put'])) {
            $attachment = $this->Attachments->patchEntity($attachment, $this->request->getData());
            if ($this->Attachments->save($attachment)) {
                $this->Flash->success(__('The attachment has been saved.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The attachment could not be saved. Please, try again.'));
        }
        $this->set(compact('attachment'));
    }

    public function delete($id = null)
    {
        $this->request->allowMethod(['post', 'delete']);
        $attachment = $this->Attachments->get($id);
        if ($this->Attachments->delete($attachment)) {
            $this->Flash->success(__('The attachment has been deleted.'));
        } else {
            $this->Flash->error(__('The attachment could not be deleted. Please, try again.'));
        }

        return $this->redirect(['action' => 'index']);
    }
}
