<?php
namespace App\Controller;

use App\Controller\AppController;

class TranscriptFootersController extends AppController
{

    public function index()
    {
        $this->paginate = [
            'contain' => ['Programs'],
        ];
        $transcriptFooters = $this->paginate($this->TranscriptFooters);

        $this->set(compact('transcriptFooters'));
    }

    public function view($id = null)
    {
        $transcriptFooter = $this->TranscriptFooters->get($id, [
            'contain' => ['Programs'],
        ]);

        $this->set('transcriptFooter', $transcriptFooter);
    }

    public function add()
    {
        $transcriptFooter = $this->TranscriptFooters->newEntity();
        if ($this->request->is('post')) {
            $transcriptFooter = $this->TranscriptFooters->patchEntity($transcriptFooter, $this->request->getData());
            if ($this->TranscriptFooters->save($transcriptFooter)) {
                $this->Flash->success(__('The transcript footer has been saved.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The transcript footer could not be saved. Please, try again.'));
        }
        $this->set(compact('transcriptFooter'));
    }


    public function edit($id = null)
    {
        $transcriptFooter = $this->TranscriptFooters->get($id, [
            'contain' => [],
        ]);
        if ($this->request->is(['patch', 'post', 'put'])) {
            $transcriptFooter = $this->TranscriptFooters->patchEntity($transcriptFooter, $this->request->getData());
            if ($this->TranscriptFooters->save($transcriptFooter)) {
                $this->Flash->success(__('The transcript footer has been saved.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The transcript footer could not be saved. Please, try again.'));
        }
        $this->set(compact('transcriptFooter'));
    }


    public function delete($id = null)
    {
        $this->request->allowMethod(['post', 'delete']);
        $transcriptFooter = $this->TranscriptFooters->get($id);
        if ($this->TranscriptFooters->delete($transcriptFooter)) {
            $this->Flash->success(__('The transcript footer has been deleted.'));
        } else {
            $this->Flash->error(__('The transcript footer could not be deleted. Please, try again.'));
        }

        return $this->redirect(['action' => 'index']);
    }
}
