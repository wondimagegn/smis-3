<?php
class UniversitiesController extends AppController {

	var $name = 'Universities';
	var $components =array('AcademicYear');
    var $helpers = array('Media.Media');
    var $menuOptions = array(
            'parent' => 'mainDatas',
             'alias' => array(
                    'index'=>'View name',
                    'add'=>'Add Name',
                )
    );
    
   function beforeRender() {
		$acyear_array_data = $this->AcademicYear->acyear_array();
		//To diplay current academic year as default in drop down list
		$defaultacademicyear=$this->AcademicYear->current_academicyear();
		$this->set(compact('acyear_array_data','defaultacademicyear'));
		unset($this->request->data['User']['password']);
	}
	
	function index() {
		$this->University->recursive = 0;
		$this->set('universities', $this->paginate());
	}

	function view($id = null) {
		if (!$id) {
			$this->Session->setFlash('<span></span>'.__('Invalid university'),
			'default',array('class'=>'error-box error-message'));
			return $this->redirect(array('action' => 'index'));
		}
		$this->set('university', $this->University->read(null, $id));
	}

	function add() {
		 
		 if (!empty($this->request->data)) {
			$this->University->create();
			       $this->request->data = $this->University->attach_temp_photo($this->request->data);
			        if ($this->University->saveAll($this->request->data,array('validate'=>'first'))) {
				        $this->Session->setFlash('<span></span>'.__('The university has been saved'),
				        'default',array('class'=>'success-box success-message'));
				        $this->redirect(array('action' => 'index'));
			        } else {
				        $this->Session->setFlash('<span></span>'.__('The university could not be saved. Please, try again.'),'default',array('class'=>'error-box error-message'));
			        }
		 }
		$years = array();
		for($i = Configure::read('Calendar.universityEstablishement'); $i <= date('Y')+1; $i++) {
			$years[$i] = $i;
		}
		$this->set(compact('years'));
	}

	function edit($id = null) {
	
		if (!$id && empty($this->request->data)) {
			$this->Session->setFlash('<span></span>'.__('Invalid university'),
			'default',array('class'=>'error-box error-message'));
			return $this->redirect(array('action' => 'index'));
		}
		if (!empty($this->request->data)) {
			
			$this->request->data = $this->University->attach_temp_photo($this->request->data);
		   
		
			if ($this->University->saveAll($this->request->data,array('validate'=>'first'))) {
				$this->Session->setFlash('<span></span>'.__('The university has been saved'),
				'default',array('class'=>'success-box success-message'));
				return $this->redirect(array('action' => 'index'));
			} else {
				$this->Session->setFlash('<span></span>'.__('The university could not be saved. Please, try again.'),'default',array('class'=>'error-box error-message'));
			}
		    
		}
		if (empty($this->request->data)) {
			$this->request->data = $this->University->read(null, $id);
		}
		$years = array();
		for($i = Configure::read('Calendar.universityEstablishement'); $i <= date('Y')+1; $i++) {
			$years[$i] = $i;
		}
		$this->set(compact('years'));
	}

	function delete($id = null) {
		if (!$id) {
			$this->Session->setFlash('<span></span>'.__('Invalid id for university'),
			'default',array('class'=>'error-box error-message'));
			return $this->redirect(array('action'=>'index'));
		}
		if ($this->University->delete($id)) {
		//if ($this->University->deleteAll(array('id' =>$id), false, true)) {
			$this->Session->setFlash('<span></span>'.__('University deleted'),
			'default',array('class'=>'success-box success-message'));
			return $this->redirect(array('action'=>'index'));
		}
		$this->Session->setFlash('<span></span>'.__('University was not deleted'),
		'default',array('class'=>'error-box error-message'));
		return $this->redirect(array('action' => 'index'));
	}
}
