<?php
App::uses('AppController', 'Controller');

class PlacementParticipatingStudentsController extends AppController
{
	var $name = 'PlacementParticipatingStudents';
	public $menuOptions = array(
		'parent' => 'placement',
		'alias' => array(
			//'index' => 'List Placement Participant Student',
		),
		'exclude' => array(
			'delete_ajax',
			'index'
		),
	);

	var $components = array('EthiopicDateTime', 'AcademicYear');
	public function beforeRender()
	{
		//$acyear_array_data = $this->AcademicYear->academicYearInArray(date('Y') - 1, date('Y') - 1);

		////////////////////////////// BLOCK: DONT REMOVE ANY VARIABLE /////////////////////////////////////

		$defaultacademicyear = $this->AcademicYear->current_academicyear();

		if (is_numeric(ACY_BACK_FOR_PLACEMENT) && ACY_BACK_FOR_PLACEMENT) {
			$acyear_array_data = $this->AcademicYear->academicYearInArray(((explode('/', $defaultacademicyear)[0]) - ACY_BACK_FOR_PLACEMENT), (explode('/', $defaultacademicyear)[0]));
		} else {
			$acyear_array_data[$defaultacademicyear] = $defaultacademicyear;
		}

		$this->set(compact('acyear_array_data', 'defaultacademicyear'));

		//////////////////////////////////// END BLOCK ///////////////////////////////////////////////////
	}

	public function beforeFilter()
	{
		parent::beforeFilter();
		//$this->Auth->Allow('delete_ajax');
	}


	public function delete_ajax($id = null)
	{
		$this->autoRender = false;
		$this->layout = 'ajax';

		//check if placement is already run
		$placementParticpatingStu = $this->PlacementParticipatingStudent->find('first', array('conditions' => array('PlacementParticipatingStudent.id' => $id), 'recursive' => -1));

		$isPlaced = $this->PlacementParticipatingStudent->find('count', array(
			'conditions' => array(
				'PlacementParticipatingStudent.program_id' => $placementParticpatingStu['PlacementParticipatingStudent']['program_id'],
				'PlacementParticipatingStudent.program_type_id' => $placementParticpatingStu['PlacementParticipatingStudent']['program_type_id'],
				'PlacementParticipatingStudent.applied_for' => $placementParticpatingStu['PlacementParticipatingStudent']['applied_for'],
				'PlacementParticipatingStudent.round' => $placementParticpatingStu['PlacementParticipatingStudent']['round'],
				'PlacementParticipatingStudent.academic_year' => $placementParticpatingStu['PlacementParticipatingStudent']['academic_year'],
				'PlacementParticipatingStudent.placement_round_participant_id is not null'
			),
			'recursive' => -1
		));

		if ($isPlaced == 0) {
			$this->PlacementParticipatingStudent->id = $id;
			$this->request->allowMethod('post', 'delete');
			if ($this->PlacementParticipatingStudent->delete()) {
			}
		}
	}
}
