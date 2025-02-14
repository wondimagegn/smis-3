<?php
namespace App\Controller;

use App\Controller\AppController;

class EslceResultsController extends AppController
{

    public function index()
    {
        $this->paginate = [
            'contain' => ['Students'],
        ];
        $eslceResults = $this->paginate($this->EslceResults);

        $this->set(compact('eslceResults'));
    }


    public function view($id = null)
    {
        $eslceResult = $this->EslceResults->get($id, [
            'contain' => ['Students'],
        ]);

        $this->set('eslceResult', $eslceResult);
    }


    public function add()
    {
        $eslceResult = $this->EslceResults->newEntity();
        if ($this->request->is('post')) {
            $eslceResult = $this->EslceResults->patchEntity($eslceResult, $this->request->getData());
            if ($this->EslceResults->save($eslceResult)) {
                $this->Flash->success(__('The eslce result has been saved.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The eslce result could not be saved. Please, try again.'));
        }

        $this->set(compact('eslceResult'));
    }


    public function edit($id = null)
    {
        $eslceResult = $this->EslceResults->get($id, [
            'contain' => [],
        ]);
        if ($this->request->is(['patch', 'post', 'put'])) {
            $eslceResult = $this->EslceResults->patchEntity($eslceResult, $this->request->getData());
            if ($this->EslceResults->save($eslceResult)) {
                $this->Flash->success(__('The eslce result has been saved.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The eslce result could not be saved. Please, try again.'));
        }
        $this->set(compact('eslceResult'));
    }


    public function delete($id = null)
    {
        $this->request->allowMethod(['post', 'delete']);
        $eslceResult = $this->EslceResults->get($id);
        if ($this->EslceResults->delete($eslceResult)) {
            $this->Flash->success(__('The eslce result has been deleted.'));
        } else {
            $this->Flash->error(__('The eslce result could not be deleted. Please, try again.'));
        }

        return $this->redirect(['action' => 'index']);
    }
}
