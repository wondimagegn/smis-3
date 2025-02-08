<?php
namespace App\Controller;

use App\Controller\AppController;

class CollegesController extends AppController
{

    public function index()
    {
        $this->paginate = [
            'contain' => ['Campuses', 'MoodleCategories'],
        ];
        $colleges = $this->paginate($this->Colleges);

        $this->set(compact('colleges'));
    }

    public function view($id = null)
    {
        $college = $this->Colleges->get($id, [
            'contain' => ['Campuses', 'MoodleCategories', 'AcademicCalendars', 'AcceptedStudents', 'ClassPeriods', 'ClassRoomBlocks', 'Departments', 'ExamPeriods', 'InstructorClassPeriodCourseConstraints', 'InstructorNumberOfExamConstraints', 'Notes', 'OnlineApplicants', 'ParticipatingDepartments', 'PeriodSettings', 'PlacementLocks', 'PlacementsResultsCriterias', 'PreferenceDeadlines', 'Preferences', 'PublishedCourses', 'Quotas', 'ReservedPlaces', 'Sections', 'StaffAssignes', 'StaffForExams', 'Staffs', 'Students', 'TakenProperties'],
        ]);

        $this->set('college', $college);
    }

    public function add()
    {
        $college = $this->Colleges->newEntity();
        if ($this->request->is('post')) {
            $college = $this->Colleges->patchEntity($college, $this->request->getData());
            if ($this->Colleges->save($college)) {
                $this->Flash->success(__('The college has been saved.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The college could not be saved. Please, try again.'));
        }
        $campuses = $this->Colleges->Campuses->find('list', ['limit' => 200]);
        $moodleCategories = $this->Colleges->MoodleCategories->find('list', ['limit' => 200]);
        $this->set(compact('college', 'campuses', 'moodleCategories'));
    }

    public function edit($id = null)
    {
        $college = $this->Colleges->get($id, [
            'contain' => [],
        ]);
        if ($this->request->is(['patch', 'post', 'put'])) {
            $college = $this->Colleges->patchEntity($college, $this->request->getData());
            if ($this->Colleges->save($college)) {
                $this->Flash->success(__('The college has been saved.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The college could not be saved. Please, try again.'));
        }
        $campuses = $this->Colleges->Campuses->find('list', ['limit' => 200]);
        $moodleCategories = $this->Colleges->MoodleCategories->find('list', ['limit' => 200]);
        $this->set(compact('college', 'campuses', 'moodleCategories'));
    }

    public function delete($id = null)
    {
        $this->request->allowMethod(['post', 'delete']);
        $college = $this->Colleges->get($id);
        if ($this->Colleges->delete($college)) {
            $this->Flash->success(__('The college has been deleted.'));
        } else {
            $this->Flash->error(__('The college could not be deleted. Please, try again.'));
        }

        return $this->redirect(['action' => 'index']);
    }
}
