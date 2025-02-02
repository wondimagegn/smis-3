<?php
App::uses('AppController', 'Controller');
class OnlineApplicantStatusesController extends AppController
{

	public $name = 'OnlineApplicantStatuses';
	public $menuOptions = array(

		'parent' => 'placement',
		'exclude' => array('search'),

		'alias' => array(
			'index' => 'View Online Admission Status',
			'add' => 'Add Online Admission Status',

		)
	);

	public $components = array('EthiopicDateTime', 'Email', 'Paginator', 'AcademicYear');


	public function beforeRender()
	{

		//$acyear_array_data = $this->AcademicYear->acyear_array();
		$acyear_array_data = $this->AcademicYear->academicYearInArray(date('Y') - 1, date('Y'));
		//To diplay current academic year as default in drop down list
		$defaultacademicyear = $this->AcademicYear->current_academicyear();
		// $defaultacademicyear=$this->AcademicYear->academicYearInArray(date('Y')-1, date('Y'));

		$this->set(compact('acyear_array_data', 'defaultacademicyear'));
	}

	public $paginate = array();
	public function beforeFilter()
	{
		parent::beforeFilter();
		$this->Auth->Allow('add', 'edit', 'search');
	}
	/*
	 *Generic search for returned items
	 */
	public function search()
	{
		// the page we will redirect to
		$url['action'] = 'index';

		// build a URL will all the search elements in it
		// the resulting URL will be
		// domain.com/returned_items/index/Search.keywords:mykeyword/Search.tag_id:3
		//debug($this->request->data);
		foreach ($this->request->data as $k => $v) {
			foreach ($v as $kk => $vv) {
				if (is_array($vv)) {
					foreach ($vv as $kkk => $vvv)
						$url[$k . '.' . $kk . '.' . $kkk] = $vvv;
				} else
					$url[$k . '.' . $kk] = $vv;
			}
		}
		// redirect the user to the url
		return $this->redirect($url, null, true);
	}


	public function index()
	{

		$this->paginate = array('contain' => array('OnlineApplicant'), 'order' => array('OnlineApplicantStatus.created DESC'));
		// filter by application number
		if (
			isset($this->passedArgs['OnlineApplicantStatus.trackingnumber'])
		) {
			$applicationnumber = $this->passedArgs['OnlineApplicantStatus.trackingnumber'];
			if (!empty($applicationnumber)) {
				$this->paginate['conditions'][]['OnlineApplicant.trackingnumber'] = $applicationnumber;
			}
			$this->request->data['OnlineApplicantStatus']['applicationnumber'] = $this->passedArgs['OnlineApplicantStatus.applicationnumber'];
		}
		// filter by name
		if (isset($this->passedArgs['OnlineApplicantStatus.name'])) {
			$name = $this->passedArgs['OnlineApplicantStatus.name'];
			if (!empty($name)) {
				$this->paginate['conditions'][]['OnlineApplicant.first_name like'] = '%' . $name . '%';
			}
			$this->request->data['OnlineApplicantStatus']['name'] = $this->passedArgs['OnlineApplicantStatus.name'];
		}
		// filter by period
		if (isset($this->passedArgs['OnlineApplicantStatus.request_to.year'])) {
			$this->paginate['conditions'][] = array('OnlineApplicantStatus.created <= \'' . $this->passedArgs['OnlineApplicantStatus.request_to.year']
				. '-' . $this->passedArgs['OnlineApplicantStatus.request_to.month'] . '-' . $this->passedArgs['OnlineApplicantStatus.request_to.day'] . '\'');

			$this->paginate['conditions'][] = array('OnlineApplicantStatus.created >= \'' . $this->passedArgs['OnlineApplicantStatus.request_from.year']
				. '-' . $this->passedArgs['OnlineApplicantStatus.request_from.month'] . '-' . $this->passedArgs['OnlineApplicantStatus.request_from.day'] . '\'');
			$this->request->data['OnlineApplicantStatus']['request_from'] = $this->passedArgs['OnlineApplicantStatus.request_from.year']
				. '-' . $this->passedArgs['OnlineApplicantStatus.request_from.month'] . '-' . $this->passedArgs['OnlineApplicantStatus.request_from.day'];

			$this->request->data['OnlineApplicantStatus']['request_to'] = $this->passedArgs['OnlineApplicantStatus.request_to.year']
				. '-' . $this->passedArgs['OnlineApplicantStatus.request_to.month'] . '-' . $this->passedArgs['OnlineApplicantStatus.request_to.day'];
		}

		$this->Paginator->settings = $this->paginate;
		$onlineApplicantStatuses =
			$this->Paginator->paginate('OnlineApplicantStatus');

		if (empty($onlineApplicantStatuses) && isset($this->request->data) && !empty($this->request->data)) {
			$this->Session->setFlash('<span></span>' . __('There is no online admission request status based on the given criteria.'), 'default', array('class' => 'info-box info-message'));
		}

		$statuses = array('Pending' => 'Pending', 'Accepted' => 'Accepted', 'Rejected' => 'Rejected');
		$this->set(compact('statuses'));

		$this->set(
			'onlineApplicantStatuses',
			$this->Paginator->paginate()
		);
	}

	public function view($id = null)
	{
		if (!$this->OnlineApplicantStatus->exists($id)) {
			throw new NotFoundException(
				__('Invalid online admission  status')
			);
		}
		$options = array('conditions' => array('OnlineApplicantStatus.' . $this->OnlineApplicantStatus->primaryKey => $id));
		$this->set('onlineApplicantStatus', $this->OnlineApplicantStatus->find('first', $options));
	}

	public function add($request_id = null)
	{
		if ($this->request->is('post')) {
			$this->OnlineApplicantStatus->create();
			if ($this->OnlineApplicantStatus->save($this->request->data)) {
				//update processed when the status of is document_sent
				$applicantDetail = $this->OnlineApplicantStatus->OnlineApplicant->find(
					'first',
					array('conditions' => array('OnlineApplicant.id' => $this->request->data['OnlineApplicantStatus']['online_applicant_id']))
				);
				$this->OnlineApplicantStatus->OnlineApplicant->id = $this->request->data['OnlineApplicantStatus']['online_applicant_id'];
				$statuses = array('Pending' => 'Pending', 'Accepted' => 'Accepted', 'Rejected' => 'Rejected');

				if ($this->request->data['OnlineApplicantStatus']['status'] == "Accepted") {
					$request_processed = 1;
				} else if ($this->request->data['OnlineApplicantStatus']['status'] == "Rejected") {
					$request_processed = -1;
				} else if ($this->request->data['OnlineApplicantStatus']['status'] == 'Pending') {
					$request_processed = 0;
				}

				$this->OnlineApplicantStatus->OnlineApplicant->saveField(
					'application_status',
					$request_processed
				);
				$this->OnlineApplicantStatus->OnlineApplicant->saveField(
					'approved_by',
					$this->Auth->user('full_name')
				);

				$message = "Your online admission status has been updated and please check  the most recent status using your application  number  <u> " . $applicantDetail['OnlineApplicant']['applicationnumber'] . "</u> <br/>";

				$Email = new CakeEmail('default');
				$Email->template('onlineapplication');
				$Email->emailFormat('html');
				$Email->from(array('wondetask@gmail.com' => 'AMU Student Portal'));
				$Email->to($applicantDetail['OnlineApplicant']['email']);
				$Email->subject('Online Admission Status Updated: ' . $applicantDetail['OnlineApplicant']['first_name'] . ' ' . $applicantDetail['OnlineApplicant']['father_name'] . ' for ' . $applicantDetail['OnlineApplicant']['academic_year'] . ' academic year');
				$Email->viewVars(array('message' => $message));
				try {
					if ($Email->send()) {
						$this->Session->setFlash('<span></span>' . __("Status updated and notification sent to " . $applicantDetail['OnlineApplicant']['email'] . " email address. "), 'default', array('class' => 'success-box success-message'));
					} else {
						$this->Session->setFlash('<span></span>' . __("Status updated but unable to send notification to . "), 'default', array('class' => 'success-box success-message'));
					}
				} catch (Exception $e) {
					$this->Session->setFlash('<span></span>' . __("Someting went wrong when sending notification  to " . $applicantDetail['OnlineApplicant']['email'] . " email address."), 'default', array('class' => 'success-box success-message'));
					return $this->redirect(array('action' => 'index'));
				}

				return $this->redirect(array('action' => 'index'));
			} else {
				$error = $this->OnlineApplicantStatus->invalidFields();
				debug($error);
				$this->Session->setFlash('<span></span>' . __('The online applicant status could not be saved. Please, try again.'), 'default', array('class' => 'error-box success-message'));
			}
		}
		$date = date("Y-m-d", strtotime("-30 day"));
		if (isset($request_id) && !empty($request_id)) {
			$requests = $this->OnlineApplicantStatus->OnlineApplicant->find('all', array(
				'conditions' => array(
					'OnlineApplicant.application_status' => array(0, 1),
					'OnlineApplicant.id' => $request_id,

				),
				'contain' => array('OnlineApplicantStatus')
			));
		} else {
			$requests = $this->OnlineApplicantStatus->OnlineApplicant->find('all', array(
				'conditions' => array(
					'OnlineApplicant.application_status' => array(0, 1),
					'OnlineApplicant.created >= ' => $date,
					//'OfficialRequestStatus.status != '=>'document_sent'
				),
				'contain' => array('OnlineApplicantStatus')
			));
		}
		$onlineApplicants = array();
		debug($requests);
		foreach ($requests as $k) {
			//check if the status is document_sent
			$onlineApplicants[$k['OnlineApplicant']['id']] = $k['OnlineApplicant']['full_name'] . '(' . $k['OnlineApplicant']['applicationnumber'] . ')';
		}
		$statuses = array('Pending' => 'Pending', 'Accepted' => 'Accepted', 'Rejected' => 'Rejected');

		$this->set(compact(
			'onlineApplicants',
			'statuses'
		));
	}

	public function edit($id = null)
	{
		if (!$this->OnlineApplicantStatus->exists($id)) {
			throw new NotFoundException(__('Invalid online applicant  status'));
		}
		if ($this->request->is(array('post', 'put'))) {

			if ($this->OnlineApplicantStatus->save($this->request->data)) {
				//update processed when the status of is document_sent

				//update processed when the status of is document_sent
				$applicantDetail = $this->OnlineApplicantStatus->OnlineApplicant->find(
					'first',
					array('conditions' => array('OnlineApplicant.id' => $this->request->data['OnlineApplicantStatus']['online_applicant_id']))
				);
				$this->OnlineApplicantStatus->OnlineApplicant->id = $this->request->data['OnlineApplicantStatus']['online_applicant_id'];
				$statuses = array('Pending' => 'Pending', 'Accepted' => 'Accepted', 'Rejected' => 'Rejected');

				if ($this->request->data['OnlineApplicantStatus']['status'] == "Accepted") {
					$request_processed = 1;
				} else if ($this->request->data['OnlineApplicantStatus']['status'] == "Rejected") {
					$request_processed = -1;
				} else if ($this->request->data['OnlineApplicantStatus']['status'] == 'Pending') {
					$request_processed = 0;
				}

				$this->OnlineApplicantStatus->OnlineApplicant->saveField(
					'application_status',
					$request_processed
				);
				$this->OnlineApplicantStatus->OnlineApplicant->saveField(
					'approved_by',
					$this->Auth->user('full_name')
				);

				$message = "Your online admission status has been updated and please check  the most recent status using your application  number  <u> " . $applicantDetail['OnlineApplicant']['applicationnumber'] . "</u> <br/>";

				$Email = new CakeEmail('default');
				$Email->template('onlineapplication');
				$Email->emailFormat('html');
				$Email->from(array('wondetask@gmail.com' => 'AMU Student Portal'));
				$Email->to($applicantDetail['OnlineApplicant']['email']);
				$Email->subject('Online Admission Status Updated: ' . $applicantDetail['OnlineApplicant']['first_name'] . ' ' . $applicantDetail['OnlineApplicant']['father_name'] . ' for ' . $applicantDetail['OnlineApplicant']['academic_year'] . ' academic year');
				$Email->viewVars(array('message' => $message));
				try {
					if ($Email->send()) {
						$this->Session->setFlash('<span></span>' . __("Status updated and notification sent to " . $applicantDetail['OnlineApplicant']['email'] . " email address. "), 'default', array('class' => 'success-box success-message'));
					} else {
						$this->Session->setFlash('<span></span>' . __("Status updated but unable to send notification to . "), 'default', array('class' => 'success-box success-message'));
					}
				} catch (Exception $e) {
					$this->Session->setFlash('<span></span>' . __("Someting went wrong when sending notification  to " . $applicantDetail['OnlineApplicant']['email'] . " email address."), 'default', array('class' => 'success-box success-message'));
					return $this->redirect(array('action' => 'index'));
				}

				return $this->redirect(array('action' => 'index'));
			} else {

				$this->Session->setFlash('<span></span>' . __('The official request status could not be saved. Please, try again.'), 'default', array('class' => 'error-box success-message'));
			}
		} else {
			$options = array('conditions' => array('OnlineApplicantStatus.' . $this->OfficialRequestStatus->primaryKey => $id), 'contain' => 'OnlineApplicant');
			$this->request->data = $this->OnlineApplicantStatus->find('first', $options);
		}

		$date = date("Y-m-d", strtotime("-30 day"));
		if (isset($request_id) && !empty($request_id)) {
			$requests = $this->OnlineApplicantStatus->OnlineApplicant->find('all', array(
				'conditions' => array(
					'OnlineApplicant.application_status' => array(0, 1),
					'OnlineApplicant.id' => $request_id,

				),
				'contain' => array('OnlineApplicantStatus')
			));
		} else {
			$requests = $this->OnlineApplicantStatus->OnlineApplicant->find('all', array(
				'conditions' => array(
					'OnlineApplicant.application_status' => array(0, 1),
					'OnlineApplicant.created >= ' => $date,
					//'OfficialRequestStatus.status != '=>'document_sent'
				),
				'contain' => array('OnlineApplicantStatus')
			));
		}
		$onlineApplicants = array();
		foreach ($requests as $k) {
			//check if the status is document_sent
			$onlineApplicants[$k['OnlineApplicant']['id']] = $k['OnlineApplicant']['full_name'] . '(' . $k['OnlineApplicant']['applicationnumber'] . ')';
		}
		$statuses = array('Pending' => 'Pending', 'Accepted' => 'Accepted', 'Rejected' => 'Rejected');

		$this->set(compact('onlineApplicants', 'statuses'));
	}
	public function delete($id = null)
	{
		$this->OfficialRequestStatus->id = $id;
		if (!$this->OfficialRequestStatus->exists()) {
			throw new NotFoundException(__('Invalid applicant  status'));
		}
		$this->request->allowMethod('post', 'delete');
		if ($this->OfficialRequestStatus->delete()) {

			$this->Session->setFlash('<span></span>' . __('The online applicant status  has been deleted.'), 'default', array('class' => 'success-box success-message'));
		} else {

			$this->Session->setFlash('<span></span>' . __('The online applicant status could not be deleted. Please, try again.'), 'default', array('class' => 'error-box error-message'));
		}
		return $this->redirect(array('action' => 'index'));
	}
}