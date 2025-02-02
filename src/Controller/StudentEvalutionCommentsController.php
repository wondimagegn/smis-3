<?php
App::uses('AppController', 'Controller');
/**
 * StudentEvalutionComments Controller
 *
 * @property StudentEvalutionComment $StudentEvalutionComment
 * @property PaginatorComponent $Paginator
 */
class StudentEvalutionCommentsController extends AppController {

/**
 * Components
 *
 * @var array
 */
	public $components = array('Paginator');


/**
 * view method
 *
 * @throws NotFoundException
 * @param string $id
 * @return void
 */
	public function view($id = null) {
		if (!$this->StudentEvalutionComment->exists($id)) {
			throw new NotFoundException(__('Invalid student evalution comment'));
		}
		$options = array('conditions' => array('StudentEvalutionComment.' . $this->StudentEvalutionComment->primaryKey => $id));
		$this->set('studentEvalutionComment', $this->StudentEvalutionComment->find('first', $options));
	}

/**
 * add method
 *
 * @return void
 */
	public function add() {
		if ($this->request->is('post')) {
			$this->StudentEvalutionComment->create();
			if ($this->StudentEvalutionComment->save($this->request->data)) {
				$this->Flash->success(__('The student evalution comment has been saved.'));
				return $this->redirect(array('action' => 'index'));
			} else {
				$this->Flash->error(__('The student evalution comment could not be saved. Please, try again.'));
			}
		}
		
		
		$instructorEvalutionQuestions = $this->StudentEvalutionComment->InstructorEvalutionQuestion->find('list',array('conditions'=>array(
			'InstructorEvalutionQuestion.type'=>'open-ended',
			'InstructorEvalutionQuestion.for'=>'student',
			'InstructorEvalutionQuestion.active'=>1

			)));
			
		//$students = $this->StudentEvalutionComment->Student->find('list');
		//$publishedCourses = $this->StudentEvalutionComment->PublishedCourse->find('list');
		$this->set(compact('instructorEvalutionQuestions', 'students', 'publishedCourses'));
	}

/**
 * edit method
 *
 * @throws NotFoundException
 * @param string $id
 * @return void
 */
	public function edit($id = null) {
		if (!$this->StudentEvalutionComment->exists($id)) {
			throw new NotFoundException(__('Invalid student evalution comment'));
		}
		if ($this->request->is(array('post', 'put'))) {
			if ($this->StudentEvalutionComment->save($this->request->data)) {
				$this->Flash->success(__('The student evalution comment has been saved.'));
				return $this->redirect(array('action' => 'index'));
			} else {
				$this->Flash->error(__('The student evalution comment could not be saved. Please, try again.'));
			}
		} else {
			$options = array('conditions' => array('StudentEvalutionComment.' . $this->StudentEvalutionComment->primaryKey => $id));
			$this->request->data = $this->StudentEvalutionComment->find('first', $options);
		}
		$instructorEvalutionQuestions = $this->StudentEvalutionComment->InstructorEvalutionQuestion->find('list');
		$students = $this->StudentEvalutionComment->Student->find('list');
		$publishedCourses = $this->StudentEvalutionComment->PublishedCourse->find('list');
		$this->set(compact('instructorEvalutionQuestions', 'students', 'publishedCourses'));
	}

/**
 * delete method
 *
 * @throws NotFoundException
 * @param string $id
 * @return void
 */
	public function delete($id = null) {
		$this->StudentEvalutionComment->id = $id;
		if (!$this->StudentEvalutionComment->exists()) {
			throw new NotFoundException(__('Invalid student evalution comment'));
		}
		$this->request->allowMethod('post', 'delete');
		if ($this->StudentEvalutionComment->delete()) {
			$this->Flash->success(__('The student evalution comment has been deleted.'));
		} else {
			$this->Flash->error(__('The student evalution comment could not be deleted. Please, try again.'));
		}
		return $this->redirect(array('action' => 'index'));
	}
}
