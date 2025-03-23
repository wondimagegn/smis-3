<?php
namespace App\Controller;


use Cake\Event\Event;


class AlumniMembersController extends AppController
{

    public $components = array('Paginator');
    public $name = 'AlumniMembers';
    public $menuOptions = array(
        //'parent' => 'Alumni',
        'weight' => 1,
        'exclude' => array('edit'),
        'alias' => array(
            'index' => 'View Alumni Registered Members',
            'add' => 'Add Alumni Member'

        )
    );
    public $paginate = array();

    public function initialize()
    {

        parent::initialize();
        $this->loadComponent('Paginator');
    }

    public function beforeFilter(Event $event)
    {

        parent::beforeFilter($event);
    }

    public function index()
    {

        $this->AlumniMember->recursive = 0;

        if (!empty($this->request->data['Display'])) {
            $this->Session->delete('display_field_student');
            $display_session = $this->request->data['Display'];
            // Session variable 'search_data'
            $this->Session->write('display_field_student', $display_session);
        }
        if (isset($this->request->data) && !empty($this->request->data)) {
            if (isset($this->request->data['Search']['department']) &&
                !empty($this->request->data['Search']['department'])) {
                $this->paginate['conditions'][]['AlumniMember.department like'] = '%' . $this->request->data['Search']['department'] . '%';
            }
            if (isset($this->request->data['Search']['gradution']) &&
                !empty($this->request->data['Search']['gradution'])) {
                $this->paginate['conditions'][]['AlumniMember.gradution'] = $this->request->data['Search']['gradution'];
            }

            if (isset($this->request->data['Search']['college']) &&
                !empty($this->request->data['Search']['college'])) {
                $this->paginate['conditions'][]['AlumniMember.institute_college like'] = '%' . $this->request->data['Search']['college'] . '%';
            }
            if (isset($this->request->data['Search']['college']) &&
                !empty($this->request->data['Search']['college'])) {
                $this->paginate['conditions'][]['AlumniMember.first_name like '] = '%' . $this->request->data['Search']['college'] . '%';
            }
            if (isset($this->request->data['Search']['program']) &&
                !empty($this->request->data['Search']['program'])) {
                $this->paginate['conditions'][]['AlumniMember.program like '] = '%' . $this->request->data['Search']['program'] . '%';
            }

            if (isset($this->request->data['Search']['program']) &&
                !empty($this->request->data['Search']['program'])) {
                $this->paginate['conditions'][]['AlumniMember.gender like '] = '%' . $this->request->data['Search']['gender'] . '%';
            }

            if (isset($this->request->data['Search']['limit'])) {
                $this->paginate['limit'] = $this->request->data['Search']['limit'];
                $this->paginate['maxLimit'] = $this->request->data['Search']['limit'];
            }
            $this->Paginator->settings = $this->paginate;

            $alumniMembers = $this->Paginator->paginate('AlumniMember');
        }

        $this->set(compact('alumniMembers'));
    }

    public function view($id = null)
    {

        if (!$this->AlumniMember->exists($id)) {
            throw new NotFoundException(__('Invalid alumni member'));
        }
        $options = array('conditions' => array('AlumniMember.' . $this->AlumniMember->primaryKey => $id));
        $this->set('alumniMember', $this->AlumniMember->find('first', $options));
    }

    public function delete($id = null)
    {

        $this->AlumniMember->id = $id;
        if (!$this->AlumniMember->exists()) {
            throw new NotFoundException(__('Invalid alumni member'));
        }
        $this->request->allowMethod('post', 'delete');
        if ($this->AlumniMember->delete()) {
            $this->Session->setFlash(
                '<span></span>' . __('The alumni member has been deleted.'),
                'default',
                array('class' => 'success-box success-message')
            );
        } else {
            $this->Session->setFlash(
                '<span></span>' . __('The alumni member could not be deleted. Please, try again..'),
                'default',
                array('class' => 'error-box error-message')
            );
        }
        return $this->redirect(array('action' => 'index'));
    }

    public function add()
    {

        if ($this->request->is('post')) {
            $this->AlumniMember->create();
            if ($this->AlumniMember->save($this->request->data)) {
                $this->Session->setFlash(
                    '<span></span>' . __('The alumni member has been saved.'),
                    'default',
                    array('class' => 'error-box error-message')
                );
                return $this->redirect(array('action' => 'index'));
            }
        }
    }

    public function edit($id = null)
    {

        if (!$this->AlumniMember->exists($id)) {
            throw new NotFoundException(__('Invalid alumni member'));
        }
        if ($this->request->is(array('post', 'put'))) {
            if ($this->AlumniMember->save($this->request->data)) {
                $this->Session->setFlash(
                    '<span></span>' . __('The alumni member has been saved.'),
                    'default',
                    array('class' => 'error-box error-message')
                );
                return $this->redirect(array('action' => 'index'));
            }
        } else {
            $options = array('conditions' => array('AlumniMember.' . $this->AlumniMember->primaryKey => $id));
            $this->request->data = $this->AlumniMember->find('first', $options);
        }
    }
}
