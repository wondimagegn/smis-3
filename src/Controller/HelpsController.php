<?php
class HelpsController extends AppController
{
	public $name = 'Helps';
	public $helpers = array('Media.Media');
	public $menuOptions = array(
		'parent' => 'dashboard',
		'alias' => array(
			'index' => 'User Manuals',
		)
	);
	public $paginate = array();

	public function beforeFilter()
	{
		parent::beforeFilter();
		$this->Auth->allow(
			'search'
		);
	}

	function __init_search_helps()
	{
		if (!empty($this->request->data)) {
			$this->Session->write('search_data_helps', $this->request->data);
		} else if ($this->Session->check('search_data_helps')) {
			$this->request->data = $this->Session->read('search_data_helps');
			
		}
	}

	function __init_clear_session_filters($data = null)
	{

		if ($this->Session->check('search_data')) {
			$this->Session->delete('search_data');
		}

		if ($this->Session->check('search_data_helps')) {
			$this->Session->delete('search_data_helps');
		}
		//return $this->redirect(array('action' => 'index', $data));
	}

	function search()
	{
		$this->__init_search_helps();
		
		$url['action'] = 'index';

		if (isset($this->request->data) && !empty($this->request->data)) {
			foreach ($this->request->data as $k => $v) {
				if (!empty($v)) {
					foreach ($v as $kk => $vv) {
						if (!empty($vv) && is_array($vv)) {
							foreach ($vv as $kkk => $vvv){
								$url[$k . '.' . $kk . '.' . $kkk] = str_replace('/', '-', trim($vvv));
							}
						} else {
							$url[$k . '.' . $kk] = str_replace('/', '-', trim($vv));
						}
					}
				}
			}
		}

		return $this->redirect($url, null, true);
	}

	public function index()
	{

		$limit = 100;
		$page = 1;
		$sort = 'Help.version';
		$direction = 'desc';

		$options = array();

		if (!empty($this->passedArgs)) {

			////////////////////

			if (isset($this->passedArgs['page'])) {
				$page = $this->request->data['page'] = $this->passedArgs['page'];
			}

			if (isset($this->passedArgs['sort'])) {
				$sort = $this->request->data['sort'] = $this->passedArgs['sort'];
			}

			if (isset($this->passedArgs['direction'])) {
				$direction = $this->request->data['direction'] = $this->passedArgs['direction'];
			}

			////////////////////

			$this->__init_search_helps();

		}


		if (isset($data) && !empty($data)) {
			$this->request->data = $data;
			$this->__init_search_helps();
		}

		if (isset($this->request->data)) {
			unset($this->passedArgs);
			$this->__init_search_helps();
			$this->__init_clear_session_filters($this->request->data);
		}


		if (!empty($page) && !isset($this->request->data)) {
			$this->request->data['page'] = $page;
		}

		if ($this->Session->read('Auth.User')['role_id'] == ROLE_SYSADMIN) {
			$options['conditions'][] = array('Help.active' => array(0, 1));
		} else if ($this->Session->read('Auth.User')['role_id']) {
			$options['conditions'][] = array('Help.active' => 1);
			$options['conditions'][] = array('Help.target LIKE ' => '%' . $this->Session->read('Auth.User')['role_id']. '%');
		}


		if (!empty($options['conditions'])) {

			$this->Paginator->settings =  array(
				'conditions' => $options['conditions'],
				'contain' => array(
					'Attachment' => array(
						'order' => array('Attachment.created' => 'DESC')
					)
				),
				'order' => array($sort => $direction),
				'limit' => (!empty($limit) ? $limit : 100),
				'maxLimit' => (!empty($limit) ? $limit : 100),
				'recursive'=> -1,
				'page' => $page
			);
			
			try {
				$helps = $this->Paginator->paginate($this->modelClass);
			} catch (NotFoundException $e) {
				if (!empty($this->request->data)) {
					unset($this->request->data['page']);
					unset($this->request->data['sort']);
					unset($this->request->data['direction']);
				}
				unset($this->passedArgs);
				return $this->redirect(array('action' => 'index'));
			} catch (Exception $e) {
				if (!empty($this->request->data)) {
					unset($this->request->data['page']);
					unset($this->request->data['sort']);
					unset($this->request->data['direction']);
				}
				unset($this->passedArgs);
				return $this->redirect(array('action' => 'index'));
			}

		} else {
			$helps = array();
		}

		if (!empty($helps)) {
			foreach ($helps as $k => $v) {
				$admin_ids = explode(',', $v['Help']['target']);
				if (!in_array(Configure::read('User.role_id'), $admin_ids)) {
					unset($helps[$k]);
				}
			}
		}

		if (empty($helps) && !empty($options['conditions'])) {
			$this->Flash->info('No Help is found with the given search criteria.');
		}

		//$this->__init_search_helps();

		$this->set(compact('helps'));
	}

	public function add()
	{
		if (!empty($this->request->data)) {

			$this->request->data['Help']['target'] = implode(',', $this->request->data['Help']['target']);
			$this->Help->create();

			$this->request->data = $this->Help->preparedAttachment($this->request->data);

			if ($this->Help->saveAll($this->request->data, array('validate' => 'first'))) {
				$this->Flash->success( __('The help has been saved'));
				return $this->redirect(array('action' => 'index'));
			} else {
				$this->Flash->error(__('The help could not be saved. Please, try again.'));
				$targets = explode(',', $this->request->data['Help']['target']);
				$this->request->data['Help']['target'] = $targets;
			}

		}

		$roles = ClassRegistry::init('Role')->find('list');
		$this->set(compact('roles'));

	}

	public function edit($id = null)
	{
		if (!$id) {
			$this->Flash->error(__('Invalid help ID'));
			return $this->redirect(array('action' => 'index'));
		}

		$this->Help->id = $id;

		if (!$this->Help->exists()) {
			$this->Flash->error('ID not found, Invalid Help ID');
			return $this->redirect(array('action' => 'index'));
		}

		if (!empty($this->request->data)) {
			$this->request->data['Help']['target'] = implode(',', $this->request->data['Help']['target']);

			$this->request->data = $this->Help->preparedAttachment($this->request->data);

			debug($this->request->data);

			if ($this->Help->saveAll($this->request->data)) {
				$this->Flash->success(__('The help has been saved'));
				return $this->redirect(array('action' => 'index'));
			} else {
				$this->Flash->error(__('The help could not be saved. Please, try again.'));
			}
		}
		

		//if (empty($this->request->data)) {
			//$this->request->data = $this->Help->read(null, $id);
			$this->request->data =  $this->Help->find('first', array('conditions' => array('Help.id' => $id), 'contain' => array('Attachment' => array('order' => array('Attachment.created' => 'DESC')))));
			$this->request->data['Help']['target'] = explode(',', $this->request->data['Help']['target']);
		//}

		//debug($this->request->data);

		$roles = ClassRegistry::init('Role')->find('list');
		$this->set(compact('roles'));
	}

	function delete($id = null)
	{
		if (!$id) {
			$this->Flash->error(__('Invalid id for help'));
			return $this->redirect(array('action' => 'index'));
		}

		$this->Help->id = $id;

		if (!$this->Help->exists()) {
			$this->Flash->error('ID not found, Invalid Help ID');
			return $this->redirect(array('action' => 'index'));
		}

		if ($this->Help->delete($id)) {
			$this->Flash->success( __('Help deleted'));
			return $this->redirect(array('action' => 'index'));
		}

		$this->Flash->error( __('Help was not deleted, please try again.'));
		return $this->redirect(array('action' => 'index'));
	}
}
