<?php
namespace App\Controller;

use App\Controller\AppController;

class PositionsController extends AppController
{

    public function index()
    {
        $this->paginate = [
            'contain' => [],
        ];
        $positions = $this->paginate($this->Positions);

        $this->set(compact('positions'));
    }

    public function view($id = null)
    {
        $position = $this->Positions->get($id, [
            'contain' => ['ServiceWings', 'Staffs'],
        ]);

        $this->set('position', $position);
    }

    public function add()
    {
        $position = $this->Positions->newEntity();
        if ($this->request->is('post')) {
            $position = $this->Positions->patchEntity($position, $this->request->getData());
            if ($this->Positions->save($position)) {
                $this->Flash->success(__('The position has been saved.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The position could not be saved. Please, try again.'));
        }

        $this->set(compact('position', 'serviceWings'));
    }

    public function edit($id = null)
    {
        $position = $this->Positions->get($id, [
            'contain' => [],
        ]);
        if ($this->request->is(['patch', 'post', 'put'])) {
            $position = $this->Positions->patchEntity($position, $this->request->getData());
            if ($this->Positions->save($position)) {
                $this->Flash->success(__('The position has been saved.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The position could not be saved. Please, try again.'));
        }
        $this->set(compact('position'));
    }


    public function delete($id = null)
    {
        $this->request->allowMethod(['post', 'delete']);
        $position = $this->Positions->get($id);
        if ($this->Positions->delete($position)) {
            $this->Flash->success(__('The position has been deleted.'));
        } else {
            $this->Flash->error(__('The position could not be deleted. Please, try again.'));
        }

        return $this->redirect(['action' => 'index']);
    }
}
