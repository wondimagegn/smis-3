<?php
namespace App\Controller;

use App\Controller\AppController;

class ProgramTypesController extends AppController
{
    public function index()
    {
        $this->paginate = [
            'contain' => ['EquivalentTos', 'ProgramModalities'],
        ];
        $programTypes = $this->paginate($this->ProgramTypes);

        $this->set(compact('programTypes'));
    }

    public function view($id = null)
    {
        $programType = $this->ProgramTypes->get($id, [
            'contain' => ['ProgramModalities', 'AcademicCalendars'],
        ]);

        $this->set('programType', $programType);
    }

    public function add()
    {
        $programType = $this->ProgramTypes->newEntity();
        if ($this->request->is('post')) {
            $programType = $this->ProgramTypes->patchEntity($programType, $this->request->getData());
            if ($this->ProgramTypes->save($programType)) {
                $this->Flash->success(__('The program type has been saved.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The program type could not be saved. Please, try again.'));
        }
      $this->set(compact('programType', 'programModalities'));
    }


    public function edit($id = null)
    {
        $programType = $this->ProgramTypes->get($id, [
            'contain' => [],
        ]);
        if ($this->request->is(['patch', 'post', 'put'])) {
            $programType = $this->ProgramTypes->patchEntity($programType, $this->request->getData());
            if ($this->ProgramTypes->save($programType)) {
                $this->Flash->success(__('The program type has been saved.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The program type could not be saved. Please, try again.'));
        }
       $this->set(compact('programType', 'programModalities'));
    }

    public function delete($id = null)
    {
        $this->request->allowMethod(['post', 'delete']);
        $programType = $this->ProgramTypes->get($id);
        if ($this->ProgramTypes->delete($programType)) {
            $this->Flash->success(__('The program type has been deleted.'));
        } else {
            $this->Flash->error(__('The program type could not be deleted. Please, try again.'));
        }

        return $this->redirect(['action' => 'index']);
    }
}
