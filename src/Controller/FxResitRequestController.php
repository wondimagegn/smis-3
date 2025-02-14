<?php
namespace App\Controller;

use App\Controller\AppController;

class FxResitRequestController extends AppController
{

    public function index()
    {
        $this->paginate = [
            'contain' => ['Students', 'CourseRegistrations', 'CourseAdds', 'PublishedCourses'],
        ];
        $fxResitRequest = $this->paginate($this->FxResitRequest);

        $this->set(compact('fxResitRequest'));
    }


    public function view($id = null)
    {
        $fxResitRequest = $this->FxResitRequest->get($id, [
            'contain' => ['Students', 'CourseRegistrations', 'CourseAdds', 'PublishedCourses'],
        ]);

        $this->set('fxResitRequest', $fxResitRequest);
    }

    public function add()
    {
        $fxResitRequest = $this->FxResitRequest->newEntity();
        if ($this->request->is('post')) {
            $fxResitRequest = $this->FxResitRequest->patchEntity($fxResitRequest, $this->request->getData());
            if ($this->FxResitRequest->save($fxResitRequest)) {
                $this->Flash->success(__('The fx resit request has been saved.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The fx resit request could not be saved. Please, try again.'));
        }

        $this->set(compact('fxResitRequest'));
    }

    public function edit($id = null)
    {
        $fxResitRequest = $this->FxResitRequest->get($id, [
            'contain' => [],
        ]);
        if ($this->request->is(['patch', 'post', 'put'])) {
            $fxResitRequest = $this->FxResitRequest->patchEntity($fxResitRequest, $this->request->getData());
            if ($this->FxResitRequest->save($fxResitRequest)) {
                $this->Flash->success(__('The fx resit request has been saved.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The fx resit request could not be saved. Please, try again.'));
        }

        $this->set(compact('fxResitRequest'));
    }

    public function delete($id = null)
    {
        $this->request->allowMethod(['post', 'delete']);
        $fxResitRequest = $this->FxResitRequest->get($id);
        if ($this->FxResitRequest->delete($fxResitRequest)) {
            $this->Flash->success(__('The fx resit request has been deleted.'));
        } else {
            $this->Flash->error(__('The fx resit request could not be deleted. Please, try again.'));
        }

        return $this->redirect(['action' => 'index']);
    }
}
