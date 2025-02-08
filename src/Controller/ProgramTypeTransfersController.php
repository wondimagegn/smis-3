<?php
namespace App\Controller;

use App\Controller\AppController;

class ProgramTypeTransfersController extends AppController
{

    public function index()
    {
        $this->paginate = [
            'contain' => ['Students', 'ProgramTypes'],
        ];
        $programTypeTransfers = $this->paginate($this->ProgramTypeTransfers);

        $this->set(compact('programTypeTransfers'));
    }

    public function view($id = null)
    {
        $programTypeTransfer = $this->ProgramTypeTransfers->get($id, [
            'contain' => ['Students', 'ProgramTypes'],
        ]);

        $this->set('programTypeTransfer', $programTypeTransfer);
    }

    public function add()
    {
        $programTypeTransfer = $this->ProgramTypeTransfers->newEntity();
        if ($this->request->is('post')) {
            $programTypeTransfer = $this->ProgramTypeTransfers->patchEntity($programTypeTransfer, $this->request->getData());
            if ($this->ProgramTypeTransfers->save($programTypeTransfer)) {
                $this->Flash->success(__('The program type transfer has been saved.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The program type transfer could not be saved. Please, try again.'));
        }

        $this->set(compact('programTypeTransfer'));
    }

    public function edit($id = null)
    {
        $programTypeTransfer = $this->ProgramTypeTransfers->get($id, [
            'contain' => [],
        ]);
        if ($this->request->is(['patch', 'post', 'put'])) {
            $programTypeTransfer = $this->ProgramTypeTransfers->patchEntity($programTypeTransfer, $this->request->getData());
            if ($this->ProgramTypeTransfers->save($programTypeTransfer)) {
                $this->Flash->success(__('The program type transfer has been saved.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The program type transfer could not be saved. Please, try again.'));
        }
        $this->set(compact('programTypeTransfer'));
    }

    public function delete($id = null)
    {
        $this->request->allowMethod(['post', 'delete']);
        $programTypeTransfer = $this->ProgramTypeTransfers->get($id);
        if ($this->ProgramTypeTransfers->delete($programTypeTransfer)) {
            $this->Flash->success(__('The program type transfer has been deleted.'));
        } else {
            $this->Flash->error(__('The program type transfer could not be deleted. Please, try again.'));
        }

        return $this->redirect(['action' => 'index']);
    }
}
