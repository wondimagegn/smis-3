<?php
class WoredasController extends AppController
{
	public $name = 'Woredas';
	public $components = array('RequestHandler');
	//var $helpers = array('Ajax','Javascript');
	public $menuOptions = array(
		'parent' => 'countries',
		'exclude' => array('index'),
		'alias' => array(
			//'index' => 'List Woredas',
			'add' => 'Add Woreda',
		)
	);

	public $paginate = array();

	public function beforeFilter()
	{
		parent::beforeFilter();
		//$this->Auth->allowedActions = array('index', 'add', 'edit', 'delete');
	}

	public function beforeRender()
	{
		parent::beforeRender();
	}

	public function index()
	{
		$this->Paginator->settings =  array(
			'contain' => array('Zone'), 
			'order' => array('Woreda.zone_id' => 'ASC', 'Woreda.name' => 'ASC'),
			'limit' => 100,
			'maxLimit' => 100,
			'recursive'=> -1
		);

		$woredas = array();

		try {
			$woredas = $this->Paginator->paginate($this->modelClass);
			$this->set(compact('woredas'));
		} catch (NotFoundException $e) {
			return $this->redirect(array('action' => 'index'));
		} catch (Exception $e) {
			return $this->redirect(array('action' => 'index'));
		}

		if (empty($woredas)) {
			$this->Flash->info('No woreda is found in the system.');
		}
	}

	public function add()
	{
		if (!empty($this->request->data) && isset($this->request->data['saveIt'])) {
			$this->Woreda->create();
			if ($this->Woreda->save($this->request->data)) {
				$this->Flash->success(__('Woreda saved'));
				$this->redirect(array('action' => 'index'));
			} else {
				$this->Flash->error( __('Woreda could not be saved. Please, try again.'));
			}
		}

		$zones = $this->Woreda->Zone->find('list', array('conditions' => array('Zone.active' => 1)));
		$this->set(compact('zones'));
	}

	public function edit($id = null)
	{
		if (!$id && empty($this->request->data)) {
			$this->Flash->error(__('Invalid Woreda ID.'));
			$this->redirect(array('action' => 'index'));
		}

		if (!empty($this->request->data) && isset($this->request->data['saveIt'])) {
			if ($this->Woreda->save($this->request->data)) {
				$this->Flash->success( __('Woreda updated successfully.'));
				$this->redirect(array('action' => 'index'));
			} else {
				$this->Flash->error(__('Woreda could not be saved. Please, try again.'));
			}
		}

		if (empty($this->request->data)) {
			//$this->request->data = $this->Woreda->read(null, $id);
			$this->request->data = $this->Woreda->find('first', array('conditions' => array('Woreda.id' => $id), 'contain' => array('Zone'), 'recursive'=> -1));
		} else {
			$woreda = $this->Woreda->find('first', array('conditions' => array('Woreda.id' => $id), 'contain' => array('Zone'), 'recursive'=> -1));
		}
		
		$zones = $this->Woreda->Zone->find('list', array('conditions' => array('Zone.id' => (isset($this->request->data['Woreda']['zone_id']) ? $this->request->data['Woreda']['zone_id'] : $woreda['Woreda']['zone_id']), /* 'Zone.active' => 1 */)));
		$this->set(compact('zones'));
	}

	public function delete($id = null)
	{
		if (!$id) {
			$this->Flash->error(__('Invalid Woreda ID'));
			return $this->redirect(array('action' => 'index'));
		}

		//check deletion is possible 
		if ($this->Woreda->canItBeDeleted($id)) {
			if ($this->Woreda->delete($id)) {
				$this->Flash->success(__('Woreda deleted.'));
				$this->redirect(array('action' => 'index'));
			}
		} else {
			$this->Flash->error(__('Woreda was not deleted. It is related to student and contacts.'));
		}

		$this->Flash->error( __('Woreda was not deleted'));
		return $this->redirect(array('action' => 'index'));
	}
}
