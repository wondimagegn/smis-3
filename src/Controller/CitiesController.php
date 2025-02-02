<?php
class CitiesController extends AppController
{

	var $name = 'Cities';
	var $menuOptions = array(
		'parent' => 'countries',
		'exclude' => array('index'),
		'alias' => array(
			//'index' => 'View Cities',
			'add' => 'Add City',
		)
	);

	public $paginate = array();

	public function beforeFilter()
	{
		parent::beforeFilter();
		$this->Auth->allow('view');
	}
	
	public function beforeRender()
	{
		parent::beforeRender();
	}

	function index()
	{
		/* $this->City->recursive = 0;
		$this->set('cities', $this->paginate()); */

		$this->Paginator->settings =  array(
			'contain' => array('Region', 'Zone'), 
			'order' => array('City.region_id' => 'ASC', 'City.name' => 'ASC'),
			'limit' => 100,
			'maxLimit' => 100,
			'recursive'=> -1
		);

		$cities = array();

		try {
			$cities = $this->Paginator->paginate($this->modelClass);
			$this->set(compact('cities'));
		} catch (NotFoundException $e) {
			return $this->redirect(array('action' => 'index'));
		} catch (Exception $e) {
			return $this->redirect(array('action' => 'index'));
		}

		if (empty($cities)) {
			$this->Flash->info('No city is found in the system.');
		}
	}

	function view($id = null)
	{
		if (!$id) {
			$this->Flash->error(__('Invalid City ID'));
			return $this->redirect(array('action' => 'index'));
		}

		$this->set('city', $this->City->find('first', array('conditions' => array('City.id' => $id), 'contain' => array('Region', 'Zone'), 'recursive'=> -1)));
	}

	function add()
	{
		if (!empty($this->request->data) && isset($this->request->data['saveIt'])) {
			$this->City->create();
			if ($this->City->save($this->request->data)) {
				$this->Flash->success(__('The city has been saved'));
				return $this->redirect(array('action' => 'index'));
			} else {
				$this->Flash->error( __('The city could not be saved. Please, try again.'));
			}
		}

		$country_id_of_region = COUNTRY_ID_OF_ETHIOPIA;

		$region_id_set = (!empty($this->request->data['City']['region_id']) ? $this->request->data['City']['region_id'] : '');

		if (!empty($region_id_set)) {

			$country_id_of_region = $this->City->Region->field('country_id', array('Region.id' => $region_id_set));
				
			$countries = ClassRegistry::init('Country')->find('list', array('conditions' => array('Country.id' => $country_id_of_region)));
			
			$regions = $this->City->Region->find('list', array(
				'conditions' => array(
					'Region.id' => $region_id_set,
					'Region.country_id' => $country_id_of_region
				)
			));

			$regions = $this->City->Region->find('list', array('conditions' => array('Region.country_id' => $country_id_of_region)));

			$zones = $this->City->Zone->find('list', array('conditions' => array('Zone.region_id' => $region_id_set)));

		} else {
			$countries = ClassRegistry::init('Country')->find('list');
			$regions = $this->City->Region->find('list');
			$zones = $this->City->Zone->find('list');
		}
		
		$this->set(compact('regions', 'zones', 'countries'));
	}

	function edit($id = null)
	{
		if (!$id && empty($this->request->data)) {
			$this->Flash->error(__('Invalid City ID'));
			return $this->redirect(array('action' => 'index'));
		}

		if (!empty($this->request->data) && isset($this->request->data['saveIt'])) {
			if ($this->City->save($this->request->data)) {
				$this->Flash->success( __('The city has been saved'));
				return $this->redirect(array('action' => 'index'));
			} else {
				$this->Flash->error( __('The city could not be saved. Please, try again.'));
			}
		}

		if (empty($this->request->data)) {
			$this->request->data = $this->City->find('first', array('conditions' => array('City.id' => $id), 'contain' => array('Region', 'Zone'), 'recursive'=> -1));// $this->City->read(null, $id);
		}

		$country_id_of_region = COUNTRY_ID_OF_ETHIOPIA;
		$region_id_set = (!empty($this->request->data['City']['region_id']) ? $this->request->data['City']['region_id'] : '');

		if (!empty($region_id_set)) {

			$country_id_of_region = $this->City->Region->field('country_id', array('Region.id' => $region_id_set));
				
			$countries = ClassRegistry::init('Country')->find('list', array('conditions' => array('Country.id' => $country_id_of_region)));
			
			$regions = $this->City->Region->find('list', array(
				'conditions' => array(
					'Region.id' => $region_id_set,
					'Region.country_id' => $country_id_of_region
				)
			));

			$regions = $this->City->Region->find('list', array('conditions' => array('Region.country_id' => $country_id_of_region)));

			$zones = $this->City->Zone->find('list', array('conditions' => array('Zone.region_id' => $region_id_set)));

		} else {
			$countries = ClassRegistry::init('Country')->find('list');
			$regions = $this->City->Region->find('list');
			$zones = $this->City->Zone->find('list');
		}

		$this->set(compact('regions', 'zones', 'countries'));
	}

	function delete($id = null)
	{
		if (!$id) {
			$this->Flash->error(__('Invalid City ID'));
			return $this->redirect(array('action' => 'index'));
		}

		//check deletion is possible 
		if ($this->City->canItBeDeleted($id)) {
			if ($this->City->delete($id)) {
				$this->Flash->success(__('City deleted'));
				$this->redirect(array('action' => 'index'));
			}
		} else {
			$this->Flash->error(__('City was not deleted. It is related to student and contacts.'));
			return $this->redirect(array('action' => 'index'));
		}

		$this->Flash->error(__('City was not deleted.'));
		return $this->redirect(array('action' => 'index'));
	}
}
