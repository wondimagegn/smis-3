<?php
App::uses('AppController', 'Controller');

class SpecializationsController extends AppController
{

	/**
	 * Components
	 *
	 * @var array
	 */
	var $name = 'Specializations';
	var $menuOptions = array(
		'parent' => 'departments',
		'alias' => array(
			'index' => 'View all Specialization',
			'add' => 'Add New Specialization'
		)
	);
	public $paginate = array();

	public function beforeFilter()
	{
		parent::beforeFilter();
		$this->Auth->allow('add', 'edit', 'index', 'search');
	}

	/*
    *Generic search for returned items
    */
	function search()
	{
		// the page we will redirect to
		$url['action'] = 'index';

		// build a URL will all the search elements in it
		// domain.com/returned_items/index/Search.keywords:mykeyword/Search.tag_id:3
		foreach ($this->request->data as $k => $v) {
			foreach ($v as $kk => $vv) {
				$url[$k . '.' . $kk] = $vv;
			}
		}
		// redirect the user to the url
		return $this->redirect($url, null, true);
	}


	/**
	 * index method
	 *
	 * @return void
	 */
	public function index()
	{

		$this->paginate = array(
			'order' => array('Specialization.created' => ' DESC'),
			'maxLimit' => 100,
			'limit' => 100,

			'contain' => array(
				'Department'
			)
		);


		$this->Specialization->recursive = 0;

		// filter by program
		if (isset($this->passedArgs['Search.department_id'])) {
			$department_id = $this->passedArgs['Search.department_id'];
			if (!empty($department_id)) {
				$this->paginate['conditions'][]['Specialization.department_id'] = $department_id;
			}

			$this->request->data['Search']['department_id'] = $this->passedArgs['Search.department_id'];
		}

		$this->Paginator->settings = $this->paginate;

		if (!isset($this->Paginator->settings['conditions'])) {
			//TODO Filtering based on department or registrar assignment
			$specializations = $this->Paginator->paginate('Specialization');
		} else {

			$specializations = $this->Paginator->paginate('Specialization');
		}
		$colleges = $this->Specialization->Department->College->find('list');
		$this->set(compact('colleges', 'departments'));

		//$this->set('specializations', $this->Paginator->paginate());
		$this->set(compact('specializations', 'colleges'));
	}

	/**
	 * view method
	 *
	 * @throws NotFoundException
	 * @param string $id
	 * @return void
	 */
	public function view($id = null)
	{
		if (!$this->Specialization->exists($id)) {
			throw new NotFoundException(__('Invalid specialization'));
		}
		$options = array('conditions' => array('Specialization.' . $this->Specialization->primaryKey => $id));
		$this->set('specialization', $this->Specialization->find('first', $options));
	}

	/**
	 * add method
	 *
	 * @return void
	 */
	public function add()
	{
		if ($this->request->is('post')) {
			$this->Specialization->create();
			if ($this->Specialization->save($this->request->data)) {

				$this->Session->setFlash(
					'<span></span>' . __('The specialization has been saved.'),
					'default',
					array('class' => 'success-box success-message')
				);

				return $this->redirect(array('action' => 'index'));
			} else {

				$this->Session->setFlash(
					'<span></span>' . __('The specialization could not be saved. Please, try again.'),
					'default',
					array('class' => 'error-box error-message')
				);
			}
		}
		//$departments = $this->Specialization->Department->find('list');
		$colleges = $this->Specialization->Department->College->find('list');
		$this->set(compact('colleges', 'departments'));
	}

	/**
	 * edit method
	 *
	 * @throws NotFoundException
	 * @param string $id
	 * @return void
	 */
	public function edit($id = null)
	{
		if (!$this->Specialization->exists($id)) {
			throw new NotFoundException(__('Invalid specialization'));
		}
		if ($this->request->is(array('post', 'put'))) {
			if ($this->Specialization->save($this->request->data)) {

				$this->Session->setFlash(
					'<span></span>' . __('The specialization has been saved.'),
					'default',
					array('class' => 'success-box success-message')
				);

				return $this->redirect(array('action' => 'index'));
			} else {
				$this->Session->setFlash(
					'<span></span>' . __('The specialization could not be saved. Please, try again.'),
					'default',
					array('class' => 'error-box error-message')
				);
			}
		} else {
			$options = array('conditions' => array('Specialization.' . $this->Specialization->primaryKey => $id));
			$this->request->data = $this->Specialization->find('first', $options);
		}

		$colleges = $this->Specialization->Department->College->find('list');
		$departments = $this->Specialization->Department->find(
			'list',
			array('conditions' => array('Department.id' =>
			$this->request->data['Specialization']['department_id']))
		);
		$this->set(compact('colleges', 'departments'));
	}


	public function delete($id = null)
	{
		$this->Specialization->id = $id;
		if (!$this->Specialization->exists()) {
			throw new NotFoundException(__('Invalid specialization'));
		}
		$this->request->allowMethod('post', 'delete');
		if ($this->Specialization->delete()) {

			$this->Session->setFlash(
				'<span></span>' . __('The specialization has been deleted.'),
				'default',
				array('class' => 'success-box success-message')
			);
		} else {

			$this->Session->setFlash(
				'<span></span>' . __('The specialization could not be deleted. Please, try again.'),
				'default',
				array('class' => 'error-box error-message')
			);
		}
		return $this->redirect(array('action' => 'index'));
	}
}