<?php
namespace App\Controller;

use App\Controller\AppController;

class CourseSplitSectionsController extends AppController
{

    public function index()
    {
        $this->paginate = [
            'contain' => ['SectionSplitForPublishedCourses'],
        ];
        $courseSplitSections = $this->paginate($this->CourseSplitSections);

        $this->set(compact('courseSplitSections'));
    }


    public function view($id = null)
    {
        $courseSplitSection = $this->CourseSplitSections->get($id, [
            'contain' => ['SectionSplitForPublishedCourses', 'Students', 'CourseInstructorAssignments', 'CourseSchedules', 'UnschedulePublishedCourses'],
        ]);

        $this->set('courseSplitSection', $courseSplitSection);
    }

    public function add()
    {
        $courseSplitSection = $this->CourseSplitSections->newEntity();
        if ($this->request->is('post')) {
            $courseSplitSection = $this->CourseSplitSections->patchEntity($courseSplitSection, $this->request->getData());
            if ($this->CourseSplitSections->save($courseSplitSection)) {
                $this->Flash->success(__('The course split section has been saved.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The course split section could not be saved. Please, try again.'));
        }
        $this->set(compact('courseSplitSection'));
    }


    public function edit($id = null)
    {
        $courseSplitSection = $this->CourseSplitSections->get($id, [
            'contain' => ['Students'],
        ]);
        if ($this->request->is(['patch', 'post', 'put'])) {
            $courseSplitSection = $this->CourseSplitSections->patchEntity($courseSplitSection, $this->request->getData());
            if ($this->CourseSplitSections->save($courseSplitSection)) {
                $this->Flash->success(__('The course split section has been saved.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The course split section could not be saved. Please, try again.'));
        }
        $this->set(compact('courseSplitSection'));
    }

    public function delete($id = null)
    {
        $this->request->allowMethod(['post', 'delete']);
        $courseSplitSection = $this->CourseSplitSections->get($id);
        if ($this->CourseSplitSections->delete($courseSplitSection)) {
            $this->Flash->success(__('The course split section has been deleted.'));
        } else {
            $this->Flash->error(__('The course split section could not be deleted. Please, try again.'));
        }

        return $this->redirect(['action' => 'index']);
    }
}
