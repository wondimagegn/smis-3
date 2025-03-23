<?php

namespace App\Controller;

use App\Controller\AppController;


use Cake\Event\Event;
use Cake\ORM\TableRegistry;
use Cake\Core\Configure;

class PlacementResultSettingsController extends AppController
{

    public $paginate = [];

    public function initialize()
    {

        parent::initialize();
        $this->loadComponent('Paginator'); // Ensure Paginator is loaded

    }

    public function beforeFilter(Event $event)
    {

        parent::beforeFilter($event);
    }
    public function index()
    {
        $this->paginate = [
            'contain' => ['Programs', 'ProgramTypes'],
        ];
        $placementResultSettings = $this->paginate($this->PlacementResultSettings);

        $this->set(compact('placementResultSettings'));
    }

    public function view($id = null)
    {
        $placementResultSetting = $this->PlacementResultSettings->get($id, [
            'contain' => ['Programs', 'ProgramTypes'],
        ]);

        $this->set('placementResultSetting', $placementResultSetting);
    }

    public function add()
    {
        $placementResultSetting = $this->PlacementResultSettings->newEntity();
        if ($this->request->is('post')) {
            $placementResultSetting = $this->PlacementResultSettings->patchEntity($placementResultSetting, $this->request->getData());
            if ($this->PlacementResultSettings->save($placementResultSetting)) {
                $this->Flash->success(__('The placement result setting has been saved.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The placement result setting could not be saved. Please, try again.'));
        }


        $this->set(compact('placementResultSetting'));
    }


    public function edit($id = null)
    {
        $placementResultSetting = $this->PlacementResultSettings->get($id, [
            'contain' => [],
        ]);
        if ($this->request->is(['patch', 'post', 'put'])) {
            $placementResultSetting = $this->PlacementResultSettings->patchEntity($placementResultSetting, $this->request->getData());
            if ($this->PlacementResultSettings->save($placementResultSetting)) {
                $this->Flash->success(__('The placement result setting has been saved.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The placement result setting could not be saved. Please, try again.'));
        }
        $this->set(compact('placementResultSetting'));
    }

    public function delete($id = null)
    {
        $this->request->allowMethod(['post', 'delete']);
        $placementResultSetting = $this->PlacementResultSettings->get($id);
        if ($this->PlacementResultSettings->delete($placementResultSetting)) {
            $this->Flash->success(__('The placement result setting has been deleted.'));
        } else {
            $this->Flash->error(__('The placement result setting could not be deleted. Please, try again.'));
        }

        return $this->redirect(['action' => 'index']);
    }
}
