<?php
namespace App\Controller;

use App\Controller\AppController;


class GraduationStatusesController extends AppController
{

    public function index()
    {
        $this->paginate = [
            'contain' => ['Programs'],
        ];
        $graduationStatuses = $this->paginate($this->GraduationStatuses);

        $this->set(compact('graduationStatuses'));
    }


    public function view($id = null)
    {
        $graduationStatus = $this->GraduationStatuses->get($id, [
            'contain' => ['Programs'],
        ]);

        $this->set('graduationStatus', $graduationStatus);
    }

    public function add()
    {
        $graduationStatus = $this->GraduationStatuses->newEntity();
        if ($this->request->is('post')) {
            $graduationStatus = $this->GraduationStatuses->patchEntity($graduationStatus, $this->request->getData());
            if ($this->GraduationStatuses->save($graduationStatus)) {
                $this->Flash->success(__('The graduation status has been saved.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The graduation status could not be saved. Please, try again.'));
        }
        $this->set(compact('graduationStatus'));
    }


    public function edit($id = null)
    {
        $graduationStatus = $this->GraduationStatuses->get($id, [
            'contain' => [],
        ]);
        if ($this->request->is(['patch', 'post', 'put'])) {
            $graduationStatus = $this->GraduationStatuses->patchEntity($graduationStatus, $this->request->getData());
            if ($this->GraduationStatuses->save($graduationStatus)) {
                $this->Flash->success(__('The graduation status has been saved.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The graduation status could not be saved. Please, try again.'));
        }
        $this->set(compact('graduationStatus'));
    }


    public function delete($id = null)
    {
        $this->request->allowMethod(['post', 'delete']);
        $graduationStatus = $this->GraduationStatuses->get($id);
        if ($this->GraduationStatuses->delete($graduationStatus)) {
            $this->Flash->success(__('The graduation status has been deleted.'));
        } else {
            $this->Flash->error(__('The graduation status could not be deleted. Please, try again.'));
        }

        return $this->redirect(['action' => 'index']);
    }
}
