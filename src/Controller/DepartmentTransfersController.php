<?php
namespace App\Controller;

use App\Controller\AppController;

class DepartmentTransfersController extends AppController
{

    public function index()
    {
        $this->paginate = [
            'contain' => ['Departments', 'FromDepartments', 'ToColleges', 'Students'],
        ];
        $departmentTransfers = $this->paginate($this->DepartmentTransfers);

        $this->set(compact('departmentTransfers'));
    }

    public function view($id = null)
    {
        $departmentTransfer = $this->DepartmentTransfers->get($id, [
            'contain' => ['Departments', 'FromDepartments', 'ToColleges', 'Students'],
        ]);

        $this->set('departmentTransfer', $departmentTransfer);
    }

    public function add()
    {
        $departmentTransfer = $this->DepartmentTransfers->newEntity();
        if ($this->request->is('post')) {
            $departmentTransfer = $this->DepartmentTransfers->patchEntity($departmentTransfer, $this->request->getData());
            if ($this->DepartmentTransfers->save($departmentTransfer)) {
                $this->Flash->success(__('The department transfer has been saved.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The department transfer could not be saved. Please, try again.'));
        }

        $this->set(compact('departmentTransfer'));
    }

    public function edit($id = null)
    {
        $departmentTransfer = $this->DepartmentTransfers->get($id, [
            'contain' => [],
        ]);
        if ($this->request->is(['patch', 'post', 'put'])) {
            $departmentTransfer = $this->DepartmentTransfers->patchEntity($departmentTransfer, $this->request->getData());
            if ($this->DepartmentTransfers->save($departmentTransfer)) {
                $this->Flash->success(__('The department transfer has been saved.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The department transfer could not be saved. Please, try again.'));
        }

        $this->set(compact('departmentTransfer'));
    }


    public function delete($id = null)
    {
        $this->request->allowMethod(['post', 'delete']);
        $departmentTransfer = $this->DepartmentTransfers->get($id);
        if ($this->DepartmentTransfers->delete($departmentTransfer)) {
            $this->Flash->success(__('The department transfer has been deleted.'));
        } else {
            $this->Flash->error(__('The department transfer could not be deleted. Please, try again.'));
        }

        return $this->redirect(['action' => 'index']);
    }
}
