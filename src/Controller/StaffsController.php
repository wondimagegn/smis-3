<?php
namespace App\Controller;

use App\Controller\AppController;

class StaffsController extends AppController
{

    public function index()
    {
        $this->paginate = [
            'contain' => ['Colleges', 'Positions', 'Departments', 'Titles', 'Users', 'Countries', 'Regions',
                'Zones', 'Woredas', 'Cities', 'Educations'],
        ];
        $staffs = $this->paginate($this->Staffs);

        $this->set(compact('staffs'));
    }

    public function view($id = null)
    {
        $staff = $this->Staffs->get($id, [
            'contain' => ['Colleges', 'Positions', 'Departments', 'Titles', 'Users',
                'Countries', 'Regions', 'Zones', 'Woredas', 'Cities', 'Educations',
                'Courses', 'ColleagueEvalutionRates', 'Contacts', 'CourseInstructorAssignments',
                'InstructorClassPeriodCourseConstraints', 'InstructorExamExcludeDateConstraints',
                'InstructorNumberOfExamConstraints', 'Invigilators', 'Offices', 'StaffForExams', 'StaffStudies'],
        ]);

        $this->set('staff', $staff);
    }

    public function add()
    {
        $staff = $this->Staffs->newEntity();
        if ($this->request->is('post')) {
            $staff = $this->Staffs->patchEntity($staff, $this->request->getData());
            if ($this->Staffs->save($staff)) {
                $this->Flash->success(__('The staff has been saved.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The staff could not be saved. Please, try again.'));
        }

        $this->set(compact('staff'));
    }

    public function edit($id = null)
    {
        $staff = $this->Staffs->get($id, [
            'contain' => ['Courses'],
        ]);
        if ($this->request->is(['patch', 'post', 'put'])) {
            $staff = $this->Staffs->patchEntity($staff, $this->request->getData());
            if ($this->Staffs->save($staff)) {
                $this->Flash->success(__('The staff has been saved.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The staff could not be saved. Please, try again.'));
        }
        $this->set(compact('staff'));
    }


    public function delete($id = null)
    {
        $this->request->allowMethod(['post', 'delete']);
        $staff = $this->Staffs->get($id);
        if ($this->Staffs->delete($staff)) {
            $this->Flash->success(__('The staff has been deleted.'));
        } else {
            $this->Flash->error(__('The staff could not be deleted. Please, try again.'));
        }

        return $this->redirect(['action' => 'index']);
    }
}
