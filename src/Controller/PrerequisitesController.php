<?php
namespace App\Controller;

use App\Controller\AppController;

class PrerequisitesController extends AppController
{

    public function index()
    {
        $this->paginate = [
            'contain' => ['Courses', 'PrerequisiteCourses'],
        ];
        $prerequisites = $this->paginate($this->Prerequisites);

        $this->set(compact('prerequisites'));
    }

    public function view($id = null)
    {
        $prerequisite = $this->Prerequisites->get($id, [
            'contain' => ['Courses', 'PrerequisiteCourses'],
        ]);

        $this->set('prerequisite', $prerequisite);
    }

    public function add()
    {
        $prerequisite = $this->Prerequisites->newEntity();
        if ($this->request->is('post')) {
            $prerequisite = $this->Prerequisites->patchEntity($prerequisite, $this->request->getData());
            if ($this->Prerequisites->save($prerequisite)) {
                $this->Flash->success(__('The prerequisite has been saved.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The prerequisite could not be saved. Please, try again.'));
        }
        $this->set(compact('prerequisite'));
    }


    public function edit($id = null)
    {
        $prerequisite = $this->Prerequisites->get($id, [
            'contain' => [],
        ]);
        if ($this->request->is(['patch', 'post', 'put'])) {
            $prerequisite = $this->Prerequisites->patchEntity($prerequisite, $this->request->getData());
            if ($this->Prerequisites->save($prerequisite)) {
                $this->Flash->success(__('The prerequisite has been saved.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The prerequisite could not be saved. Please, try again.'));
        }
        $courses = $this->Prerequisites->Courses->find('list', ['limit' => 200]);
        $prerequisiteCourses = $this->Prerequisites->PrerequisiteCourses->find('list', ['limit' => 200]);
        $this->set(compact('prerequisite', 'courses', 'prerequisiteCourses'));
    }

    public function delete($id = null)
    {
        $this->request->allowMethod(['post', 'delete']);
        $prerequisite = $this->Prerequisites->get($id);
        if ($this->Prerequisites->delete($prerequisite)) {
            $this->Flash->success(__('The prerequisite has been deleted.'));
        } else {
            $this->Flash->error(__('The prerequisite could not be deleted. Please, try again.'));
        }

        return $this->redirect(['action' => 'index']);
    }
}
