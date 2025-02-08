<?php
namespace App\Controller;

use App\Controller\AppController;

class WeblinksController extends AppController
{
    public function index()
    {
        $weblinks = $this->paginate($this->Weblinks);

        $this->set(compact('weblinks'));
    }

    public function view($id = null)
    {
        $weblink = $this->Weblinks->get($id, [
            'contain' => ['Courses'],
        ]);

        $this->set('weblink', $weblink);
    }

    public function add()
    {
        $weblink = $this->Weblinks->newEntity();
        if ($this->request->is('post')) {
            $weblink = $this->Weblinks->patchEntity($weblink, $this->request->getData());
            if ($this->Weblinks->save($weblink)) {
                $this->Flash->success(__('The weblink has been saved.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The weblink could not be saved. Please, try again.'));
        }
        $this->set(compact('weblink'));
    }


    public function edit($id = null)
    {
        $weblink = $this->Weblinks->get($id, [
            'contain' => ['Courses'],
        ]);
        if ($this->request->is(['patch', 'post', 'put'])) {
            $weblink = $this->Weblinks->patchEntity($weblink, $this->request->getData());
            if ($this->Weblinks->save($weblink)) {
                $this->Flash->success(__('The weblink has been saved.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The weblink could not be saved. Please, try again.'));
        }
        $this->set(compact('weblink'));
    }


    public function delete($id = null)
    {
        $this->request->allowMethod(['post', 'delete']);
        $weblink = $this->Weblinks->get($id);
        if ($this->Weblinks->delete($weblink)) {
            $this->Flash->success(__('The weblink has been deleted.'));
        } else {
            $this->Flash->error(__('The weblink could not be deleted. Please, try again.'));
        }

        return $this->redirect(['action' => 'index']);
    }
}
