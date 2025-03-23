<?php

namespace App\Controller;

use App\Controller\AppController;

use Cake\Event\Event;
use Cake\ORM\TableRegistry;
use Cake\Core\Configure;

class OfficialTranscriptRequestsController extends AppController
{

    public $name = 'OfficialTranscriptRequests';
    public $menuOptions = array(
        'parent' => 'dashboard',
        'alias' => array(
            'index' => 'View Offical Transcript Request',

        )
    );

    public $paginate = array();

    public function initialize()
    {

        parent::initialize();
        $this->loadComponent('Paginator'); // Ensure Paginator is loaded


    }

    public function beforeFilter(Event $event)
    {

        parent::beforeFilter($event);
        $this->Auth->Allow('search', 'view');
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

        $this->OfficialTranscriptRequest->recursive = 0;

        // filter by tracking number
        if (
            isset($this->passedArgs['OfficialTranscriptRequest.trackingnumber'])) {
            $trackingnumber = $this->passedArgs['OfficialTranscriptRequest.trackingnumber'];
            if (!empty($trackingnumber)) {
                $this->paginate['conditions'][]['OfficialTranscriptRequest.trackingnumber'] = $trackingnumber;
            }
            $this->request->data['OfficialTranscriptRequest']['trackingnumber'] = $this->passedArgs['OfficialTranscriptRequest.trackingnumber'];
        }
        // filter by name
        if (isset($this->passedArgs['OfficialTranscriptRequest.name'])) {
            $name = $this->passedArgs['OfficialTranscriptRequest.name'];
            if (!empty($name)) {
                $this->paginate['conditions'][]['OfficialTranscriptRequest.first_name like'] = '%' . $name . '%';
            }
            $this->request->data['OfficialTranscriptRequest']['name'] = $this->passedArgs['OfficialTranscriptRequest.name'];
        }
        // filter by period
        if (isset($this->passedArgs['OfficialTranscriptRequest.request_to.year'])) {
            $this->paginate['conditions'][] = array(
                'OfficialTranscriptRequest.created <= \'' . $this->passedArgs['OfficialTranscriptRequest.request_to.year']
                . '-' . $this->passedArgs['OfficialTranscriptRequest.request_to.month'] . '-' . $this->passedArgs['OfficialTranscriptRequest.request_to.day'] . '\''
            );

            $this->paginate['conditions'][] = array(
                'OfficialTranscriptRequest.created >= \'' . $this->passedArgs['OfficialTranscriptRequest.request_from.year']
                . '-' . $this->passedArgs['OfficialTranscriptRequest.request_from.month'] . '-' . $this->passedArgs['OfficialTranscriptRequest.request_from.day'] . '\''
            );
            $this->request->data['OfficialTranscriptRequest']['request_from'] = $this->passedArgs['OfficialTranscriptRequest.request_from.year']
                . '-' . $this->passedArgs['OfficialTranscriptRequest.request_from.month'] . '-' . $this->passedArgs['OfficialTranscriptRequest.request_from.day'];

            $this->request->data['OfficialTranscriptRequest']['request_to'] = $this->passedArgs['OfficialTranscriptRequest.request_to.year']
                . '-' . $this->passedArgs['OfficialTranscriptRequest.request_to.month'] . '-' . $this->passedArgs['OfficialTranscriptRequest.request_to.day'];
        }

        debug($this->request->data);

        $this->Paginator->settings = $this->paginate;
        $officialTranscriptRequests =
            $this->Paginator->paginate('OfficialTranscriptRequest');

        if (empty($officialTranscriptRequests) && isset($this->request->data) && !empty($this->request->data)) {
            $this->Session->setFlash(
                '<span></span>' . __('There is no official transcript request based on the given criteria.'),
                'default',
                array('class' => 'info-box info-message')
            );
        }

        $admissiontypes = ClassRegistry::init('ProgramType')->find('list', array(
            'fields' => array(
                'ProgramType.name',
                'ProgramType.name'
            )
        ));
        $degreetypes['Bachelor of Arts'] = "Bachelor of Arts";
        $degreetypes['Bachelor of Science'] = "Bachelor of Science";
        $degreetypes['Doctor of Medicine'] = "Doctor of Medicine";
        $degreetypes['Master of Science'] = "Master of Science";
        $degreetypes['Master of Arts'] = "Master of Arts";
        $degreetypes['Doctor of Philosophy'] = 'Doctor of Philosophy';
        $this->set(
            compact(
                'admissiontypes',
                'degreetypes',
                'officialTranscriptRequests'
            )
        );
    }

    public function view($id = null)
    {

        if (!$this->OfficialTranscriptRequest->exists($id)) {
            throw new NotFoundException(__('Invalid official transcript request'));
        }
        $options = array('conditions' => array('OfficialTranscriptRequest.' . $this->OfficialTranscriptRequest->primaryKey => $id));
        $statuses = array(
            'request_verified' => 'Request Verified',
            'request_cancelled' => 'Request Cancelled',
            'document_sent' => 'Document Sent To Destination'
        );

        $this->set('officialTranscriptRequest', $this->OfficialTranscriptRequest->find('first', $options));
        $this->set(compact('statuses'));
    }

    public function delete($id = null)
    {

        $this->OfficialTranscriptRequest->id = $id;
        if (!$this->OfficialTranscriptRequest->exists()) {
            throw new NotFoundException(__('Invalid official transcript request'));
        }
        $this->request->allowMethod('post', 'delete');
        if ($this->OfficialTranscriptRequest->delete()) {
            $this->Session->setFlash(
                '<span></span>' . __('The official transcript request has been deleted.'),
                'default',
                array('class' => 'success-box	success-message')
            );
        } else {
            $this->Session->setFlash(
                '<span></span>' . __('The official transcript request could not be deleted. Please, try again.'),
                'default',
                array('class' => 'error-box error-message')
            );
        }
        return $this->redirect(array('action' => 'index'));
	}
}
