<?php
namespace App\Controller;

use App\Controller\AppController;

/**
 * PeriodSettings Controller
 *
 * @property \App\Model\Table\PeriodSettingsTable $PeriodSettings
 *
 * @method \App\Model\Entity\PeriodSetting[]|\Cake\Datasource\ResultSetInterface paginate($object = null, array $settings = [])
 */
class PeriodSettingsController extends AppController
{

    public function index()
    {
        $this->paginate = [
            'contain' => ['Colleges'],
        ];
        $periodSettings = $this->paginate($this->PeriodSettings);

        $this->set(compact('periodSettings'));
    }

    public function view($id = null)
    {
        $periodSetting = $this->PeriodSettings->get($id, [
            'contain' => ['Colleges', 'ClassPeriods'],
        ]);

        $this->set('periodSetting', $periodSetting);
    }


    public function add()
    {
        $periodSetting = $this->PeriodSettings->newEntity();
        if ($this->request->is('post')) {
            $periodSetting = $this->PeriodSettings->patchEntity($periodSetting, $this->request->getData());
            if ($this->PeriodSettings->save($periodSetting)) {
                $this->Flash->success(__('The period setting has been saved.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The period setting could not be saved. Please, try again.'));
        }
        $colleges = $this->PeriodSettings->Colleges->find('list', ['limit' => 200]);
        $this->set(compact('periodSetting', 'colleges'));
    }

    public function edit($id = null)
    {
        $periodSetting = $this->PeriodSettings->get($id, [
            'contain' => [],
        ]);
        if ($this->request->is(['patch', 'post', 'put'])) {
            $periodSetting = $this->PeriodSettings->patchEntity($periodSetting, $this->request->getData());
            if ($this->PeriodSettings->save($periodSetting)) {
                $this->Flash->success(__('The period setting has been saved.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The period setting could not be saved. Please, try again.'));
        }
        $this->set(compact('periodSetting'));
    }


    public function delete($id = null)
    {
        $this->request->allowMethod(['post', 'delete']);
        $periodSetting = $this->PeriodSettings->get($id);
        if ($this->PeriodSettings->delete($periodSetting)) {
            $this->Flash->success(__('The period setting has been deleted.'));
        } else {
            $this->Flash->error(__('The period setting could not be deleted. Please, try again.'));
        }

        return $this->redirect(['action' => 'index']);
    }
}
