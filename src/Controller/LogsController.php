<?php
namespace App\Controller;

use App\Controller\AppController;

class LogsController extends AppController
{

    public function index()
    {
        $this->paginate = [
            'contain' => ['Users'],
        ];
        $logs = $this->paginate($this->Logs);

        $this->set(compact('logs'));
    }

    public function view($id = null)
    {
        $log = $this->Logs->get($id, [
            'contain' => ['Users'],
        ]);

        $this->set('log', $log);
    }

    public function add()
    {
        $log = $this->Logs->newEntity();
        if ($this->request->is('post')) {
            $log = $this->Logs->patchEntity($log, $this->request->getData());
            if ($this->Logs->save($log)) {
                $this->Flash->success(__('The log has been saved.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The log could not be saved. Please, try again.'));
        }
        $this->set(compact('log'));
    }


    public function edit($id = null)
    {
        $log = $this->Logs->get($id, [
            'contain' => [],
        ]);
        if ($this->request->is(['patch', 'post', 'put'])) {
            $log = $this->Logs->patchEntity($log, $this->request->getData());
            if ($this->Logs->save($log)) {
                $this->Flash->success(__('The log has been saved.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The log could not be saved. Please, try again.'));
        }
        $this->set(compact('log'));
    }


    public function delete($id = null)
    {
        $this->request->allowMethod(['post', 'delete']);
        $log = $this->Logs->get($id);
        if ($this->Logs->delete($log)) {
            $this->Flash->success(__('The log has been deleted.'));
        } else {
            $this->Flash->error(__('The log could not be deleted. Please, try again.'));
        }

        return $this->redirect(['action' => 'index']);
    }
}
