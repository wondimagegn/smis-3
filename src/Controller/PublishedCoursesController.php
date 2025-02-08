<?php
namespace App\Controller;

use App\Controller\AppController;

class PublishedCoursesController extends AppController
{

    public function index()
    {
        $this->paginate = [
            'contain' => ['YearLevels', 'Courses', 'ProgramTypes', 'Programs',
                'Departments', 'GivenByDepartments', 'Sections', 'Colleges', 'GradeScales'],
        ];
        $publishedCourses = $this->paginate($this->PublishedCourses);

        $this->set(compact('publishedCourses'));
    }

    public function view($id = null)
    {
        $publishedCourse = $this->PublishedCourses->get($id, [
            'contain' => ['YearLevels', 'Courses', 'ProgramTypes', 'Programs', 'Departments', 'GivenByDepartments', 'Sections', 'Colleges', 'GradeScales', 'Attendances', 'ClassPeriodCourseConstraints', 'ClassRoomCourseConstraints', 'CourseAdds', 'CourseExamConstraints', 'CourseExamGapConstraints', 'CourseInstructorAssignments', 'CourseRegistrations', 'CourseSchedules', 'ExamRoomCourseConstraints', 'ExamSchedules', 'ExamTypes', 'ExcludedPublishedCourseExams', 'FxResitRequest', 'GradeScalePublishedCourses', 'HistoricalStudentCourseGradeExcludes', 'MakeupExams', 'MergedSectionsCourses', 'MergedSectionsExams', 'MoodleCourseEnrollments', 'MoodleCourses', 'ResultEntryAssignments', 'SectionSplitForExams', 'SectionSplitForPublishedCourses', 'StudentEvalutionComments', 'StudentEvalutionRates', 'UnschedulePublishedCourses'],
        ]);

        $this->set('publishedCourse', $publishedCourse);
    }

    public function add()
    {
        $publishedCourse = $this->PublishedCourses->newEntity();
        if ($this->request->is('post')) {
            $publishedCourse = $this->PublishedCourses->patchEntity($publishedCourse, $this->request->getData());
            if ($this->PublishedCourses->save($publishedCourse)) {
                $this->Flash->success(__('The published course has been saved.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The published course could not be saved. Please, try again.'));
        }
        $this->set(compact('publishedCourse'));
    }


    public function edit($id = null)
    {
        $publishedCourse = $this->PublishedCourses->get($id, [
            'contain' => [],
        ]);
        if ($this->request->is(['patch', 'post', 'put'])) {
            $publishedCourse = $this->PublishedCourses->patchEntity($publishedCourse, $this->request->getData());
            if ($this->PublishedCourses->save($publishedCourse)) {
                $this->Flash->success(__('The published course has been saved.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The published course could not be saved. Please, try again.'));
        }

        $this->set(compact('publishedCourse'));
    }

    public function delete($id = null)
    {
        $this->request->allowMethod(['post', 'delete']);
        $publishedCourse = $this->PublishedCourses->get($id);
        if ($this->PublishedCourses->delete($publishedCourse)) {
            $this->Flash->success(__('The published course has been deleted.'));
        } else {
            $this->Flash->error(__('The published course could not be deleted. Please, try again.'));
        }

        return $this->redirect(['action' => 'index']);
    }
}
