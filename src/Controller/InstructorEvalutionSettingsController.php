<?php
App::uses('AppController', 'Controller');
class InstructorEvalutionSettingsController extends AppController
{
	var $menuOptions = array(
		'parent' => 'evalution',
		'exclude' => array('index'),
		'alias' => array(
			'view_ss' => 'Instructor Evaluation Settings',
		)
	);

	public $components = array('AcademicYear');

	public function beforeFilter()
	{
		parent::beforeFilter();
		//$this->Auth->Allow();
	}

	function beforeRender()
	{
		$acyear_array_data = $this->AcademicYear->acyear_array();
		//To diplay current academic year as default in drop down list
		$defaultacademicyear = $this->AcademicYear->current_academicyear();
		$this->set(compact('acyear_array_data', 'defaultacademicyear'));
		unset($this->request->data['User']['password']);
	}

	public function index()
	{
		return $this->redirect(array('action' => 'view_ss'));
	}

	function view_ss()
	{
		$this->set('instructorEvalutionSetting', $this->InstructorEvalutionSetting->find('first', array('order' => array('InstructorEvalutionSetting.academic_year' => 'DESC'))));
	}

	public function edit()
	{
		if (!empty($this->request->data)) {
			if ($this->InstructorEvalutionSetting->save($this->request->data)) {
				$this->Flash->success( __('Instructor Evaluation setting has been updated.'));
				return $this->redirect(array('action' => 'view_ss'));
			} else {
				$this->Flash->error(__('Instructor evaluation setting could not be updated. Please, try again.'));
			}
		}

		if (empty($this->request->data)) {
			$this->request->data = $this->InstructorEvalutionSetting->find('first', array('order' => array('InstructorEvalutionSetting.academic_year DESC')));
		}
	}

}
