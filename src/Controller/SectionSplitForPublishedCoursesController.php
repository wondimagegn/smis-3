<?php
namespace App\Controller;

use App\Controller\AppController;

class SectionSplitForPublishedCoursesController extends AppController
{

    public function index()
    {
        $this->paginate = [
            'contain' => ['PublishedCourses', 'Sections'],
        ];
        $sectionSplitForPublishedCourses = $this->paginate($this->SectionSplitForPublishedCourses);

        $this->set(compact('sectionSplitForPublishedCourses'));
    }


    public function view($id = null)
    {
        $sectionSplitForPublishedCourse = $this->SectionSplitForPublishedCourses->get($id, [
            'contain' => ['PublishedCourses', 'Sections', 'CourseSplitSections'],
        ]);

        $this->set('sectionSplitForPublishedCourse', $sectionSplitForPublishedCourse);
    }

    public function add()
    {
        $sectionSplitForPublishedCourse = $this->SectionSplitForPublishedCourses->newEntity();
        if ($this->request->is('post')) {
            $sectionSplitForPublishedCourse = $this->SectionSplitForPublishedCourses->patchEntity($sectionSplitForPublishedCourse, $this->request->getData());
            if ($this->SectionSplitForPublishedCourses->save($sectionSplitForPublishedCourse)) {
                $this->Flash->success(__('The section split for published course has been saved.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The section split for published course could not be saved. Please, try again.'));
        }
        $this->set(compact('sectionSplitForPublishedCourse'));
    }

    public function edit($id = null)
    {
        $sectionSplitForPublishedCourse = $this->SectionSplitForPublishedCourses->get($id, [
            'contain' => [],
        ]);
        if ($this->request->is(['patch', 'post', 'put'])) {
            $sectionSplitForPublishedCourse = $this->SectionSplitForPublishedCourses->patchEntity($sectionSplitForPublishedCourse, $this->request->getData());
            if ($this->SectionSplitForPublishedCourses->save($sectionSplitForPublishedCourse)) {
                $this->Flash->success(__('The section split for published course has been saved.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The section split for published course could not be saved. Please, try again.'));
        }

        $this->set(compact('sectionSplitForPublishedCourse'));
    }

    public function delete($id = null)
    {
        $this->request->allowMethod(['post', 'delete']);
        $sectionSplitForPublishedCourse = $this->SectionSplitForPublishedCourses->get($id);
        if ($this->SectionSplitForPublishedCourses->delete($sectionSplitForPublishedCourse)) {
            $this->Flash->success(__('The section split for published course has been deleted.'));
        } else {
            $this->Flash->error(__('The section split for published course could not be deleted. Please, try again.'));
        }

        return $this->redirect(['action' => 'index']);
    }
}
