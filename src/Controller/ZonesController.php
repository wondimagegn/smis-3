<?php

namespace App\Controller;

use App\Controller\AppController;

use Cake\Event\Event;
use Cake\ORM\TableRegistry;
use Cake\Core\Configure;
class ZonesController extends AppController
{

    public $name = 'Zones';
    public $components = array('RequestHandler');
    //var $helpers = array('Ajax','Javascript');
    public $menuOptions = array(
        'parent' => 'countries',
        'exclude' => array('index'),
        'alias' => array(
            //'index' => 'List Zones',
            'add' => 'Add Zone',
        )
    );
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


    public function beforeRender(Event $event)
    {

        parent::beforeRender($event);
    }

    public function index()
    {

        $this->Paginator->settings = array(
            'contain' => array('Region'),
            'order' => array('Zone.region_id' => 'ASC', 'Zone.name' => 'ASC'),
            'limit' => 100,
            'maxLimit' => 100,
            'recursive' => -1
        );

        $zones = array();

        try {
            $zones = $this->Paginator->paginate($this->modelClass);
            $this->set(compact('zones'));
        } catch (NotFoundException $e) {
            return $this->redirect(array('action' => 'index'));
        } catch (Exception $e) {
            return $this->redirect(array('action' => 'index'));
        }

        if (empty($zones)) {
            $this->Flash->info('No zone is found in the system.');
        }
    }

    public function add()
    {

        if (!empty($this->request->data) && isset($this->request->data['saveIt'])) {
            $this->Zone->create();
            if ($this->Zone->save($this->request->data)) {
                $this->Flash->success(__('Zone saved'));
                $this->redirect(array('action' => 'index'));
            } else {
                $this->Flash->error(__('Zone could not be saved. Please, try again.'));
            }
        }

        $regions = $this->Zone->Region->find('list', array('conditions' => array('Region.active' => 1)));
        $this->set(compact('regions'));
    }

    public function edit($id = null)
    {

        if (!$id && empty($this->request->data)) {
            $this->Flash->error(__('Invalid Region ID.'));
            $this->redirect(array('action' => 'index'));
        }

        if (!empty($this->request->data) && isset($this->request->data['saveIt'])) {
            if ($this->Zone->save($this->request->data)) {
                $this->Flash->success(__('Zone updated successfully.'));
                $this->redirect(array('action' => 'index'));
            } else {
                $this->Flash->error(__('Zone could not be saved. Please, try again.'));
            }
        }

        if (empty($this->request->data)) {
            //$this->request->data = $this->Zone->read(null, $id);
            $this->request->data = $this->Zone->find(
                'first',
                array('conditions' => array('Zone.id' => $id), 'contain' => array('Region'), 'recursive' => -1)
            );
        } else {
            $zone = $this->Zone->find(
                'first',
                array('conditions' => array('Zone.id' => $id), 'contain' => array('Region'), 'recursive' => -1)
            );
        }

        //$regions = $this->Zone->Region->find('list', array('conditions' => array('Region.id' => (isset($this->request->data['Region']['id']) ? $this->request->data['Region']['id'] : $zone['Region']['id']), /* 'Region.active' => 1 */)));

        if (!empty($this->request->data['Region']['id']) || (isset($zone['Region']['id']) && !empty($zone['Region']['id']))) {
            $region_country_id = $this->Zone->Region->field(
                'country_id',
                array('Region.id' => (isset($this->request->data['Region']['id']) ? $this->request->data['Region']['id'] : $zone['Region']['id']))
            );

            if ($region_country_id) {
                $regions = $this->Zone->Region->find(
                    'list',
                    array('conditions' => array('Region.country_id' => $region_country_id /*, 'Region.active' => 1 */))
                );
            } else {
                $regions = $this->Zone->Region->find('list');
            }
        } else {
            $regions = $this->Zone->Region->find('list');
        }

        $this->set(compact('regions'));
    }

    public function delete($id = null)
    {

        if (!$id) {
            $this->Flash->error(__('Invalid Zone ID'));
            return $this->redirect(array('action' => 'index'));
        }

        //check deletion is possible
        if ($this->Zone->canItBeDeleted($id)) {
            if ($this->Zone->delete($id)) {
                $this->Flash->success(__('Zone deleted.'));
                $this->redirect(array('action' => 'index'));
            }
        } else {
            $this->Flash->error(__('Zone was not deleted. It is related to student and contacts.'));
        }

        $this->Flash->error(__('Region was not deleted'));
        return $this->redirect(array('action' => 'index'));
    }
}
