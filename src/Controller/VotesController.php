<?php
namespace App\Controller;

use App\Controller\AppController;

class VotesController extends AppController
{

    public function index()
    {
        $this->paginate = [
            'contain' => ['RequesterUsers', 'ApplicableOnUsers'],
        ];
        $votes = $this->paginate($this->Votes);

        $this->set(compact('votes'));
    }

    public function view($id = null)
    {
        $vote = $this->Votes->get($id, [
            'contain' => ['RequesterUsers', 'ApplicableOnUsers'],
        ]);

        $this->set('vote', $vote);
    }

    public function add()
    {
        $vote = $this->Votes->newEntity();
        if ($this->request->is('post')) {
            $vote = $this->Votes->patchEntity($vote, $this->request->getData());
            if ($this->Votes->save($vote)) {
                $this->Flash->success(__('The vote has been saved.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The vote could not be saved. Please, try again.'));
        }
        $this->set(compact('vote'));
    }


    public function edit($id = null)
    {
        $vote = $this->Votes->get($id, [
            'contain' => [],
        ]);
        if ($this->request->is(['patch', 'post', 'put'])) {
            $vote = $this->Votes->patchEntity($vote, $this->request->getData());
            if ($this->Votes->save($vote)) {
                $this->Flash->success(__('The vote has been saved.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The vote could not be saved. Please, try again.'));
        }
        $this->set(compact('vote'));
    }

    public function delete($id = null)
    {
        $this->request->allowMethod(['post', 'delete']);
        $vote = $this->Votes->get($id);
        if ($this->Votes->delete($vote)) {
            $this->Flash->success(__('The vote has been deleted.'));
        } else {
            $this->Flash->error(__('The vote could not be deleted. Please, try again.'));
        }

        return $this->redirect(['action' => 'index']);
    }
}
