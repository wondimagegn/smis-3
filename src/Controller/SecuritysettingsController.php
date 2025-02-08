<?php
namespace App\Controller;

use App\Controller\AppController;

class SecuritysettingsController extends AppController
{

    public function index()
    {
        $securitysettings = $this->paginate($this->Securitysettings);

        $this->set(compact('securitysettings'));
    }

    public function view($id = null)
    {
        $securitysetting = $this->Securitysettings->get($id, [
            'contain' => [],
        ]);

        $this->set('securitysetting', $securitysetting);
    }

    public function add()
    {
        $securitysetting = $this->Securitysettings->newEntity();
        if ($this->request->is('post')) {
            $securitysetting = $this->Securitysettings->patchEntity($securitysetting, $this->request->getData());
            if ($this->Securitysettings->save($securitysetting)) {
                $this->Flash->success(__('The securitysetting has been saved.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The securitysetting could not be saved. Please, try again.'));
        }
        $this->set(compact('securitysetting'));
    }

    public function edit($id = null)
    {
        $securitysetting = $this->Securitysettings->get($id, [
            'contain' => [],
        ]);
        if ($this->request->is(['patch', 'post', 'put'])) {
            $securitysetting = $this->Securitysettings->patchEntity($securitysetting, $this->request->getData());
            if ($this->Securitysettings->save($securitysetting)) {
                $this->Flash->success(__('The securitysetting has been saved.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The securitysetting could not be saved. Please, try again.'));
        }
        $this->set(compact('securitysetting'));
    }

    public function delete($id = null)
    {
        $this->request->allowMethod(['post', 'delete']);
        $securitysetting = $this->Securitysettings->get($id);
        if ($this->Securitysettings->delete($securitysetting)) {
            $this->Flash->success(__('The securitysetting has been deleted.'));
        } else {
            $this->Flash->error(__('The securitysetting could not be deleted. Please, try again.'));
        }

        return $this->redirect(['action' => 'index']);
    }
}
