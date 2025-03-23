<?php

namespace App\Controller;

use App\Controller\AppController;

use Cake\Event\Event;
use Cake\ORM\TableRegistry;
use Cake\Core\Configure;

class OfficialRequestStatusesController extends AppController
{

    public $name = 'OfficialRequestStatuses';
    public $menuOptions = array(
        'parent' => 'dashboard',
        'alias' => array(
            'index' => 'View Offical Transcript Request Status',
            'add' => 'Add Official Transcript Request Status',

        )
    );

    public function initialize()
    {

        parent::initialize();
        $this->loadComponent('AcademicYear');
        $this->loadComponent('EthiopicDateTime');
        $this->loadComponent('Email');
        $this->loadComponent('Paginator'); // Ensure Paginator is loaded


    }


    public $paginate = array();

    public function beforeFilter(Event $event)
    {

        parent::beforeFilter($event);
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
                    foreach ($vv as $kkk => $vvv) {
                        $url[$k . '.' . $kk . '.' . $kkk] = $vvv;
                    }
                } else {
                    $url[$k . '.' . $kk] = $vv;
                }
            }
        }
        // redirect the user to the url
        return $this->redirect($url, null, true);
    }


    public function index()
    {

        $this->OfficialRequestStatus->recursive = 0;
        $this->paginate = array(
            'contain' => array('OfficialTranscriptRequest'),
            'order' => array('OfficialRequestStatus.created DESC')
        );
        // filter by tracking number
        if (
            isset($this->passedArgs['OfficialRequestStatus.trackingnumber'])) {
            $trackingnumber = $this->passedArgs['OfficialRequestStatus.trackingnumber'];
            if (!empty($trackingnumber)) {
                $this->paginate['conditions'][]['OfficialTranscriptRequest.trackingnumber'] = $trackingnumber;
            }
            $this->request->data['OfficialRequestStatus']['trackingnumber'] = $this->passedArgs['OfficialRequestStatus.trackingnumber'];
        }
        // filter by name
        if (isset($this->passedArgs['OfficialRequestStatus.name'])) {
            $name = $this->passedArgs['OfficialRequestStatus.name'];
            if (!empty($name)) {
                $this->paginate['conditions'][]['OfficialTranscriptRequest.first_name like'] = '%' . $name . '%';
            }
            $this->request->data['OfficialRequestStatus']['name'] = $this->passedArgs['OfficialRequestStatus.name'];
        }
        // filter by period
        if (isset($this->passedArgs['OfficialRequestStatus.request_to.year'])) {
            $this->paginate['conditions'][] = array(
                'OfficialRequestStatus.created <= \'' . $this->passedArgs['OfficialRequestStatus.request_to.year']
                . '-' . $this->passedArgs['OfficialRequestStatus.request_to.month'] . '-' . $this->passedArgs['OfficialRequestStatus.request_to.day'] . '\''
            );

            $this->paginate['conditions'][] = array(
                'OfficialRequestStatus.created >= \'' . $this->passedArgs['OfficialRequestStatus.request_from.year']
                . '-' . $this->passedArgs['OfficialRequestStatus.request_from.month'] . '-' . $this->passedArgs['OfficialRequestStatus.request_from.day'] . '\''
            );
            $this->request->data['OfficialRequestStatus']['request_from'] = $this->passedArgs['OfficialRequestStatus.request_from.year']
                . '-' . $this->passedArgs['OfficialRequestStatus.request_from.month'] . '-' . $this->passedArgs['OfficialRequestStatus.request_from.day'];

            $this->request->data['OfficialRequestStatus']['request_to'] = $this->passedArgs['OfficialRequestStatus.request_to.year']
                . '-' . $this->passedArgs['OfficialRequestStatus.request_to.month'] . '-' . $this->passedArgs['OfficialRequestStatus.request_to.day'];
        }

        $this->Paginator->settings = $this->paginate;
        $officialRequestStatuses =
            $this->Paginator->paginate('OfficialRequestStatus');

        if (empty($officialRequestStatuses) && isset($this->request->data) && !empty($this->request->data)) {
            $this->Session->setFlash(
                '<span></span>' . __('There is no official transcript request status based on the given criteria.'),
                'default',
                array('class' => 'info-box info-message')
            );
        }

        $statuses = array(
            'request_verified' => 'Request Verified',
            'request_cancelled' => 'Request Cancelled',
            'document_sent' => 'Document Sent To Destination'
        );
        $this->set(compact('statuses'));

        $this->set(
            'officialRequestStatuses',
            $this->Paginator->paginate()
        );
    }

    public function view($id = null)
    {

        if (!$this->OfficialRequestStatus->exists($id)) {
            throw new NotFoundException(
                __('Invalid official request status')
            );
        }
        $options = array('conditions' => array('OfficialRequestStatus.' . $this->OfficialRequestStatus->primaryKey => $id));
        $this->set('officialRequestStatus', $this->OfficialRequestStatus->find('first', $options));
    }

    public function add($request_id = null)
    {

        if ($this->request->is('post')) {
            $this->OfficialRequestStatus->create();
            if ($this->OfficialRequestStatus->save($this->request->data)) {
                //update processed when the status of is document_sent

                $this->OfficialRequestStatus->OfficialTranscriptRequest->id = $this->request->data['OfficialRequestStatus']['official_transcript_request_id'];
                $applicantDetail = $this->OfficialRequestStatus->OfficialTranscriptRequest->find(
                    'first',
                    array('conditions' => array('OfficialTranscriptRequest.id' => $this->request->data['OfficialRequestStatus']['official_transcript_request_id']))
                );

                $statuses = array(
                    'request_verified' => 'Request Verified',
                    'request_cancelled' => 'Request Cancelled',
                    'document_sent' => 'Document Sent To Destination'
                );
                if ($this->request->data['OfficialRequestStatus']['status'] == "request_verified") {
                    $request_processed = 1;
                } else {
                    if ($this->request->data['OfficialRequestStatus']['status'] == "request_cancelled") {
                        $request_processed = 2;
                    } else {
                        if ($this->request->data['OfficialRequestStatus']['status'] == 'document_sent') {
                            $request_processed = 3;
                        }
                    }
                }

                $this->OfficialRequestStatus->OfficialTranscriptRequest->saveField(
                    'request_processed',
                    $request_processed
                );

                $this->Session->setFlash(
                    '<span></span>' .
                    __('The request status has been saved.'),
                    'default',
                    array('class' => 'success-box success-message')
                );

                $message = "Your official transcript status has been updated and please check
				the most recent status using your application  number  <u> " .
                    $applicantDetail['OfficialTranscriptRequest']['trackingnumber'] .
                    "</u> <br/>";

                $Email = new CakeEmail('default');
                $Email->template('onlineapplication');
                $Email->emailFormat('html');
                $Email->from(array('wondetask@gmail.com' => 'AMU Student Portal'));
                $Email->to($applicantDetail['OfficialTranscriptRequest']['email']);
                $Email->subject(
                    'Official Transcript Request Status Updated: ' . $applicantDetail['OfficialTranscriptRequest']['first_name'] . ' ' . $applicantDetail['OfficialTranscriptRequest']['father_name'] . ' for ' . $applicantDetail['OfficialTranscriptRequest']['degreetype'] . ' '
                );
                $Email->viewVars(array('message' => $message));
                try {
                    if ($Email->send()) {
                        $this->Session->setFlash(
                            '<span></span>' . __(
                                "Official Status updated and notification sent to " . $applicantDetail['OfficialTranscriptRequest']['email'] . " email address. "
                            ),
                            'default',
                            array('class' => 'success-box success-message')
                        );
                    } else {
                        $this->Session->setFlash(
                            '<span></span>' . __("Status updated but unable to send notification. "),
                            'default',
                            array('class' => 'success-box success-message')
                        );
                    }
                } catch (Exception $e) {
                    $this->Session->setFlash(
                        '<span></span>' .
                        __(
                            "Someting went wrong when sending notification  to " .
                            $applicantDetail['OfficialTranscriptRequest']['email'] .
                            " email address."
                        ),
                        'default',
                        array('class' => 'success-box success-message')
                    );
                }

                return $this->redirect(array('action' => 'index'));
            } else {
                $this->Session->setFlash(
                    '<span></span>' . __('The official request status could not be saved. Please, try again.'),
                    'default',
                    array('class' => 'error-box success-message')
                );
            }
        }
        $date = date("Y-m-d", strtotime("-30 day"));
        if (isset($request_id) && !empty($request_id)) {
            $requests = $this->OfficialRequestStatus->OfficialTranscriptRequest->find('all', array(
                'conditions' => array(
                    'OfficialTranscriptRequest.request_processed' => array(0, 1),
                    'OfficialTranscriptRequest.id' => $request_id,

                ),
                'contain' => array('OfficialRequestStatus')
            ));
        } else {
            $requests = $this->OfficialRequestStatus->OfficialTranscriptRequest->find('all', array(
                'conditions' => array(
                    'OfficialTranscriptRequest.request_processed' => array(0, 1),
                    'OfficialTranscriptRequest.created >= ' => $date,
                    //'OfficialRequestStatus.status != '=>'document_sent'
                ),
                'contain' => array('OfficialRequestStatus')
            ));
        }
        $officialTranscriptRequests = array();
        debug($requests);
        foreach ($requests as $k) {
            //check if the status is document_sent
            $officialTranscriptRequests[$k['OfficialTranscriptRequest']['id']] = $k['OfficialTranscriptRequest']['first_name'] . ' ' . $k['OfficialTranscriptRequest']['father_name'] . ' ' . $k['OfficialTranscriptRequest']['grand_father'] . '(' . $k['OfficialTranscriptRequest']['trackingnumber'] . ')';
        }
        $statuses = array(
            'request_verified' => 'Request Verified',
            'request_cancelled' => 'Request Cancelled',
            'document_sent' => 'Document Sent To Destination'
        );
        $this->set(
            compact(
                'officialTranscriptRequests',
                'statuses'
            )
        );
    }

    public function edit($id = null)
    {

        if (!$this->OfficialRequestStatus->exists($id)) {
            throw new NotFoundException(__('Invalid official request status'));
        }
        if ($this->request->is(array('post', 'put'))) {
            if ($this->OfficialRequestStatus->save($this->request->data)) {
                //update processed when the status of is document_sent
                $this->OfficialRequestStatus->OfficialTranscriptRequest->id = $this->request->data['OfficialRequestStatus']['official_transcript_request_id'];
                $applicantDetail = $this->OfficialRequestStatus->OfficialTranscriptRequest->find(
                    'first',
                    array('conditions' => array('OfficialTranscriptRequest.id' => $this->request->data['OfficialRequestStatus']['official_transcript_request_id']))
                );

                $statuses = array(
                    'request_verified' => 'Request Verified',
                    'request_cancelled' => 'Request Cancelled',
                    'document_sent' => 'Document Sent To Destination'
                );
                if ($this->request->data['OfficialRequestStatus']['status'] == "request_verified") {
                    $request_processed = 1;
                } else {
                    if ($this->request->data['OfficialRequestStatus']['status'] == "request_cancelled") {
                        $request_processed = 2;
                    } else {
                        if ($this->request->data['OfficialRequestStatus']['status'] == 'document_sent') {
                            $request_processed = 3;
                        }
                    }
                }

                $this->OfficialRequestStatus->OfficialTranscriptRequest->saveField(
                    'request_processed',
                    $request_processed
                );


                $message = "Your official transcript status has been updated and please check
				the most recent status using your application  number  <u> " .
                    $applicantDetail['OfficialTranscriptRequest']['trackingnumber'] .
                    "</u> <br/>";

                $Email = new CakeEmail('default');
                $Email->template('onlineapplication');
                $Email->emailFormat('html');
                $Email->from(array('wondetask@gmail.com' => 'AMU Student Portal'));
                $Email->to($applicantDetail['OfficialTranscriptRequest']['email']);
                $Email->subject(
                    'Official Transcript Request Status Updated: ' . $applicantDetail['OfficialTranscriptRequest']['first_name'] . ' ' . $applicantDetail['OfficialTranscriptRequest']['father_name'] . ' for ' . $applicantDetail['OfficialTranscriptRequest']['degreetype'] . ' '
                );
                $Email->viewVars(array('message' => $message));
                try {
                    if ($Email->send()) {
                        $this->Session->setFlash(
                            '<span></span>' . __(
                                "Official Status updated and notification sent to " . $applicantDetail['OfficialTranscriptRequest']['email'] . " email address. "
                            ),
                            'default',
                            array('class' => 'success-box success-message')
                        );
                    } else {
                        $this->Session->setFlash(
                            '<span></span>' . __("Status updated but unable to send notification. "),
                            'default',
                            array('class' => 'success-box success-message')
                        );
                    }
                } catch (Exception $e) {
                    $this->Session->setFlash(
                        '<span></span>' .
                        __(
                            "Someting went wrong when sending notification  to " .
                            $applicantDetail['OfficialTranscriptRequest']['email'] .
                            " email address."
                        ),
                        'default',
                        array('class' => 'success-box success-message')
                    );
                }


                $this->Session->setFlash(
                    '<span></span>' . __('The request status has been saved.'),
                    'default',
                    array('class' => 'success-box success-message')
                );
                return $this->redirect(array('action' => 'index'));
            } else {
                $this->Session->setFlash(
                    '<span></span>' . __('The official request status could not be saved. Please, try again.'),
                    'default',
                    array('class' => 'error-box success-message')
                );
            }
        } else {
            $options = array(
                'conditions' => array('OfficialRequestStatus.' . $this->OfficialRequestStatus->primaryKey => $id),
                'contain' => 'OfficialTranscriptRequest'
            );
            $this->request->data = $this->OfficialRequestStatus->find('first', $options);
        }
        $date = date("Y-m-d", strtotime("-30 day"));
        $requests = $this->OfficialRequestStatus->OfficialTranscriptRequest->find('all', array(
            'conditions' => array(
                'OfficialTranscriptRequest.request_processed' => 0,
                'OfficialTranscriptRequest.created >= ' => $date,

                //'OfficialRequestStatus.status != '=>'document_sent'
            ),
            'contain' => array('OfficialRequestStatus')
        ));
        $officialTranscriptRequests = array();
        foreach ($requests as $k) {
            //check if the status is document_sent
            $officialTranscriptRequests[$k['OfficialTranscriptRequest']['id']] = $k['OfficialTranscriptRequest']['first_name'] . ' ' . $k['OfficialTranscriptRequest']['father_name'] . ' ' . $k['OfficialTranscriptRequest']['grand_father'] . '(' . $k['OfficialTranscriptRequest']['trackingnumber'] . ')';
        }
        $statuses = array(
            'request_verified' => 'Request Verified',
            'request_cancelled' => 'Request Cancelled',
            'document_sent' => 'Document Sent To Destination'
        );
        $this->set(
            compact(
                'officialTranscriptRequests',
                'statuses'
            )
        );
    }

    public function delete($id = null)
    {

        $this->OfficialRequestStatus->id = $id;
        if (!$this->OfficialRequestStatus->exists()) {
            throw new NotFoundException(__('Invalid official request status'));
        }
        $this->request->allowMethod('post', 'delete');
        if ($this->OfficialRequestStatus->delete()) {
            $this->Session->setFlash(
                '<span></span>' . __('The official request status has been deleted.'),
                'default',
                array('class' => 'success-box success-message')
            );
        } else {
            $this->Session->setFlash(
                '<span></span>' . __('The official request status could not be deleted. Please, try again.'),
                'default',
                array('class' => 'error-box error-message')
            );
        }
        return $this->redirect(array('action' => 'index'));
	}
}
