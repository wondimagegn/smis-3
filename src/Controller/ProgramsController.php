<?php
namespace App\Controller;

use App\Controller\AppController;

class ProgramsController extends AppController
{

    public function index()
    {
        $programs = $this->paginate($this->Programs);

        $this->set(compact('programs'));
    }

    public function view($id = null)
    {
        $program = $this->Programs->get($id, [
            'contain' => ['AcademicCalendars', 'AcademicStands', 'AcceptedStudents', 'ClassPeriods', 'Curriculums', 'ExamPeriods', 'ExtendingAcademicCalendars', 'GeneralSettings', 'GradeScales', 'GraduationCertificates', 'GraduationLetters', 'GraduationRequirements', 'GraduationStatuses', 'OnlineApplicants', 'OtherAcademicRules', 'PlacementAdditionalPoints', 'PlacementDeadlines', 'PlacementParticipatingStudents', 'PlacementResultSettings', 'PlacementRoundParticipants', 'ProgramProgramTypeClassRooms', 'PublishedCourses', 'Qualifications', 'Sections', 'StaffAssignes', 'StudentStatusPatterns', 'Students', 'TranscriptFooters'],
        ]);

        $this->set('program', $program);
    }

    public function add()
    {
        $program = $this->Programs->newEntity();
        if ($this->request->is('post')) {
            $program = $this->Programs->patchEntity($program, $this->request->getData());
            if ($this->Programs->save($program)) {
                $this->Flash->success(__('The program has been saved.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The program could not be saved. Please, try again.'));
        }
        $this->set(compact('program'));
    }

    public function edit($id = null)
    {
        $program = $this->Programs->get($id, [
            'contain' => [],
        ]);
        if ($this->request->is(['patch', 'post', 'put'])) {
            $program = $this->Programs->patchEntity($program, $this->request->getData());
            if ($this->Programs->save($program)) {
                $this->Flash->success(__('The program has been saved.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The program could not be saved. Please, try again.'));
        }
        $this->set(compact('program'));
    }

    public function delete($id = null)
    {
        $this->request->allowMethod(['post', 'delete']);
        $program = $this->Programs->get($id);
        if ($this->Programs->delete($program)) {
            $this->Flash->success(__('The program has been deleted.'));
        } else {
            $this->Flash->error(__('The program could not be deleted. Please, try again.'));
        }

        return $this->redirect(['action' => 'index']);
    }
}
