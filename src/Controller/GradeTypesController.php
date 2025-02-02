<?php
class GradeTypesController extends AppController
{
	var $name = 'GradeTypes';

	var $menuOptions = array(
		'controllerButton' => false,
		'exclude' => array('*'),
	);

	// var $components =array('Security');

	/**
	 * After successful test , this before filter will be applied to all controller
	 * in our application to make our application more secure, protecting against form modification,
	 * CSRF attacks
	*/

	function beforeFilter()
	{
		parent::beforeFilter();
		// The Security requireAuth method tells CakePHP to validate any
		// form submission with the authorization key.
		// This validation will only happen if the form was submitted via POST.
		
		/*
		$this->Security->requireAuth('edit','add','delete');
        $this->Security->disabledFields = array('Grade.grade','Grade.point_value','Grade.pass_grade');
		$this->Security->blackHoleCallback='invalid';
		$this->Auth->Allow('invalid');
		*/

		//security against (primary key) injection, xss or other things
	}

	function index()
	{
		$this->GradeType->recursive = 0;
		$this->paginate = array(
			'contain' => array(
				'Grade' => array(
					'fields' => array('id', 'grade', 'point_value', 'pass_grade', 'allow_repetition')
				)
			), 
			'limit' => 100, 'maxLimit' => 100,
		);
		$this->set('gradeTypes', $this->paginate());
	}

	function view($id = null)
	{
		if (!$id) {
			$this->Flash->error('Invalid grade type');
			return $this->redirect(array('action' => 'index'));
		}

		$this->GradeType->id = $id;

		if (!$this->GradeType->exists()) {
			$this->Flash->error('Invalid grade type');
			return $this->redirect(array('action' => 'index'));
		}

		$gradeType = $this->GradeType->find('first', array(
			'conditions' => array('GradeType.id' => $id),
			'contain' => array(
				/* 'Course' => array(
					'fields' => array('id', 'course_title', 'course_code', 'credit', 'course_detail_hours'), 
					'Curriculum' => array(
						'Department' => array('id', 'name'), 
						'fields' => array('id', 'name')
					),
					'CourseCategory' => array('id', 'name')
				),  */
				'Grade'/*  => array(
					'fields' => array('id', 'grade', 'point_value', 'pass_grade', 'allow_repetition')
				) */
			), 
			'recursive' => -1
		));

		$this->set('gradeType', $gradeType);
	}

	function add()
	{
		if (!empty($this->request->data)) {

			//debug($this->request->data);
			foreach ($this->request->data['Grade'] as $key => $grade) {
				debug(trim($grade['grade']));
				$this->request->data['Grade'][$key]['grade'] = trim($grade['grade']);
				if($grade['pass_grade'] == 'on'){
					$this->request->data['Grade'][$key]['pass_grade'] = 1;
				}
				if($grade['allow_repetition'] == 'on'){
					$this->request->data['Grade'][$key]['allow_repetition'] = 1;
				}
			}
			//debug($this->request->data);

			$this->GradeType->create();

			//$this->set($this->request->data);
			//$this->request->data = $this->GradeType->unset_empty_rows($this->request->data);

			if ($this->GradeType->Grade->checkGradeIsUnique($this->request->data)) {
				if ($this->GradeType->saveAll($this->request->data, array('validate' => 'first'))) {
					$this->Flash->success('The grade type has been saved');
					$this->redirect(array('action' => 'index'));
				} else {
					$this->Flash->error('The grade type could not be saved. Please, try again.');
				}
			} else {
				$error = $this->GradeType->Grade->invalidFields();
				if (isset($error['checkGradeIsUnique'])) {
					$this->Flash->error($error['checkGradeIsUnique'][0]);
				}
			}
		}
	}

	function edit($id = null)
	{
		if (!$id && empty($this->request->data)) {
			$this->Flash->error('Invalid grade type');
			return $this->redirect(array('action' => 'index'));
		}

		$this->GradeType->id = $id;

		if (!$this->GradeType->exists()) {
			$this->Flash->error('Invalid grade type');
			return $this->redirect(array('action' => 'index'));
		}

		if (!empty($this->request->data)) {

			//debug($this->request->data);
			foreach ($this->request->data['Grade'] as $key => $grade) {
				debug(trim($grade['grade']));
				$this->request->data['Grade'][$key]['grade'] = trim($grade['grade']);
				if($grade['pass_grade'] == 'on'){
					$this->request->data['Grade'][$key]['pass_grade'] = 1;
				}
				if($grade['allow_repetition'] == 'on'){
					$this->request->data['Grade'][$key]['allow_repetition'] = 1;
				}
			}
			//debug($this->request->data);
			
			if ($this->GradeType->Grade->checkGradeIsUnique($this->request->data)) {
				if ($this->GradeType->saveAll($this->request->data, array('validate' => 'first'))) {
					$this->Flash->success('The grade type has been saved');
					$this->redirect(array('action' => 'index'));
				} else {
					$this->Flash->error('The grade type could not be saved. Please, try again.');
				}
			} else {
				$error = $this->GradeType->Grade->invalidFields();
				if (isset($error['checkGradeIsUnique'])) {
					$this->Flash->error($error['checkGradeIsUnique'][0]);
				}
			}
		}

		if (empty($this->request->data)) {
			$this->request->data = $this->GradeType->read(null, $id);
			$this->request->data = $this->GradeType->find('first', array('conditions' => array('GradeType.id' => $id), 'contain' => array('Grade')));
			$check_not_involved_in_grade_computing = $this->GradeType->is_grade_type_attached_to_course($id);
			debug($check_not_involved_in_grade_computing);
		}
		$this->set(compact('check_not_involved_in_grade_computing'));
	}

	function delete($id = null, $action_controller_id = null)
	{
		if (!empty($action_controller_id)) {
			$grade_type = explode('~', $action_controller_id);
		}

		$this->GradeType->id = $id;

		if (!$this->GradeType->exists()) {
			$this->Flash->error('Invalid grade type');
			if (!empty($grade_type[0]) && !empty($grade_type[1]) && !empty($grade_type[2])) {
				$this->redirect(array('controller' => $grade_type[1], 'action' => $grade_type[0], $grade_type[2]));
			}
			return $this->redirect(array('action' => 'index'));
		}

		// TODO: CHeck grade type is not involved for any grade computing. if true, call function in here to return true or false to allow deletion.

		$check_not_involved_in_grade_computing = $this->GradeType->is_grade_type_attached_to_course($id);

		if ($check_not_involved_in_grade_computing) {
			if ($this->GradeType->delete($id)) {
				$this->Flash->success('Grade type deleted.');
				if (!empty($grade_type[0]) && !empty($grade_type[1]) && !empty($grade_type[2])) {
					$this->redirect(array('controller' => $grade_type[1], 'action' => $grade_type[0], $grade_type[2]));
				}
				$this->redirect(array('action' => 'index'));
			}
		} else {
			$this->Flash->error('You can not delet this grade type, it is attached to courses.');
			$this->redirect(array('action' => 'index'));
		}
	}

	/**
	 * The authentication key is regenerated every time a form is evaluated with requireAuth.
	 * This means that if a user submits a form with a key that has already been used, 
	 * the form submission will be considered invalid.There are several cases in 
	 * which this could occur, including but not limited to using multiple browser windows, 
	 * using the Back button to return to a previous page, browser caching, proxy caching, 
	 * and more. While you may be tempted to write off these problems as user error, 
	 * you should resist the temptation and plan on handling invalid form submissions gracefully.
	*/

	function invalid()
	{
		//$this->cakeError('youSuck');
	}
}
