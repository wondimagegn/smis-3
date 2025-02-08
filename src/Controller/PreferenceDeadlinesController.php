<?php
namespace App\Controller;

use App\Controller\AppController;

/**
 * PreferenceDeadlines Controller
 *
 * @property \App\Model\Table\PreferenceDeadlinesTable $PreferenceDeadlines
 *
 * @method \App\Model\Entity\PreferenceDeadline[]|\Cake\Datasource\ResultSetInterface paginate($object = null, array $settings = [])
 */
class PreferenceDeadlinesController extends AppController
{
    /**
     * Index method
     *
     * @return \Cake\Http\Response|null
     */
    public function index()
    {
        $this->paginate = [
            'contain' => ['Users', 'Colleges'],
        ];
        $preferenceDeadlines = $this->paginate($this->PreferenceDeadlines);

        $this->set(compact('preferenceDeadlines'));
    }

    public function view($id = null)
    {
        $preferenceDeadline = $this->PreferenceDeadlines->get($id, [
            'contain' => ['Users', 'Colleges'],
        ]);

        $this->set('preferenceDeadline', $preferenceDeadline);
    }

    public function add()
    {
        $preferenceDeadline = $this->PreferenceDeadlines->newEntity();
        if ($this->request->is('post')) {
            $preferenceDeadline = $this->PreferenceDeadlines->patchEntity($preferenceDeadline, $this->request->getData());
            if ($this->PreferenceDeadlines->save($preferenceDeadline)) {
                $this->Flash->success(__('The preference deadline has been saved.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The preference deadline could not be saved. Please, try again.'));
        }

        $this->set(compact('preferenceDeadline'));
    }


    public function edit($id = null)
    {
        $preferenceDeadline = $this->PreferenceDeadlines->get($id, [
            'contain' => [],
        ]);
        if ($this->request->is(['patch', 'post', 'put'])) {
            $preferenceDeadline = $this->PreferenceDeadlines->patchEntity($preferenceDeadline, $this->request->getData());
            if ($this->PreferenceDeadlines->save($preferenceDeadline)) {
                $this->Flash->success(__('The preference deadline has been saved.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The preference deadline could not be saved. Please, try again.'));
        }
        $this->set(compact('preferenceDeadline', 'users', 'colleges'));
    }

    public function delete($id = null)
    {
        $this->request->allowMethod(['post', 'delete']);
        $preferenceDeadline = $this->PreferenceDeadlines->get($id);
        if ($this->PreferenceDeadlines->delete($preferenceDeadline)) {
            $this->Flash->success(__('The preference deadline has been deleted.'));
        } else {
            $this->Flash->error(__('The preference deadline could not be deleted. Please, try again.'));
        }

        return $this->redirect(['action' => 'index']);
    }
}
