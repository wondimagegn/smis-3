<?php
namespace App\Controller;

use App\Controller\AppController;

class ClassPeriodsController extends AppController
{

    public function index()
    {
        $this->paginate = [
            'contain' => ['PeriodSettings', 'Colleges', 'ProgramTypes', 'Programs'],
        ];
        $classPeriods = $this->paginate($this->ClassPeriods);

        $this->set(compact('classPeriods'));
    }


    public function view($id = null)
    {
        $classPeriod = $this->ClassPeriods->get($id, [
            'contain' => ['PeriodSettings', 'Colleges', 'ProgramTypes', 'Programs',
                'CourseSchedules', 'ClassPeriodCourseConstraints',
                'ClassRoomClassPeriodConstraints', 'InstructorClassPeriodCourseConstraints'],
        ]);

        $this->set('classPeriod', $classPeriod);
    }


    public function add()
    {
        $classPeriod = $this->ClassPeriods->newEntity();
        if ($this->request->is('post')) {
            $classPeriod = $this->ClassPeriods->patchEntity($classPeriod, $this->request->getData());
            if ($this->ClassPeriods->save($classPeriod)) {
                $this->Flash->success(__('The class period has been saved.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The class period could not be saved. Please, try again.'));
        }
        $periodSettings = $this->ClassPeriods->PeriodSettings->find('list', ['limit' => 200]);
        $colleges = $this->ClassPeriods->Colleges->find('list', ['limit' => 200]);
        $programTypes = $this->ClassPeriods->ProgramTypes->find('list', ['limit' => 200]);
        $programs = $this->ClassPeriods->Programs->find('list', ['limit' => 200]);
        $courseSchedules = $this->ClassPeriods->CourseSchedules->find('list', ['limit' => 200]);
        $this->set(compact('classPeriod', 'periodSettings', 'colleges', 'programTypes', 'programs', 'courseSchedules'));
    }

    public function edit($id = null)
    {
        $classPeriod = $this->ClassPeriods->get($id, [
            'contain' => ['CourseSchedules'],
        ]);
        if ($this->request->is(['patch', 'post', 'put'])) {
            $classPeriod = $this->ClassPeriods->patchEntity($classPeriod, $this->request->getData());
            if ($this->ClassPeriods->save($classPeriod)) {
                $this->Flash->success(__('The class period has been saved.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The class period could not be saved. Please, try again.'));
        }
        $periodSettings = $this->ClassPeriods->PeriodSettings->find('list', ['limit' => 200]);
        $colleges = $this->ClassPeriods->Colleges->find('list', ['limit' => 200]);
        $programTypes = $this->ClassPeriods->ProgramTypes->find('list', ['limit' => 200]);
        $programs = $this->ClassPeriods->Programs->find('list', ['limit' => 200]);
        $courseSchedules = $this->ClassPeriods->CourseSchedules->find('list', ['limit' => 200]);
        $this->set(compact('classPeriod', 'periodSettings', 'colleges', 'programTypes', 'programs', 'courseSchedules'));
    }

    public function delete($id = null)
    {
        $this->request->allowMethod(['post', 'delete']);
        $classPeriod = $this->ClassPeriods->get($id);
        if ($this->ClassPeriods->delete($classPeriod)) {
            $this->Flash->success(__('The class period has been deleted.'));
        } else {
            $this->Flash->error(__('The class period could not be deleted. Please, try again.'));
        }

        return $this->redirect(['action' => 'index']);
    }
}
