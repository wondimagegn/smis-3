<?php
namespace App\Controller;

use App\Controller\AppController;

class InstructorEvalutionSettingsController extends AppController
{

    public function index()
    {
        $instructorEvalutionSettings = $this->paginate($this->InstructorEvalutionSettings);

        $this->set(compact('instructorEvalutionSettings'));
    }

    public function view($id = null)
    {
        $instructorEvalutionSetting = $this->InstructorEvalutionSettings->get($id, [
            'contain' => [],
        ]);

        $this->set('instructorEvalutionSetting', $instructorEvalutionSetting);
    }

    public function add()
    {
        $instructorEvalutionSetting = $this->InstructorEvalutionSettings->newEntity();
        if ($this->request->is('post')) {
            $instructorEvalutionSetting = $this->InstructorEvalutionSettings->patchEntity($instructorEvalutionSetting, $this->request->getData());
            if ($this->InstructorEvalutionSettings->save($instructorEvalutionSetting)) {
                $this->Flash->success(__('The instructor evalution setting has been saved.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The instructor evalution setting could not be saved. Please, try again.'));
        }
        $this->set(compact('instructorEvalutionSetting'));
    }


    public function edit($id = null)
    {
        $instructorEvalutionSetting = $this->InstructorEvalutionSettings->get($id, [
            'contain' => [],
        ]);
        if ($this->request->is(['patch', 'post', 'put'])) {
            $instructorEvalutionSetting = $this->InstructorEvalutionSettings->patchEntity($instructorEvalutionSetting, $this->request->getData());
            if ($this->InstructorEvalutionSettings->save($instructorEvalutionSetting)) {
                $this->Flash->success(__('The instructor evalution setting has been saved.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The instructor evalution setting could not be saved. Please, try again.'));
        }
        $this->set(compact('instructorEvalutionSetting'));
    }


    public function delete($id = null)
    {
        $this->request->allowMethod(['post', 'delete']);
        $instructorEvalutionSetting = $this->InstructorEvalutionSettings->get($id);
        if ($this->InstructorEvalutionSettings->delete($instructorEvalutionSetting)) {
            $this->Flash->success(__('The instructor evalution setting has been deleted.'));
        } else {
            $this->Flash->error(__('The instructor evalution setting could not be deleted. Please, try again.'));
        }

        return $this->redirect(['action' => 'index']);
    }
}
