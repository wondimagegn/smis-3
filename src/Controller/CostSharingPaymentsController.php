<?php
class CostSharingPaymentsController extends AppController
{

	var $name = 'CostSharingPayments';
	var $menuOptions = array(
		'parent' => 'costShares',
		'exclude' => array('search'),
		'alias' => array(

			'add' => 'Cost Sharing Payment',
			'index' => 'View Payment'
		)
	);
	var $helpers = array('Media.Media');
	var $components = array('AcademicYear');

	function beforeRender()
	{

		$acyear_array_data = $this->AcademicYear->acyear_array();

		$this->set(compact('acyear_array_data'));
	}

	function index()
	{
		/*$this->CostSharingPayment->recursive = 0;
		$this->set('costSharingPayments', $this->paginate());
	    */

		if (!empty($this->request->data) && isset($this->request->params['form']['search'])) {

			$options = array();

			if (!empty($this->request->data['CostSharingPayment']['reference_number'])) {
				$options[] = array(

					"CostSharingPayment.reference_number" => trim($this->request->data['CostSharingPayment']['reference_number']),
					"Student.department_id" => $this->department_ids
				);
			}

			if (!empty($this->request->data['CostSharingPayment']['stud'])) {
				$options[] = array(

					"Student.deparment_id" => $this->department_ids,
					"Student.studentnumber like " => trim($this->request->data['CostSharingPayment']['studentnumber']) . '%'
				);
			}
			$costSharingPayments = $this->paginate($options);
		} else {

			$costSharingPayments = $this->paginate();
		}


		$this->set(compact('costSharingPayments'));
	}

	function view($id = null)
	{
		if (!$id) {
			$this->Session->setFlash(__('Invalid cost sharing payment'));
			return $this->redirect(array('action' => 'index'));
		}
		$this->set('costSharingPayment', $this->CostSharingPayment->read(null, $id));
	}

	public function add()
	{

		if (
			!empty($this->request->data) &&
			isset($this->request->data['saveApplicablePayment'])
		) {
			$this->CostSharingPayment->create();
			if ($this->CostSharingPayment->save($this->request->data)) {
				$this->Session->setFlash(
					'<span></span>' . __('The cost sharing payment has been saved'),
					'default',
					array('class' => 'success-box success-message')
				);
				return $this->redirect(array('action' => 'index'));
			} else {
				$this->Session->setFlash('<span></span>' . __('The cost sharing payment could not be saved. Please, try again.'), 'default', array('class' => 'error-box error-message'));
				$this->request->params['form']['continue'] = true;
				$student_number = $this->CostSharingPayment->Student->field(
					'studentnumber',
					array('id' => trim($this->request->data['CostSharingPayment']['student_id']))
				);
				$this->request->data['CostSharingPayment']['studentID'] = $student_number;
			}
		}

		if (!empty($this->request->data) && isset($this->request->data['continue'])) {

			debug($this->request->data);
			$everythingfine = false;
			if (!empty($this->request->data['CostSharingPayment']['studentID'])) {
				$check_id_is_valid = $this->CostSharingPayment->Student->find(
						'count',
						array('conditions' => array('Student.studentnumber' =>
						trim($this->request->data['CostSharingPayment']['studentID'])))
					);
				$studentIDs = 1;

				if ($check_id_is_valid > 0) {
					$everythingfine = true;
					$student_id = $this->CostSharingPayment->Student->field(
						'id',
						array('studentnumber' => trim($this->request->data['CostSharingPayment']['studentID']))
					);
					$student_section_exam_status = $this->CostSharingPayment->Student->get_student_section($student_id);
					$cost_share_summery = ClassRegistry::init('CostShare')->find('all', array('conditions' => array('CostShare.student_id' => $student_id), 'contain' => array('Student' => array('id', 'full_name'), 'Attachment')));

					$this->set(compact(
						'student_section_exam_status',
						'cost_share_summery'
					));

					$this->set(compact('studentIDs'));
				} else {
					$this->Session->setFlash('<span></span> ' . __('The provided student number is not valid.'), 'default', array('class' => 'error-box error-message'));
				}
			} else {
				$this->Session->setFlash('<span></span> ' . __('Please provide student number to maintain student cost sharing payment .'), 'default', array('class' => 'error-box error-message'));
			}
		}
	}

	function edit($id = null)
	{
		if (!$id && empty($this->request->data)) {
			$this->Session->setFlash(__('Invalid cost sharing payment'));
			return $this->redirect(array('action' => 'index'));
		}
		if (!empty($this->request->data)) {
			if ($this->CostSharingPayment->save($this->request->data)) {
				$this->Session->setFlash(__('The cost sharing payment has been saved'));
				return $this->redirect(array('action' => 'index'));
			} else {
				$this->Session->setFlash(__('The cost sharing payment could not be saved. Please, try again.'));
			}
		}
		if (empty($this->request->data)) {
			$this->request->data = $this->CostSharingPayment->read(null, $id);
		}
		//$students = $this->CostSharingPayment->Student->find('list');
		$this->set(compact('students'));
	}

	function delete($id = null)
	{
		if (!$id) {
			$this->Session->setFlash(
				'<span></span>' . __('Invalid id for cost sharing payment'),
				'default',
				array('class' => 'error-box error-message')
			);
			return $this->redirect(array('action' => 'index'));
		}
		if ($this->CostSharingPayment->delete($id)) {
			$this->Session->setFlash(
				'<span></span>' . __('Cost sharing payment deleted'),
				'default',
				array('class' => 'success-box success-message')
			);
			return $this->redirect(array('action' => 'index'));
		}
		$this->Session->setFlash(
			'<span></span>' . __('Cost sharing payment was not deleted'),
			'default',
			array('class' => 'error-box')
		);
		return $this->redirect(array('action' => 'index'));
	}
}