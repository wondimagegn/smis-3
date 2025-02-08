<?php
namespace App\Controller;

use App\Controller\AppController;

class GraduationLettersController extends AppController
{

    public function index()
    {
        $this->paginate = [
            'contain' => ['Programs', 'ProgramTypes'],
        ];
        $graduationLetters = $this->paginate($this->GraduationLetters);

        $this->set(compact('graduationLetters'));
    }

    public function view($id = null)
    {
        $graduationLetter = $this->GraduationLetters->get($id, [
            'contain' => ['Programs', 'ProgramTypes'],
        ]);

        $this->set('graduationLetter', $graduationLetter);
    }


    public function add()
    {
        $graduationLetter = $this->GraduationLetters->newEntity();
        if ($this->request->is('post')) {
            $graduationLetter = $this->GraduationLetters->patchEntity($graduationLetter, $this->request->getData());
            if ($this->GraduationLetters->save($graduationLetter)) {
                $this->Flash->success(__('The graduation letter has been saved.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The graduation letter could not be saved. Please, try again.'));
        }

        $this->set(compact('graduationLetter'));
    }


    public function edit($id = null)
    {
        $graduationLetter = $this->GraduationLetters->get($id, [
            'contain' => [],
        ]);
        if ($this->request->is(['patch', 'post', 'put'])) {
            $graduationLetter = $this->GraduationLetters->patchEntity($graduationLetter, $this->request->getData());
            if ($this->GraduationLetters->save($graduationLetter)) {
                $this->Flash->success(__('The graduation letter has been saved.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The graduation letter could not be saved. Please, try again.'));
        }
        $this->set(compact('graduationLetter'));
    }


    public function delete($id = null)
    {
        $this->request->allowMethod(['post', 'delete']);
        $graduationLetter = $this->GraduationLetters->get($id);
        if ($this->GraduationLetters->delete($graduationLetter)) {
            $this->Flash->success(__('The graduation letter has been deleted.'));
        } else {
            $this->Flash->error(__('The graduation letter could not be deleted. Please, try again.'));
        }

        return $this->redirect(['action' => 'index']);
    }
}
