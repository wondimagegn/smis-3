<?php
namespace App\Controller;

use App\Controller\AppController;

class PlacementDeadlinesController extends AppController
{
    /**
     * Index method
     *
     * @return \Cake\Http\Response|null
     */
    public function index()
    {
        $this->paginate = [
            'contain' => ['Programs', 'ProgramTypes'],
        ];
        $placementDeadlines = $this->paginate($this->PlacementDeadlines);

        $this->set(compact('placementDeadlines'));
    }

    public function view($id = null)
    {
        $placementDeadline = $this->PlacementDeadlines->get($id, [
            'contain' => ['Programs', 'ProgramTypes'],
        ]);

        $this->set('placementDeadline', $placementDeadline);
    }

    public function add()
    {
        $placementDeadline = $this->PlacementDeadlines->newEntity();
        if ($this->request->is('post')) {
            $placementDeadline = $this->PlacementDeadlines->patchEntity($placementDeadline, $this->request->getData());
            if ($this->PlacementDeadlines->save($placementDeadline)) {
                $this->Flash->success(__('The placement deadline has been saved.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The placement deadline could not be saved. Please, try again.'));
        }
        $this->set(compact('placementDeadline'));
    }

    public function edit($id = null)
    {
        $placementDeadline = $this->PlacementDeadlines->get($id, [
            'contain' => [],
        ]);
        if ($this->request->is(['patch', 'post', 'put'])) {
            $placementDeadline = $this->PlacementDeadlines->patchEntity($placementDeadline, $this->request->getData());
            if ($this->PlacementDeadlines->save($placementDeadline)) {
                $this->Flash->success(__('The placement deadline has been saved.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The placement deadline could not be saved. Please, try again.'));
        }
        $this->set(compact('placementDeadline'));
    }

    public function delete($id = null)
    {
        $this->request->allowMethod(['post', 'delete']);
        $placementDeadline = $this->PlacementDeadlines->get($id);
        if ($this->PlacementDeadlines->delete($placementDeadline)) {
            $this->Flash->success(__('The placement deadline has been deleted.'));
        } else {
            $this->Flash->error(__('The placement deadline could not be deleted. Please, try again.'));
        }

        return $this->redirect(['action' => 'index']);
    }
}
