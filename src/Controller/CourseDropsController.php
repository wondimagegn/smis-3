<?php
namespace App\Controller;

use App\Controller\AppController;

class CourseDropsController extends AppController
{

    public function index()
    {
        $this->paginate = [
            'contain' => ['YearLevels', 'Students', 'CourseRegistrations'],
        ];
        $courseDrops = $this->paginate($this->CourseDrops);

        $this->set(compact('courseDrops'));
    }

    public function view($id = null)
    {
        $courseDrop = $this->CourseDrops->get($id, [
            'contain' => ['YearLevels', 'Students', 'CourseRegistrations'],
        ]);

        $this->set('courseDrop', $courseDrop);
    }

    public function add()
    {
        $courseDrop = $this->CourseDrops->newEntity();
        if ($this->request->is('post')) {
            $courseDrop = $this->CourseDrops->patchEntity($courseDrop, $this->request->getData());
            if ($this->CourseDrops->save($courseDrop)) {
                $this->Flash->success(__('The course drop has been saved.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The course drop could not be saved. Please, try again.'));
        }

        $this->set(compact('courseDrop'));
    }


    public function edit($id = null)
    {
        $courseDrop = $this->CourseDrops->get($id, [
            'contain' => [],
        ]);
        if ($this->request->is(['patch', 'post', 'put'])) {
            $courseDrop = $this->CourseDrops->patchEntity($courseDrop, $this->request->getData());
            if ($this->CourseDrops->save($courseDrop)) {
                $this->Flash->success(__('The course drop has been saved.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The course drop could not be saved. Please, try again.'));
        }

        $this->set(compact('courseDrop'));
    }

    public function delete($id = null)
    {
        $this->request->allowMethod(['post', 'delete']);
        $courseDrop = $this->CourseDrops->get($id);
        if ($this->CourseDrops->delete($courseDrop)) {
            $this->Flash->success(__('The course drop has been deleted.'));
        } else {
            $this->Flash->error(__('The course drop could not be deleted. Please, try again.'));
        }

        return $this->redirect(['action' => 'index']);
    }
}
