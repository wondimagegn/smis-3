<?php
class OfficesController extends AppController {

	var $name = 'Offices';
    var $menuOptions = array(           
             'parent' => 'dashboard',
             'alias' => array(
                    'index'=>'View Clearance Offices',
                    'add'=>'Add Clearance Office',
            )
    );
	function index() {
		$this->Office->recursive = 0;
		$this->set('offices', $this->paginate());
	}

	function view($id = null) {
		if (!$id) {
			$this->Session->setFlash('<span></span>'.__('Invalid office'),
			'default',array('class'=>'error-box error-message'));
			return $this->redirect(array('action' => 'index'));
		}
		
		$this->set('office',$this->Office->find('first', array('conditions'=>array('Office.id'=>$id),
		'contain'=>array('TakenProperty'=>array('Student','Office')))));
	}

	function add() {
		if (!empty($this->request->data)) {
			$this->Office->create();
			if ($this->Office->save($this->request->data)) {
				$this->Session->setFlash('<span></span>'.__('The office has been saved'),
				'default',array('class'=>'success-box success-message'));
				return $this->redirect(array('action' => 'index'));
			} else {
				$this->Session->setFlash('<span></span>'.__('The office could not be saved. Please, try again.'),'default',array('class'=>'error-box error-message'));
			}
		}
	    $staffs = $this->Office->Staff->find('list',array('fields'=>array('id','full_name'),'conditions'=>array(
		        'Staff.user_id IN (select id from users where role_id='.ROLE_CLEARANCE.' 
		        OR role_id = '.ROLE_ACCOMODATION.')',
		        'Staff.id NOT IN (select staff_id from offices)'
		
		),
		'contain'=>array()));
		
        $this->set(compact('staffs'));
				
	}

	function edit($id = null) {
		if (!$id && empty($this->request->data)) {
			$this->Session->setFlash('<span></span>'.__('Invalid office'),
			'default',array('class'=>'error-box error-message'));
			return $this->redirect(array('action' => 'index'));
		}
		if (!empty($this->request->data)) {
			if ($this->Office->save($this->request->data)) {
				$this->Session->setFlash('<span></span>'.__('The office has been saved'),
				'default',array('class'=>'success-box success-message'));
				return $this->redirect(array('action' => 'index'));
			} else {
				$this->Session->setFlash('<span></span>'.__('The office could not be saved. Please, try again.'),'default',array('class'=>'error-box error-message'));
			}
		}
		if (empty($this->request->data)) {
			$this->request->data = $this->Office->read(null, $id);
		}
		
		
		 $staffs = $this->Office->Staff->find('list',array('fields'=>array('id','full_name'),'conditions'=>array(
		        'Staff.user_id IN (select id from users where role_id='.ROLE_CLEARANCE.' 
		        OR role_id = '.ROLE_ACCOMODATION.')'
		
		),
		'contain'=>array()));
		$this->set(compact('staffs'));
		
	}

	function delete($id = null) {
		if (!$id) {
			$this->Session->setFlash('<span></span>'.__('Invalid id for office'),
			'default',array('class'=>'error-box error-message'));
			return $this->redirect(array('action'=>'index'));
		}
		$check_student_taken_properties_recorded=$this->Office->TakenProperty->find('count',
		array('conditions'=>array('TakenProperty.office_id'=>$id)));
		if ($check_student_taken_properties_recorded==0) {
		
		    if ($this->Office->delete($id)) {
			    $this->Session->setFlash('<span></span>'.__('Office deleted'),
			    'default',array('class'=>'success-box success-message'));
			    $this->redirect(array('action'=>'index'));
		    }
		} else {
		    $this->Session->setFlash('<span></span>'.__('Office was not deleted. Student  taken properties has already recorded by the name of the office.'),
		'default',array('class'=>'error-box error-message'));
		    $this->redirect(array('action'=>'index'));
		}
		$this->Session->setFlash('<span></span>'.__('Office was not deleted'),
		'default',array('class'=>'error-box error-message'));
		return $this->redirect(array('action' => 'index'));
	}
}
