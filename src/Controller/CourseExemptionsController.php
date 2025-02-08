<?php
namespace App\Controller;

use App\Controller\AppController;

class CourseExemptionsController extends AppController
{

    public function index()
    {
        $this->paginate = [
            'contain' => ['Courses', 'Students'],
        ];
        $courseExemptions = $this->paginate($this->CourseExemptions);

        $this->set(compact('courseExemptions'));
    }


    public function view($id = null)
    {
        $courseExemption = $this->CourseExemptions->get($id, [
            'contain' => ['Courses', 'Students', 'ExcludedCourseFromTranscripts'],
        ]);

        $this->set('courseExemption', $courseExemption);
    }

    public function add()
    {
        $courseExemption = $this->CourseExemptions->newEntity();
        if ($this->request->is('post')) {
            $courseExemption = $this->CourseExemptions->patchEntity($courseExemption, $this->request->getData());
            if ($this->CourseExemptions->save($courseExemption)) {
                $this->Flash->success(__('The course exemption has been saved.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The course exemption could not be saved. Please, try again.'));
        }

        $this->set(compact('courseExemption'));
    }


    public function edit($id = null)
    {
        $courseExemption = $this->CourseExemptions->get($id, [
            'contain' => [],
        ]);
        if ($this->request->is(['patch', 'post', 'put'])) {
            $courseExemption = $this->CourseExemptions->patchEntity($courseExemption, $this->request->getData());
            if ($this->CourseExemptions->save($courseExemption)) {
                $this->Flash->success(__('The course exemption has been saved.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The course exemption could not be saved. Please, try again.'));
        }

        $this->set(compact('courseExemption'));
    }


    public function delete($id = null)
    {
        $this->request->allowMethod(['post', 'delete']);
        $courseExemption = $this->CourseExemptions->get($id);
        if ($this->CourseExemptions->delete($courseExemption)) {
            $this->Flash->success(__('The course exemption has been deleted.'));
        } else {
            $this->Flash->error(__('The course exemption could not be deleted. Please, try again.'));
        }

        return $this->redirect(['action' => 'index']);
    }
}
