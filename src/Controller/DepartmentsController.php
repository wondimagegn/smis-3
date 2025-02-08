<?php
namespace App\Controller;

use App\Controller\AppController;

class DepartmentsController extends AppController
{

    public function index()
    {
        $this->paginate = [
            'contain' => ['Colleges', 'MoodleCategories'],
        ];
        $departments = $this->paginate($this->Departments);

        $this->set(compact('departments'));
    }


    public function view($id = null)
    {
        $department = $this->Departments->get($id, [
            'contain' => ['Colleges', 'MoodleCategories', 'AcademicCalendars', 'AcceptedStudents', 'Courses', 'Curriculums', 'DepartmentStudyPrograms', 'DepartmentTransfers', 'ExtendingAcademicCalendars', 'Notes', 'Offers', 'OnlineApplicants', 'OtherAcademicRules', 'ParticipatingDepartments', 'Preferences', 'PublishedCourses', 'Sections', 'Specializations', 'StaffAssignes', 'Staffs', 'Students', 'TakenProperties', 'TypeCredits', 'YearLevels'],
        ]);

        $this->set('department', $department);
    }

    public function add()
    {
        $department = $this->Departments->newEntity();
        if ($this->request->is('post')) {
            $department = $this->Departments->patchEntity($department, $this->request->getData());
            if ($this->Departments->save($department)) {
                $this->Flash->success(__('The department has been saved.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The department could not be saved. Please, try again.'));
        }
        $this->set(compact('department'));
    }


    public function edit($id = null)
    {
        $department = $this->Departments->get($id, [
            'contain' => [],
        ]);
        if ($this->request->is(['patch', 'post', 'put'])) {
            $department = $this->Departments->patchEntity($department, $this->request->getData());
            if ($this->Departments->save($department)) {
                $this->Flash->success(__('The department has been saved.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The department could not be saved. Please, try again.'));
        }
        $this->set(compact('department'));
    }


    public function delete($id = null)
    {
        $this->request->allowMethod(['post', 'delete']);
        $department = $this->Departments->get($id);
        if ($this->Departments->delete($department)) {
            $this->Flash->success(__('The department has been deleted.'));
        } else {
            $this->Flash->error(__('The department could not be deleted. Please, try again.'));
        }

        return $this->redirect(['action' => 'index']);
    }
}
