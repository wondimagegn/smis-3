<?php
namespace App\Controller;

use App\Controller\AppController;

class StaffStudiesController extends AppController
{
    public function index()
    {
        $this->paginate = [
            'contain' => ['Staffs', 'Countries'],
        ];
        $staffStudies = $this->paginate($this->StaffStudies);

        $this->set(compact('staffStudies'));
    }

    public function view($id = null)
    {
        $staffStudy = $this->StaffStudies->get($id, [
            'contain' => ['Staffs', 'Countries'],
        ]);

        $this->set('staffStudy', $staffStudy);
    }

    public function add()
    {
        $staffStudy = $this->StaffStudies->newEntity();
        if ($this->request->is('post')) {
            $staffStudy = $this->StaffStudies->patchEntity($staffStudy, $this->request->getData());
            if ($this->StaffStudies->save($staffStudy)) {
                $this->Flash->success(__('The staff study has been saved.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The staff study could not be saved. Please, try again.'));
        }
        $this->set(compact('staffStudy'));
    }


    public function edit($id = null)
    {
        $staffStudy = $this->StaffStudies->get($id, [
            'contain' => [],
        ]);
        if ($this->request->is(['patch', 'post', 'put'])) {
            $staffStudy = $this->StaffStudies->patchEntity($staffStudy, $this->request->getData());
            if ($this->StaffStudies->save($staffStudy)) {
                $this->Flash->success(__('The staff study has been saved.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The staff study could not be saved. Please, try again.'));
        }
        $this->set(compact('staffStudy'));
    }


    public function delete($id = null)
    {
        $this->request->allowMethod(['post', 'delete']);
        $staffStudy = $this->StaffStudies->get($id);
        if ($this->StaffStudies->delete($staffStudy)) {
            $this->Flash->success(__('The staff study has been deleted.'));
        } else {
            $this->Flash->error(__('The staff study could not be deleted. Please, try again.'));
        }

        return $this->redirect(['action' => 'index']);
    }
}
