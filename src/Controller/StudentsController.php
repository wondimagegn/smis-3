<?php
namespace App\Controller;

use App\Controller\AppController;

class StudentsController extends AppController
{

    public function index()
    {
        $this->paginate = [
            'contain' => ['Users', 'AcceptedStudents', 'Departments', 'Colleges', 'Countries', 'Regions',
                'Zones', 'Woredas', 'Cities', 'Programs', 'ProgramTypes', 'Specializations',
                'Curriculums'],
        ];
        $students = $this->paginate($this->Students);

        $this->set(compact('students'));
    }

    public function view($id = null)
    {
        $student = $this->Students->get($id, [
            'contain' => ['Users', 'AcceptedStudents', 'Departments', 'Colleges',
                'Countries', 'Regions', 'Zones', 'Woredas', 'Cities',
                'Programs', 'ProgramTypes', 'Specializations', 'Curriculums',
                'Alumni', 'ApplicablePayments', 'Attendances', 'CertificateVerificationCodes',
                'Clearances', 'Contacts', 'CostShares', 'CostSharingPayments', 'CourseAdds',
                'CourseDrops', 'CourseExemptions', 'CourseRegistrations', 'CourseSubstitutionRequests',
                'CurriculumAttachments', 'DepartmentTransfers', 'Disciplines', 'Dismissals',
                'DormitoryAssignments', 'DropOuts', 'EheeceResults', 'EslceResults',
                'ExceptionMealAssignments', 'ExitExams',
                'FxResitRequest', 'GraduateLists',
                'GraduationWorks', 'HighSchoolEducationBackgrounds',
                'HigherEducationBackgrounds', 'HistoricalStudentCourseGradeExcludes',
                'HistoricalStudentExamStatuses', 'MakeupExams', 'MealAttendances',
                'MealHallAssignments', 'MedicalHistories', 'Otps', 'Payments',
                'PlacementEntranceExamResultEntries', 'PlacementParticipatingStudents',
                'PlacementPreferences', 'ProgramTypeTransfers', 'Readmissions',
                'ResultEntryAssignments', 'SenateLists', 'StudentEvalutionComments',
                'StudentEvalutionRates', 'StudentExamStatuses', 'StudentNameHistories',
                'StudentRanks', 'TakenProperties', 'Withdrawals'],
        ]);

        $this->set('student', $student);
    }


    public function add()
    {
        $student = $this->Students->newEntity();
        if ($this->request->is('post')) {
            $student = $this->Students->patchEntity($student, $this->request->getData());
            if ($this->Students->save($student)) {
                $this->Flash->success(__('The student has been saved.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The student could not be saved. Please, try again.'));
        }

    }


    public function edit($id = null)
    {
        $student = $this->Students->get($id, [
            'contain' => [],
        ]);
        if ($this->request->is(['patch', 'post', 'put'])) {
            $student = $this->Students->patchEntity($student, $this->request->getData());
            if ($this->Students->save($student)) {
                $this->Flash->success(__('The student has been saved.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The student could not be saved. Please, try again.'));
        }


    }

    public function delete($id = null)
    {
        $this->request->allowMethod(['post', 'delete']);
        $student = $this->Students->get($id);
        if ($this->Students->delete($student)) {
            $this->Flash->success(__('The student has been deleted.'));
        } else {
            $this->Flash->error(__('The student could not be deleted. Please, try again.'));
        }

        return $this->redirect(['action' => 'index']);
    }
}
