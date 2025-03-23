<?php

namespace App\Controller;

use Cake\Event\Event;

class RegionsController extends AppController
{

    public $name = 'Regions';
    public $menuOptions = array(
        'parent' => 'countries',
        'exclude' => array('index'),
        'alias' => array(
            //'index' => 'List Regions',
            'add' => 'Add Region',
        )
    );

    public $paginate = array();

    public function initialize()
    {

        parent::initialize();
        $this->loadComponent('AcademicYear');
        $this->loadComponent('RequestHandler');
        $this->loadComponent('Paginator');
    }

    public function beforeFilter(Event $event)
    {

        parent::beforeFilter($event);
        $this->Auth->allowedActions = array('getRegions');
    }

    public function index()
    {

        // $this->Region->recursive = 0;
        // $this->set('regions', $this->paginate());

        $this->Paginator->settings = array(
            'contain' => array('Country'),
            'order' => array('Region.country_id' => 'ASC', 'Region.name' => 'ASC'),
            'limit' => 100,
            'maxLimit' => 100,
            'recursive' => -1
        );

        $regions = array();

        try {
            $regions = $this->Paginator->paginate($this->modelClass);
            $this->set(compact('regions'));
        } catch (NotFoundException $e) {
            return $this->redirect(array('action' => 'index'));
        } catch (Exception $e) {
            return $this->redirect(array('action' => 'index'));
        }

        if (empty($regions)) {
            $this->Flash->info('No region is found in the system.');
        }
    }

    public function view($id = null)
    {

        if (!$id) {
            $this->Flash->error(__('Invalid region'));
            return $this->redirect(array('action' => 'index'));
        }
        $this->set('region', $this->Region->read(null, $id));
    }

    public function add()
    {

        if (!empty($this->request->data)) {
            $this->Region->create();
            if ($this->Region->save($this->request->data)) {
                $this->Flash->success(__('Region saved'));
                $this->redirect(array('action' => 'index'));
            } else {
                $this->Flash->error(__('The region  could not be saved. Please, try again.'));
            }
        }
        $countries = $this->Region->Country->find('list');
        $this->set(compact('countries'));
    }

    public function edit($id = null)
    {

        if (!$id && empty($this->request->data)) {
            $this->Flash->error(__('Invalid Region ID'));
            $this->redirect(array('action' => 'index'));
        }

        if (!empty($this->request->data)) {
            if ($this->Region->save($this->request->data)) {
                $this->Flash->success(__('Region updated successfully'));
                $this->redirect(array('action' => 'index'));
            } else {
                $this->Flash->error(__('The region could not be saved. Please, try again.'));
            }
        }

        if (empty($this->request->data)) {
            $this->request->data = $this->Region->find(
                'first',
                array('conditions' => array('Region.id' => $id), 'contain' => array('Country'), 'recursive' => -1)
            ); //$this->Region->read(null, $id);
        }

        $countries = $this->Region->Country->find('list');
        $this->set(compact('countries'));
    }

    public function delete($id = null)
    {

        if (!$id) {
            $this->Flash->error(__('Invalid id for region'));
            return $this->redirect(array('action' => 'index'));
        }

        //check deletion is possible
        if ($this->Region->canItBeDeleted($id)) {
            if ($this->Region->delete($id)) {
                $this->Flash->success(__('Region deleted'));
                $this->redirect(array('action' => 'index'));
            }
        } else {
            $this->Flash->error(__('Region was not deleted. It is related to student and contacts.'));
        }

        $this->Flash->error(__('Region was not deleted'));
        return $this->redirect(array('action' => 'index'));
    }
}
