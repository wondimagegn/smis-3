<?php
namespace App\Controller;

use App\Controller\AppController;

class AlumniController extends AppController
{

    public function index()
    {
        $this->paginate = [
            'contain' => ['Students'],
        ];
        $alumni = $this->paginate($this->Alumni);

        $this->set(compact('alumni'));
    }

    public function view($id = null)
    {
        $alumnus = $this->Alumni->get($id, [
            'contain' => ['Students'],
        ]);

        $this->set('alumnus', $alumnus);
    }

    public function add()
    {
        $alumnus = $this->Alumni->newEntity();
        if ($this->request->is('post')) {
            $alumnus = $this->Alumni->patchEntity($alumnus, $this->request->getData());
            if ($this->Alumni->save($alumnus)) {
                $this->Flash->success(__('The alumnus has been saved.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The alumnus could not be saved. Please, try again.'));
        }

    }

    public function edit($id = null)
    {
        $alumnus = $this->Alumni->get($id, [
            'contain' => [],
        ]);
        if ($this->request->is(['patch', 'post', 'put'])) {
            $alumnus = $this->Alumni->patchEntity($alumnus, $this->request->getData());
            if ($this->Alumni->save($alumnus)) {
                $this->Flash->success(__('The alumnus has been saved.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The alumnus could not be saved. Please, try again.'));
        }

    }


    public function delete($id = null)
    {
        $this->request->allowMethod(['post', 'delete']);
        $alumnus = $this->Alumni->get($id);
        if ($this->Alumni->delete($alumnus)) {
            $this->Flash->success(__('The alumnus has been deleted.'));
        } else {
            $this->Flash->error(__('The alumnus could not be deleted. Please, try again.'));
        }

        return $this->redirect(['action' => 'index']);
    }
}
