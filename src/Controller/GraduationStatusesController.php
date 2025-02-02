<?php
class GraduationStatusesController extends AppController {

	public $name = 'GraduationStatuses';
	public $menuOptions = array(
		'parent' => 'graduation',
		'weight'=>5,
		'alias' => array(
			'index' => 'View Graduation Status',
			'add' => 'New Graduation Status'
		)
	);
	public $paginate=array();
	
	public function index($id = null) {
		$programs = $this->GraduationStatus->Program->find('list');
		if(empty($id) && !empty($programs)) {
			$programKeys = array_keys($programs);
			$id = $programKeys[0];
		}
		if(!empty($id)) {
			$this->GraduationStatus->recursive = 0;
			$this->set('graduationStatuses', $this->paginate());
			$this->paginate['GraduationStatus'] = 
			array(
				'order' =>
				array(
					'GraduationStatus.academic_year' => 'DESC'
				),
				'conditions' =>
				array(
					'GraduationStatus.program_id' => $id
				)
			);
		     $this->Paginator->settings=$this->paginate;
			$graduationStatuses = $this->Paginator->paginate('GraduationStatus');
			if(empty($graduationStatuses)) {
				//$this->Session->setFlash('<span></span>'.__('There is no graduation status for the selected program.'), 'default',array('class'=>'info-message info-box'));
			}
			$this->set(compact('graduationStatuses', 'id'));
		}
		$this->set(compact('programs'));
	}

	function view($id = null) {
		if (!$id) {
			$this->Session->setFlash('<span></span>'.__('Invalid graduation status'), 'default',array('class'=>'error-box error-message'));
			return $this->redirect(array('action' => 'index'));
		}
		$this->set('graduationStatus', $this->GraduationStatus->read(null, $id));
	}

	function add() {
		if (!empty($this->request->data)) {
			$this->request->data['GraduationStatus']['status'] = trim($this->request->data['GraduationStatus']['status']);
			$this->request->data['GraduationStatus']['academic_year'] = $this->request->data['GraduationStatus']['academic_year']['year'];
			$cgpa_duplicate_check = 0;
			$status_duplicate_check = 0;
			$cgpa_duplicate_check = $this->GraduationStatus->find('count',
				array(
					'conditions' =>
					array(
						'GraduationStatus.academic_year' => $this->request->data['GraduationStatus']['academic_year'],
						'GraduationStatus.program_id' => $this->request->data['GraduationStatus']['program_id'],
						'GraduationStatus.cgpa' => $this->request->data['GraduationStatus']['cgpa'],
					)
				)
			);
			$status_duplicate_check = $this->GraduationStatus->find('count',
				array(
					'conditions' =>
					array(
						'GraduationStatus.academic_year' => $this->request->data['GraduationStatus']['academic_year'],
						'GraduationStatus.status' => $this->request->data['GraduationStatus']['status'],
					)
				)
			);
			if($cgpa_duplicate_check > 0) {
				$this->Session->setFlash('<span></span>'.__('There is already graduation status with '.$this->request->data['GraduationStatus']['cgpa'].' CGPA for '.$this->request->data['GraduationStatus']['academic_year'].' academic year. Please use edit to apply changes.'), 'default',array('class'=>'error-box error-message'));
			}
			else if($status_duplicate_check > 0) {
				$this->Session->setFlash('<span></span>'.__('There is already graduation status with "'.$this->request->data['GraduationStatus']['status'].'" status for '.$this->request->data['GraduationStatus']['academic_year'].' academic year. Please use edit to apply changes.'), 'default',array('class'=>'error-box error-message'));
			}
			else {
				$this->GraduationStatus->create();
				if ($this->GraduationStatus->save($this->request->data)) {
					$this->Session->setFlash('<span></span>'.__('The graduation status has been saved'), 'default',array('class'=>'success-box success-message'));
					return $this->redirect(array('action' => 'index'));
				} else {
					$this->Session->setFlash('<span></span>'.__('The graduation status could not be saved. Please, try again.'), 'default',array('class'=>'error-box error-message'));
				}
			}
		}
		$programs = $this->GraduationStatus->Program->find('list');
		$this->set(compact('programs'));
	}

	function edit($id = null) {
		if (!$id && empty($this->request->data)) {
			$this->Session->setFlash('<span></span>'.__('Invalid graduation status'), 'default',array('class'=>'error-box error-message'));
			return $this->redirect(array('action' => 'index'));
		}
		if (!empty($this->request->data)) {
			$this->request->data['GraduationStatus']['status'] = trim($this->request->data['GraduationStatus']['status']);
			$this->request->data['GraduationStatus']['academic_year'] = $this->request->data['GraduationStatus']['academic_year']['year'];
			$cgpa_duplicate_check = 0;
			$status_duplicate_check = 0;
			$cgpa_duplicate_check = $this->GraduationStatus->find('count',
				array(
					'conditions' =>
					array(
						'GraduationStatus.id <> ' => $this->request->data['GraduationStatus']['id'],
						'GraduationStatus.academic_year' => $this->request->data['GraduationStatus']['academic_year'],
						'GraduationStatus.program_id' => $this->request->data['GraduationStatus']['program_id'],
						'GraduationStatus.cgpa' => $this->request->data['GraduationStatus']['cgpa'],
					)
				)
			);
			$status_duplicate_check = $this->GraduationStatus->find('count',
				array(
					'conditions' =>
					array(
						'GraduationStatus.id <> ' => $this->request->data['GraduationStatus']['id'],
						'GraduationStatus.academic_year' => $this->request->data['GraduationStatus']['academic_year'],
						'GraduationStatus.status' => $this->request->data['GraduationStatus']['status'],
					)
				)
			);
			if($cgpa_duplicate_check > 0) {
				$this->Session->setFlash('<span></span>'.__('There is already graduation status with '.$this->request->data['GraduationStatus']['cgpa'].' CGPA for '.$this->request->data['GraduationStatus']['academic_year'].' academic year. Please use edit to apply changes.'), 'default',array('class'=>'error-box error-message'));
			}
			else if($status_duplicate_check > 0) {
				$this->Session->setFlash('<span></span>'.__('There is already graduation status with "'.$this->request->data['GraduationStatus']['status'].'" status for '.$this->request->data['GraduationStatus']['academic_year'].' academic year. Please use edit to apply changes.'), 'default',array('class'=>'error-box error-message'));
			}
			else {
				if ($this->GraduationStatus->save($this->request->data)) {
					$this->Session->setFlash('<span></span>'.__('The graduation status has been saved'), 'default',array('class'=>'success-box success-message'));
					return $this->redirect(array('action' => 'index', $this->request->data['GraduationStatus']['program_id']));
				} else {
					$this->Session->setFlash('<span></span>'.__('The graduation status could not be saved. Please, try again.'), 'default',array('class'=>'error-box error-message'));
				}
			}
		}
		if (empty($this->request->data)) {
			$this->request->data = $this->GraduationStatus->read(null, $id);
		}
		$programs = $this->GraduationStatus->Program->find('list');
		$this->set(compact('programs'));
	}

	function delete($id = null) {
		if (!$id) {
			$this->Session->setFlash('<span></span>'.__('Invalid id for graduation status'), 'default',array('class'=>'error-box error-message'));
			return $this->redirect(array('action'=>'index'));
		}
		if ($this->GraduationStatus->delete($id)) {
			$this->Session->setFlash('<span></span>'.__('Graduation status deleted'), 'default',array('class'=>'success-box success-message'));
			return $this->redirect(array('action'=>'index'));
		}
		$this->Session->setFlash('<span></span>'.__('Graduation status was not deleted'), 'default',array('class'=>'error-box error-message'));
		return $this->redirect(array('action' => 'index'));
	}
}
