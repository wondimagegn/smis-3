<?php
namespace App\Controller;

use App\Controller\AppController;

class StudentsSectionsController extends AppController
{

    public function index()
    {
        $this->paginate = [
            'contain' => ['Students', 'Sections'],
        ];
        $studentsSections = $this->paginate($this->StudentsSections);

        $this->set(compact('studentsSections'));
    }

    public function view($id = null)
    {
        $studentsSection = $this->StudentsSections->get($id, [
            'contain' => ['Students', 'Sections'],
        ]);

        $this->set('studentsSection', $studentsSection);
    }

    public function add()
    {
        $studentsSection = $this->StudentsSections->newEntity();
        if ($this->request->is('post')) {
            $studentsSection = $this->StudentsSections->patchEntity($studentsSection, $this->request->getData());
            if ($this->StudentsSections->save($studentsSection)) {
                $this->Flash->success(__('The students section has been saved.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The students section could not be saved. Please, try again.'));
        }
        $this->set(compact('studentsSection'));
    }


    public function edit($id = null)
    {
        $studentsSection = $this->StudentsSections->get($id, [
            'contain' => [],
        ]);
        if ($this->request->is(['patch', 'post', 'put'])) {
            $studentsSection = $this->StudentsSections->patchEntity($studentsSection, $this->request->getData());
            if ($this->StudentsSections->save($studentsSection)) {
                $this->Flash->success(__('The students section has been saved.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The students section could not be saved. Please, try again.'));
        }
        $this->set(compact('studentsSection'));
    }

    public function delete($id = null)
    {
        $this->request->allowMethod(['post', 'delete']);
        $studentsSection = $this->StudentsSections->get($id);
        if ($this->StudentsSections->delete($studentsSection)) {
            $this->Flash->success(__('The students section has been deleted.'));
        } else {
            $this->Flash->error(__('The students section could not be deleted. Please, try again.'));
        }

        return $this->redirect(['action' => 'index']);
    }
}
