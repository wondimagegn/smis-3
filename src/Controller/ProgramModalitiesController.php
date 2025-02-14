<?php
namespace App\Controller;

use App\Controller\AppController;

class ProgramModalitiesController extends AppController
{

    public function index()
    {
        $programModalities = $this->paginate($this->ProgramModalities);

        $this->set(compact('programModalities'));
    }

    public function view($id = null)
    {
        $programModality = $this->ProgramModalities->get($id, [
            'contain' => ['DepartmentStudyPrograms', 'ProgramTypes'],
        ]);

        $this->set('programModality', $programModality);
    }

    public function add()
    {
        $programModality = $this->ProgramModalities->newEntity();
        if ($this->request->is('post')) {
            $programModality = $this->ProgramModalities->patchEntity($programModality, $this->request->getData());
            if ($this->ProgramModalities->save($programModality)) {
                $this->Flash->success(__('The program modality has been saved.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The program modality could not be saved. Please, try again.'));
        }
        $this->set(compact('programModality'));
    }

    public function edit($id = null)
    {
        $programModality = $this->ProgramModalities->get($id, [
            'contain' => [],
        ]);
        if ($this->request->is(['patch', 'post', 'put'])) {
            $programModality = $this->ProgramModalities->patchEntity($programModality, $this->request->getData());
            if ($this->ProgramModalities->save($programModality)) {
                $this->Flash->success(__('The program modality has been saved.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The program modality could not be saved. Please, try again.'));
        }
        $this->set(compact('programModality'));
    }

    public function delete($id = null)
    {
        $this->request->allowMethod(['post', 'delete']);
        $programModality = $this->ProgramModalities->get($id);
        if ($this->ProgramModalities->delete($programModality)) {
            $this->Flash->success(__('The program modality has been deleted.'));
        } else {
            $this->Flash->error(__('The program modality could not be deleted. Please, try again.'));
        }

        return $this->redirect(['action' => 'index']);
    }
}
