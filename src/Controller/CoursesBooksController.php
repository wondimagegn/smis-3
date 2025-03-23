<?php


namespace App\Controller;

use App\Controller\AppController;

use Cake\Event\Event;
use Cake\ORM\TableRegistry;
use Cake\Core\Configure;

class CoursesBooksController extends AppController
{
    public function index()
    {
        $this->paginate = [
            'contain' => ['Courses', 'Books'],
        ];
        $coursesBooks = $this->paginate($this->CoursesBooks);

        $this->set(compact('coursesBooks'));
    }

    public function view($id = null)
    {
        $coursesBook = $this->CoursesBooks->get($id, [
            'contain' => ['Courses', 'Books'],
        ]);

        $this->set('coursesBook', $coursesBook);
    }

    public function add()
    {
        $coursesBook = $this->CoursesBooks->newEntity();
        if ($this->request->is('post')) {
            $coursesBook = $this->CoursesBooks->patchEntity($coursesBook, $this->request->getData());
            if ($this->CoursesBooks->save($coursesBook)) {
                $this->Flash->success(__('The courses book has been saved.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The courses book could not be saved. Please, try again.'));
        }

        $this->set(compact('coursesBook'));
    }


    public function edit($id = null)
    {
        $coursesBook = $this->CoursesBooks->get($id, [
            'contain' => [],
        ]);
        if ($this->request->is(['patch', 'post', 'put'])) {
            $coursesBook = $this->CoursesBooks->patchEntity($coursesBook, $this->request->getData());
            if ($this->CoursesBooks->save($coursesBook)) {
                $this->Flash->success(__('The courses book has been saved.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The courses book could not be saved. Please, try again.'));
        }

        $this->set(compact('coursesBook'));
    }

    public function delete($id = null)
    {
        $this->request->allowMethod(['post', 'delete']);
        $coursesBook = $this->CoursesBooks->get($id);
        if ($this->CoursesBooks->delete($coursesBook)) {
            $this->Flash->success(__('The courses book has been deleted.'));
        } else {
            $this->Flash->error(__('The courses book could not be deleted. Please, try again.'));
        }

        return $this->redirect(['action' => 'index']);
    }
}
