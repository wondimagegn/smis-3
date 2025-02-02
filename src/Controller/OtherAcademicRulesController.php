<?php
App::uses('AppController', 'Controller');
/**
 * OtherAcademicRules Controller
 *
 * @property OtherAcademicRule $OtherAcademicRule
 * @property PaginatorComponent $Paginator
 * @property FlashComponent $Flash
 */
class OtherAcademicRulesController extends AppController {

/**
 * Components
 *
 * @var array
 */
	public $components = array('Paginator', 'Flash');

/**
 * index method
 *
 * @return void
 */
	public function index() {
		$this->OtherAcademicRule->recursive = 0;
		$this->set('otherAcademicRules', $this->Paginator->paginate());
	}

/**
 * view method
 *
 * @throws NotFoundException
 * @param string $id
 * @return void
 */
	public function view($id = null) {
		if (!$this->OtherAcademicRule->exists($id)) {
			throw new NotFoundException(__('Invalid other academic rule'));
		}
		$options = array('conditions' => array('OtherAcademicRule.' . $this->OtherAcademicRule->primaryKey => $id));
		$this->set('otherAcademicRule', $this->OtherAcademicRule->find('first', $options));
	}

/**
 * add method
 *
 * @return void
 */
	public function add() {
		if ($this->request->is('post')) {
			$this->OtherAcademicRule->create();
			if ($this->OtherAcademicRule->save($this->request->data)) {
				return $this->flash(__('The other academic rule has been saved.'), array('action' => 'index'));
			}
		}
	}

/**
 * edit method
 *
 * @throws NotFoundException
 * @param string $id
 * @return void
 */
	public function edit($id = null) {
		if (!$this->OtherAcademicRule->exists($id)) {
			throw new NotFoundException(__('Invalid other academic rule'));
		}
		if ($this->request->is(array('post', 'put'))) {
			if ($this->OtherAcademicRule->save($this->request->data)) {
				return $this->flash(__('The other academic rule has been saved.'), array('action' => 'index'));
			}
		} else {
			$options = array('conditions' => array('OtherAcademicRule.' . $this->OtherAcademicRule->primaryKey => $id));
			$this->request->data = $this->OtherAcademicRule->find('first', $options);
		}
	}

/**
 * delete method
 *
 * @throws NotFoundException
 * @param string $id
 * @return void
 */
	public function delete($id = null) {
		$this->OtherAcademicRule->id = $id;
		if (!$this->OtherAcademicRule->exists()) {
			throw new NotFoundException(__('Invalid other academic rule'));
		}
		$this->request->allowMethod('post', 'delete');
		if ($this->OtherAcademicRule->delete()) {
			return $this->flash(__('The other academic rule has been deleted.'), array('action' => 'index'));
		} else {
			return $this->flash(__('The other academic rule could not be deleted. Please, try again.'), array('action' => 'index'));
		}
	}
}
