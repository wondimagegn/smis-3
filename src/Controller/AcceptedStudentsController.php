<?php
namespace App\Controller;

use App\Controller\AppController;

class AcceptedStudentsController extends AppController
{

    public function index()
    {
        $this->paginate = [
            'contain' => ['Regions', 'Zones', 'Woredas', 'Colleges', 'Campuses', 'OriginalColleges', 'Departments', 'Curriculums', 'Programs', 'ProgramTypes', 'Specializations', 'PlacementTypes', 'Users', 'OnlineApplicants', 'Disabilities', 'ForeignPrograms'],
        ];
        $acceptedStudents = $this->paginate($this->AcceptedStudents);

        $this->set(compact('acceptedStudents'));
    }

    public function view($id = null)
    {
        $acceptedStudent = $this->AcceptedStudents->get($id, [
            'contain' => ['Regions', 'Zones', 'Woredas', 'Colleges', 'Campuses',
                 'Departments', 'Curriculums', 'Programs', 'ProgramTypes', 'Specializations',
                'PlacementTypes', 'Users', 'OnlineApplicants',
                'DormitoryAssignments', 'MealHallAssignments', 'PlacementEntranceExamResultEntries',
                'PlacementParticipatingStudents', 'PlacementPreferences', 'Preferences', 'Students'],
        ]);

        $this->set('acceptedStudent', $acceptedStudent);
    }

    /**
     * Add method
     *
     * @return \Cake\Http\Response|null Redirects on successful add, renders view otherwise.
     */
    public function add()
    {
        $acceptedStudent = $this->AcceptedStudents->newEntity();
        if ($this->request->is('post')) {
            $acceptedStudent = $this->AcceptedStudents->patchEntity($acceptedStudent, $this->request->getData());
            if ($this->AcceptedStudents->save($acceptedStudent)) {
                $this->Flash->success(__('The accepted student has been saved.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The accepted student could not be saved. Please, try again.'));
        }

    }

    public function edit($id = null)
    {
        $acceptedStudent = $this->AcceptedStudents->get($id, [
            'contain' => [],
        ]);
        if ($this->request->is(['patch', 'post', 'put'])) {
            $acceptedStudent = $this->AcceptedStudents->patchEntity($acceptedStudent, $this->request->getData());
            if ($this->AcceptedStudents->save($acceptedStudent)) {
                $this->Flash->success(__('The accepted student has been saved.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The accepted student could not be saved. Please, try again.'));
        }
    }


    public function delete($id = null)
    {
        $this->request->allowMethod(['post', 'delete']);
        $acceptedStudent = $this->AcceptedStudents->get($id);
        if ($this->AcceptedStudents->delete($acceptedStudent)) {
            $this->Flash->success(__('The accepted student has been deleted.'));
        } else {
            $this->Flash->error(__('The accepted student could not be deleted. Please, try again.'));
        }

        return $this->redirect(['action' => 'index']);
    }
}
