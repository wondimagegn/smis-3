<?php
namespace App\Controller;

use App\Controller\AppController;

class PasswordChanageVotesController extends AppController
{

    public function index()
    {
        $this->paginate = [
            'contain' => ['Users', 'Roles'],
        ];
        $passwordChanageVotes = $this->paginate($this->PasswordChanageVotes);

        $this->set(compact('passwordChanageVotes'));
    }


    public function view($id = null)
    {
        $passwordChanageVote = $this->PasswordChanageVotes->get($id, [
            'contain' => ['Users', 'Roles'],
        ]);

        $this->set('passwordChanageVote', $passwordChanageVote);
    }

    public function add()
    {
        $passwordChanageVote = $this->PasswordChanageVotes->newEntity();
        if ($this->request->is('post')) {
            $passwordChanageVote = $this->PasswordChanageVotes->patchEntity($passwordChanageVote, $this->request->getData());
            if ($this->PasswordChanageVotes->save($passwordChanageVote)) {
                $this->Flash->success(__('The password chanage vote has been saved.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The password chanage vote could not be saved. Please, try again.'));
        }
        $this->set(compact('passwordChanageVote'));
    }


    public function edit($id = null)
    {
        $passwordChanageVote = $this->PasswordChanageVotes->get($id, [
            'contain' => [],
        ]);
        if ($this->request->is(['patch', 'post', 'put'])) {
            $passwordChanageVote = $this->PasswordChanageVotes->patchEntity($passwordChanageVote, $this->request->getData());
            if ($this->PasswordChanageVotes->save($passwordChanageVote)) {
                $this->Flash->success(__('The password chanage vote has been saved.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The password chanage vote could not be saved. Please, try again.'));
        }
        $this->set(compact('passwordChanageVote'));
    }


    public function delete($id = null)
    {
        $this->request->allowMethod(['post', 'delete']);
        $passwordChanageVote = $this->PasswordChanageVotes->get($id);
        if ($this->PasswordChanageVotes->delete($passwordChanageVote)) {
            $this->Flash->success(__('The password chanage vote has been deleted.'));
        } else {
            $this->Flash->error(__('The password chanage vote could not be deleted. Please, try again.'));
        }

        return $this->redirect(['action' => 'index']);
    }
}
