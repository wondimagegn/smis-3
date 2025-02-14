<?php
namespace App\Controller;

use App\Controller\AppController;

class CourseCategoriesController extends AppController
{

    public function index()
    {
        $this->paginate = [
            'contain' => ['Curriculums'],
        ];
        $courseCategories = $this->paginate($this->CourseCategories);

        $this->set(compact('courseCategories'));
    }

    public function view($id = null)
    {
        $courseCategory = $this->CourseCategories->get($id, [
            'contain' => ['Curriculums', 'Courses', 'OtherAcademicRules'],
        ]);

        $this->set('courseCategory', $courseCategory);
    }

    public function add()
    {
        $courseCategory = $this->CourseCategories->newEntity();
        if ($this->request->is('post')) {
            $courseCategory = $this->CourseCategories->patchEntity($courseCategory, $this->request->getData());
            if ($this->CourseCategories->save($courseCategory)) {
                $this->Flash->success(__('The course category has been saved.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The course category could not be saved. Please, try again.'));
        }

        $this->set(compact('courseCategory'));
    }


    public function edit($id = null)
    {
        $courseCategory = $this->CourseCategories->get($id, [
            'contain' => [],
        ]);
        if ($this->request->is(['patch', 'post', 'put'])) {
            $courseCategory = $this->CourseCategories->patchEntity($courseCategory, $this->request->getData());
            if ($this->CourseCategories->save($courseCategory)) {
                $this->Flash->success(__('The course category has been saved.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The course category could not be saved. Please, try again.'));
        }

        $this->set(compact('courseCategory'));
    }

    public function delete($id = null)
    {
        $this->request->allowMethod(['post', 'delete']);
        $courseCategory = $this->CourseCategories->get($id);
        if ($this->CourseCategories->delete($courseCategory)) {
            $this->Flash->success(__('The course category has been deleted.'));
        } else {
            $this->Flash->error(__('The course category could not be deleted. Please, try again.'));
        }

        return $this->redirect(['action' => 'index']);
    }
}
