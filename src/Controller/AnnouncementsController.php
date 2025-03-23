<?php

namespace App\Controller;


use App\Controller\AppController;
use Cake\Event\Event;
use Cake\ORM\TableRegistry;
use Cake\Core\Configure;

class AnnouncementsController extends AppController
{

    public $name = 'Announcements';
    public $paginate = [];
    public $menuOptions = array(
        'parent' => 'dashboard',
        'exclude' => array('index'),
        'weight' => -2,
        'alias' => array(
            'add' => 'Add Announcement',
            'index' => 'View Announcement',
        )
    );

    public function initialize()
    {

        parent::initialize();
        $this->loadComponent('Paginator'); // Ensure Paginator is loaded
    }

    public function beforeFilter(Event $event)
    {

        parent::beforeFilter($event);
        //$this->Auth->Allow('add','index');
    }

    public function index()
    {

        $this->Announcement->recursive = 0;
        $this->set('announcements', $this->Paginator->paginate());
    }

    public function view($id = null)
    {

        if (!$this->Announcement->exists($id)) {
            throw new NotFoundException(__('Invalid announcement'));
        }
        $options = array('conditions' => array('Announcement.' . $this->Announcement->primaryKey => $id));
        $this->set('announcement', $this->Announcement->find('first', $options));
    }

    public function add()
    {

        if ($this->request->is('post')) {
            $this->Announcement->create();
            $this->request->data['Announcement']['user_id'] = $this->Auth->user('id');
            debug($this->request->data);
            if ($this->Announcement->save($this->request->data)) {
                $this->Session->setFlash(
                    '<span></span>' . __('The announcement has been saved.'),
                    'default',
                    array('class' => 'success-box success-message')
                );

                return $this->redirect(array('action' => 'index'));
            } else {
                $this->Session->setFlash(
                    '<span></span>' . __('The announcement could not be saved. Please, try again.'),
                    'default',
                    array('class' => 'error-box error-message')
                );
            }
        }
    }

    public function edit($id = null)
    {

        if (!$this->Announcement->exists($id)) {
            throw new NotFoundException(__('Invalid announcement'));
        }
        if ($this->request->is(array('post', 'put'))) {
            if ($this->Announcement->save($this->request->data)) {
                $this->Session->setFlash(
                    '<span></span>' . __('The announcement has been saved.'),
                    'default',
                    array('class' => 'success-box success-message')
                );
                return $this->redirect(array('action' => 'index'));
            } else {
                $this->Session->setFlash(
                    '<span></span>' . __('The announcement could not be saved. Please, try again.'),
                    'default',
                    array('class' => 'error-box error-message')
                );
            }
        } else {
            $options = array('conditions' => array('Announcement.' . $this->Announcement->primaryKey => $id));
            $this->request->data = $this->Announcement->find('first', $options);
        }
        $users = $this->Announcement->User->find('list');
        $this->set(compact('users'));
    }

    public function delete($id = null)
    {

        $this->Announcement->id = $id;
        if (!$this->Announcement->exists()) {
            throw new NotFoundException(__('Invalid announcement'));
        }
        $this->request->allowMethod('post', 'delete');
        if ($this->Announcement->delete()) {
            $this->Session->setFlash(
                '<span></span>' . __('The announcement has been deleted.'),
                'default',
                array('class' => 'success-box success-message')
            );
        } else {
            $this->Session->setFlash(
                '<span></span>' . __('The announcement could not be deleted. Please, try again.'),
                'default',
                array('class' => 'error-box error-message')
            );
        }
        return $this->redirect(array('action' => 'index'));
    }
}
