<?php
namespace App\Controller;

use App\Controller\AppController;

class GraduationWorksController extends AppController
{

    public function index()
    {
        $this->paginate = [
            'contain' => ['Students', 'Courses'],
        ];
        $graduationWorks = $this->paginate($this->GraduationWorks);

        $this->set(compact('graduationWorks'));
    }


    public function view($id = null)
    {
        $graduationWork = $this->GraduationWorks->get($id, [
            'contain' => ['Students', 'Courses'],
        ]);

        $this->set('graduationWork', $graduationWork);
    }


    public function add()
    {
        $graduationWork = $this->GraduationWorks->newEntity();
        if ($this->request->is('post')) {
            $graduationWork = $this->GraduationWorks->patchEntity($graduationWork, $this->request->getData());
            if ($this->GraduationWorks->save($graduationWork)) {
                $this->Flash->success(__('The graduation work has been saved.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The graduation work could not be saved. Please, try again.'));
        }
        $this->set(compact('graduationWork'));
    }


    public function edit($id = null)
    {
        $graduationWork = $this->GraduationWorks->get($id, [
            'contain' => [],
        ]);
        if ($this->request->is(['patch', 'post', 'put'])) {
            $graduationWork = $this->GraduationWorks->patchEntity($graduationWork, $this->request->getData());
            if ($this->GraduationWorks->save($graduationWork)) {
                $this->Flash->success(__('The graduation work has been saved.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The graduation work could not be saved. Please, try again.'));
        }
        $this->set(compact('graduationWork'));
    }


    public function delete($id = null)
    {
        $this->request->allowMethod(['post', 'delete']);
        $graduationWork = $this->GraduationWorks->get($id);
        if ($this->GraduationWorks->delete($graduationWork)) {
            $this->Flash->success(__('The graduation work has been deleted.'));
        } else {
            $this->Flash->error(__('The graduation work could not be deleted. Please, try again.'));
        }

        return $this->redirect(['action' => 'index']);
    }
}
