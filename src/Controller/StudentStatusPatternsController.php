<?php
namespace App\Controller;

use App\Controller\AppController;

class StudentStatusPatternsController extends AppController
{

    public function index()
    {
        $this->paginate = [
            'contain' => ['Programs', 'ProgramTypes'],
        ];
        $studentStatusPatterns = $this->paginate($this->StudentStatusPatterns);

        $this->set(compact('studentStatusPatterns'));
    }

    public function view($id = null)
    {
        $studentStatusPattern = $this->StudentStatusPatterns->get($id, [
            'contain' => ['Programs', 'ProgramTypes'],
        ]);

        $this->set('studentStatusPattern', $studentStatusPattern);
    }

    public function add()
    {
        $studentStatusPattern = $this->StudentStatusPatterns->newEntity();
        if ($this->request->is('post')) {
            $studentStatusPattern = $this->StudentStatusPatterns->patchEntity($studentStatusPattern, $this->request->getData());
            if ($this->StudentStatusPatterns->save($studentStatusPattern)) {
                $this->Flash->success(__('The student status pattern has been saved.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The student status pattern could not be saved. Please, try again.'));
        }
        $this->set(compact('studentStatusPattern', 'programs', 'programTypes'));
    }

    public function edit($id = null)
    {
        $studentStatusPattern = $this->StudentStatusPatterns->get($id, [
            'contain' => [],
        ]);
        if ($this->request->is(['patch', 'post', 'put'])) {
            $studentStatusPattern = $this->StudentStatusPatterns->patchEntity($studentStatusPattern, $this->request->getData());
            if ($this->StudentStatusPatterns->save($studentStatusPattern)) {
                $this->Flash->success(__('The student status pattern has been saved.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The student status pattern could not be saved. Please, try again.'));
        }
        $this->set(compact('studentStatusPattern'));
    }

    public function delete($id = null)
    {
        $this->request->allowMethod(['post', 'delete']);
        $studentStatusPattern = $this->StudentStatusPatterns->get($id);
        if ($this->StudentStatusPatterns->delete($studentStatusPattern)) {
            $this->Flash->success(__('The student status pattern has been deleted.'));
        } else {
            $this->Flash->error(__('The student status pattern could not be deleted. Please, try again.'));
        }

        return $this->redirect(['action' => 'index']);
    }
}
