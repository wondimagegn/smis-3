<?php
namespace App\Controller;

use App\Controller\AppController;

class GraduationRequirementsController extends AppController
{

    public function index()
    {
        $this->paginate = [
            'contain' => ['Programs'],
        ];
        $graduationRequirements = $this->paginate($this->GraduationRequirements);

        $this->set(compact('graduationRequirements'));
    }


    public function view($id = null)
    {
        $graduationRequirement = $this->GraduationRequirements->get($id, [
            'contain' => ['Programs'],
        ]);

        $this->set('graduationRequirement', $graduationRequirement);
    }


    public function add()
    {
        $graduationRequirement = $this->GraduationRequirements->newEntity();
        if ($this->request->is('post')) {
            $graduationRequirement = $this->GraduationRequirements->patchEntity($graduationRequirement, $this->request->getData());
            if ($this->GraduationRequirements->save($graduationRequirement)) {
                $this->Flash->success(__('The graduation requirement has been saved.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The graduation requirement could not be saved. Please, try again.'));
        }
        $this->set(compact('graduationRequirement'));
    }


    public function edit($id = null)
    {
        $graduationRequirement = $this->GraduationRequirements->get($id, [
            'contain' => [],
        ]);
        if ($this->request->is(['patch', 'post', 'put'])) {
            $graduationRequirement = $this->GraduationRequirements->patchEntity($graduationRequirement, $this->request->getData());
            if ($this->GraduationRequirements->save($graduationRequirement)) {
                $this->Flash->success(__('The graduation requirement has been saved.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The graduation requirement could not be saved. Please, try again.'));
        }

        $this->set(compact('graduationRequirement'));
    }


    public function delete($id = null)
    {
        $this->request->allowMethod(['post', 'delete']);
        $graduationRequirement = $this->GraduationRequirements->get($id);
        if ($this->GraduationRequirements->delete($graduationRequirement)) {
            $this->Flash->success(__('The graduation requirement has been deleted.'));
        } else {
            $this->Flash->error(__('The graduation requirement could not be deleted. Please, try again.'));
        }

        return $this->redirect(['action' => 'index']);
    }
}
