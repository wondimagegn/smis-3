<?php

namespace App\Controller;

use App\Controller\AppController;
use Cake\Event\EventInterface;
use Cake\ORM\TableRegistry;
use Cake\Core\Configure;

class CourseSchedulesClassPeriodsController extends AppController
{
    public function index()
    {
        $this->paginate = [
            'contain' => ['CourseSchedules', 'ClassPeriods'],
        ];
        $courseSchedulesClassPeriods = $this->paginate($this->CourseSchedulesClassPeriods);

        $this->set(compact('courseSchedulesClassPeriods'));
    }


    public function view($id = null)
    {
        $courseSchedulesClassPeriod = $this->CourseSchedulesClassPeriods->get($id, [
            'contain' => ['CourseSchedules', 'ClassPeriods'],
        ]);

        $this->set('courseSchedulesClassPeriod', $courseSchedulesClassPeriod);
    }

    public function add()
    {
        $courseSchedulesClassPeriod = $this->CourseSchedulesClassPeriods->newEntity();
        if ($this->request->is('post')) {
            $courseSchedulesClassPeriod = $this->CourseSchedulesClassPeriods->patchEntity($courseSchedulesClassPeriod, $this->request->getData());
            if ($this->CourseSchedulesClassPeriods->save($courseSchedulesClassPeriod)) {
                $this->Flash->success(__('The course schedules class period has been saved.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The course schedules class period could not be saved. Please, try again.'));
        }
        $this->set(compact('courseSchedulesClassPeriod'));
    }


    public function edit($id = null)
    {
        $courseSchedulesClassPeriod = $this->CourseSchedulesClassPeriods->get($id, [
            'contain' => [],
        ]);
        if ($this->request->is(['patch', 'post', 'put'])) {
            $courseSchedulesClassPeriod = $this->CourseSchedulesClassPeriods->patchEntity($courseSchedulesClassPeriod, $this->request->getData());
            if ($this->CourseSchedulesClassPeriods->save($courseSchedulesClassPeriod)) {
                $this->Flash->success(__('The course schedules class period has been saved.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The course schedules class period could not be saved. Please, try again.'));
        }
         $this->set(compact('courseSchedulesClassPeriod'));
    }

    public function delete($id = null)
    {
        $this->request->allowMethod(['post', 'delete']);
        $courseSchedulesClassPeriod = $this->CourseSchedulesClassPeriods->get($id);
        if ($this->CourseSchedulesClassPeriods->delete($courseSchedulesClassPeriod)) {
            $this->Flash->success(__('The course schedules class period has been deleted.'));
        } else {
            $this->Flash->error(__('The course schedules class period could not be deleted. Please, try again.'));
        }

        return $this->redirect(['action' => 'index']);
    }
}
