<?php
namespace App\Controller;

use App\Controller\AppController;

class HigherEducationBackgroundsController extends AppController
{

    public function index()
    {
        $this->paginate = [
            'contain' => ['Students'],
        ];
        $higherEducationBackgrounds = $this->paginate($this->HigherEducationBackgrounds);

        $this->set(compact('higherEducationBackgrounds'));
    }


    public function view($id = null)
    {
        $higherEducationBackground = $this->HigherEducationBackgrounds->get($id, [
            'contain' => ['Students'],
        ]);

        $this->set('higherEducationBackground', $higherEducationBackground);
    }

    public function add()
    {
        $higherEducationBackground = $this->HigherEducationBackgrounds->newEntity();
        if ($this->request->is('post')) {
            $higherEducationBackground = $this->HigherEducationBackgrounds->patchEntity($higherEducationBackground, $this->request->getData());
            if ($this->HigherEducationBackgrounds->save($higherEducationBackground)) {
                $this->Flash->success(__('The higher education background has been saved.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The higher education background could not be saved. Please, try again.'));
        }
        $this->set(compact('higherEducationBackground'));
    }


    public function edit($id = null)
    {
        $higherEducationBackground = $this->HigherEducationBackgrounds->get($id, [
            'contain' => [],
        ]);
        if ($this->request->is(['patch', 'post', 'put'])) {
            $higherEducationBackground = $this->HigherEducationBackgrounds->patchEntity($higherEducationBackground, $this->request->getData());
            if ($this->HigherEducationBackgrounds->save($higherEducationBackground)) {
                $this->Flash->success(__('The higher education background has been saved.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The higher education background could not be saved. Please, try again.'));
        }
        $this->set(compact('higherEducationBackground'));
    }


    public function delete($id = null)
    {
        $this->request->allowMethod(['post', 'delete']);
        $higherEducationBackground = $this->HigherEducationBackgrounds->get($id);
        if ($this->HigherEducationBackgrounds->delete($higherEducationBackground)) {
            $this->Flash->success(__('The higher education background has been deleted.'));
        } else {
            $this->Flash->error(__('The higher education background could not be deleted. Please, try again.'));
        }

        return $this->redirect(['action' => 'index']);
    }
}
