<?php
namespace App\Controller;

use App\Controller\AppController;

class GradeTypesController extends AppController
{

    public function index()
    {
        $gradeTypes = $this->paginate($this->GradeTypes);

        $this->set(compact('gradeTypes'));
    }

    public function view($id = null)
    {
        $gradeType = $this->GradeTypes->get($id, [
            'contain' => ['Courses', 'GradeScales', 'Grades'],
        ]);

        $this->set('gradeType', $gradeType);
    }

    public function add()
    {
        $gradeType = $this->GradeTypes->newEntity();
        if ($this->request->is('post')) {
            $gradeType = $this->GradeTypes->patchEntity($gradeType, $this->request->getData());
            if ($this->GradeTypes->save($gradeType)) {
                $this->Flash->success(__('The grade type has been saved.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The grade type could not be saved. Please, try again.'));
        }
        $this->set(compact('gradeType'));
    }

    public function edit($id = null)
    {
        $gradeType = $this->GradeTypes->get($id, [
            'contain' => [],
        ]);
        if ($this->request->is(['patch', 'post', 'put'])) {
            $gradeType = $this->GradeTypes->patchEntity($gradeType, $this->request->getData());
            if ($this->GradeTypes->save($gradeType)) {
                $this->Flash->success(__('The grade type has been saved.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The grade type could not be saved. Please, try again.'));
        }
        $this->set(compact('gradeType'));
    }

    public function delete($id = null)
    {
        $this->request->allowMethod(['post', 'delete']);
        $gradeType = $this->GradeTypes->get($id);
        if ($this->GradeTypes->delete($gradeType)) {
            $this->Flash->success(__('The grade type has been deleted.'));
        } else {
            $this->Flash->error(__('The grade type could not be deleted. Please, try again.'));
        }

        return $this->redirect(['action' => 'index']);
    }
}
