<?php
namespace App\Controller;

use App\Controller\AppController;

class JournalsController extends AppController
{

    public function index()
    {
        $journals = $this->paginate($this->Journals);

        $this->set(compact('journals'));
    }

    public function view($id = null)
    {
        $journal = $this->Journals->get($id, [
            'contain' => ['Courses'],
        ]);

        $this->set('journal', $journal);
    }

    public function add()
    {
        $journal = $this->Journals->newEntity();
        if ($this->request->is('post')) {
            $journal = $this->Journals->patchEntity($journal, $this->request->getData());
            if ($this->Journals->save($journal)) {
                $this->Flash->success(__('The journal has been saved.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The journal could not be saved. Please, try again.'));
        }
        $this->set(compact('journal'));
    }


    public function edit($id = null)
    {
        $journal = $this->Journals->get($id, [
            'contain' => ['Courses'],
        ]);
        if ($this->request->is(['patch', 'post', 'put'])) {
            $journal = $this->Journals->patchEntity($journal, $this->request->getData());
            if ($this->Journals->save($journal)) {
                $this->Flash->success(__('The journal has been saved.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The journal could not be saved. Please, try again.'));
        }

        $this->set(compact('journal'));
    }


    public function delete($id = null)
    {
        $this->request->allowMethod(['post', 'delete']);
        $journal = $this->Journals->get($id);
        if ($this->Journals->delete($journal)) {
            $this->Flash->success(__('The journal has been deleted.'));
        } else {
            $this->Flash->error(__('The journal could not be deleted. Please, try again.'));
        }

        return $this->redirect(['action' => 'index']);
    }
}
