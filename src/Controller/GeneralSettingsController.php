<?php
namespace App\Controller;

use App\Controller\AppController;

class GeneralSettingsController extends AppController
{

    public function index()
    {
        $this->paginate = [
            'contain' => ['Programs', 'ProgramTypes'],
        ];
        $generalSettings = $this->paginate($this->GeneralSettings);

        $this->set(compact('generalSettings'));
    }

    public function view($id = null)
    {
        $generalSetting = $this->GeneralSettings->get($id, [
            'contain' => ['Programs', 'ProgramTypes'],
        ]);

        $this->set('generalSetting', $generalSetting);
    }

    public function add()
    {
        $generalSetting = $this->GeneralSettings->newEntity();
        if ($this->request->is('post')) {
            $generalSetting = $this->GeneralSettings->patchEntity($generalSetting, $this->request->getData());
            if ($this->GeneralSettings->save($generalSetting)) {
                $this->Flash->success(__('The general setting has been saved.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The general setting could not be saved. Please, try again.'));
        }
        $this->set(compact('generalSetting'));
    }


    public function edit($id = null)
    {
        $generalSetting = $this->GeneralSettings->get($id, [
            'contain' => [],
        ]);
        if ($this->request->is(['patch', 'post', 'put'])) {
            $generalSetting = $this->GeneralSettings->patchEntity($generalSetting, $this->request->getData());
            if ($this->GeneralSettings->save($generalSetting)) {
                $this->Flash->success(__('The general setting has been saved.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The general setting could not be saved. Please, try again.'));
        }
        $this->set(compact('generalSetting'));
    }


    public function delete($id = null)
    {
        $this->request->allowMethod(['post', 'delete']);
        $generalSetting = $this->GeneralSettings->get($id);
        if ($this->GeneralSettings->delete($generalSetting)) {
            $this->Flash->success(__('The general setting has been deleted.'));
        } else {
            $this->Flash->error(__('The general setting could not be deleted. Please, try again.'));
        }

        return $this->redirect(['action' => 'index']);
    }
}
