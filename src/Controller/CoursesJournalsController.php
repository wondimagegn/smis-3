<?php
namespace App\Controller;

use App\Controller\AppController;

class CoursesJournalsController extends AppController
{

    public function index()
    {
        $this->paginate = [
            'contain' => ['Courses', 'Journals'],
        ];
        $coursesJournals = $this->paginate($this->CoursesJournals);

        $this->set(compact('coursesJournals'));
    }


    public function view($id = null)
    {
        $coursesJournal = $this->CoursesJournals->get($id, [
            'contain' => ['Courses', 'Journals'],
        ]);

        $this->set('coursesJournal', $coursesJournal);
    }


    public function add()
    {
        $coursesJournal = $this->CoursesJournals->newEntity();
        if ($this->request->is('post')) {
            $coursesJournal = $this->CoursesJournals->patchEntity($coursesJournal, $this->request->getData());
            if ($this->CoursesJournals->save($coursesJournal)) {
                $this->Flash->success(__('The courses journal has been saved.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The courses journal could not be saved. Please, try again.'));
        }

        $this->set(compact('coursesJournal'));
    }


    public function edit($id = null)
    {
        $coursesJournal = $this->CoursesJournals->get($id, [
            'contain' => [],
        ]);
        if ($this->request->is(['patch', 'post', 'put'])) {
            $coursesJournal = $this->CoursesJournals->patchEntity($coursesJournal, $this->request->getData());
            if ($this->CoursesJournals->save($coursesJournal)) {
                $this->Flash->success(__('The courses journal has been saved.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The courses journal could not be saved. Please, try again.'));
        }

        $this->set(compact('coursesJournal'));
    }


    public function delete($id = null)
    {
        $this->request->allowMethod(['post', 'delete']);
        $coursesJournal = $this->CoursesJournals->get($id);
        if ($this->CoursesJournals->delete($coursesJournal)) {
            $this->Flash->success(__('The courses journal has been deleted.'));
        } else {
            $this->Flash->error(__('The courses journal could not be deleted. Please, try again.'));
        }

        return $this->redirect(['action' => 'index']);
    }
}
