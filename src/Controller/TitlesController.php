<?php

namespace App\Controller;

use App\Controller\AppController;

use Cake\Event\Event;
use Cake\ORM\TableRegistry;
use Cake\Core\Configure;

class TitlesController extends AppController
{

    public $name = 'Titles';
    public $menuOptions = array(
        'parent' => 'mainDatas',
        'alias' => array(
            'index' => 'View Title',
            'add' => 'Add Title',
        )
    );
    public $paginate = [];

    public function initialize()
    {

        parent::initialize();
        $this->loadComponent('AcademicYear');
        $this->loadComponent('Paginator'); // Ensure Paginator is loaded

    }

    public function beforeFilter(Event $event)
    {

        parent::beforeFilter($event);
    }

    public function index()
    {

        $this->Title->recursive = 0;
        $this->set('titles', $this->paginate());
    }

    public function view($id = null)
    {

        if (!$id) {
            $this->Session->setFlash(__('Invalid title'));
            return $this->redirect(array('action' => 'index'));
        }
        $this->set('title', $this->Title->read(null, $id));
    }

    public function add()
    {

        if (!empty($this->request->data)) {
            $this->Title->create();
            if ($this->Title->save($this->request->data)) {
                $this->Session->setFlash(
                    '<span></span>' . __('The title has been saved'),
                    'default',
                    array('class' => 'success-box success-message')
                );
                return $this->redirect(array('action' => 'index'));
            } else {
                $this->Session->setFlash(
                    '<span></span>' . __('The title could not be saved. Please, try again.'),
                    'default',
                    array('class' => 'error-box error-message')
                );
            }
        }
    }

    public function edit($id = null)
    {

        if (!$id && empty($this->request->data)) {
            $this->Session->setFlash(__('Invalid title'));
            return $this->redirect(array('action' => 'index'));
        }
        if (!empty($this->request->data)) {
            if ($this->Title->save($this->request->data)) {
                $this->Session->setFlash(
                    '<span></span>' . __('The title has been saved'),
                    'default',
                    array('class' => 'success-box success-message')
                );
                return $this->redirect(array('action' => 'index'));
            } else {
                $this->Session->setFlash(
                    '<span></span>' . __('The title could not be saved. Please, try again.'),
                    'default',
                    array('class' => 'error-box error-message')
                );
            }
        }
        if (empty($this->request->data)) {
            $this->request->data = $this->Title->read(null, $id);
        }
    }

    public function delete($id = null)
    {

        if (!$id) {
            $this->Session->setFlash(__('Invalid id for title'));
            return $this->redirect(array('action' => 'index'));
        }
        if ($this->Title->delete($id)) {
            $this->Session->setFlash(
                '<span></span>' . __('Title deleted'),
                'default',
                array('class' => 'success-box success-message')
            );
            return $this->redirect(array('action' => 'index'));
        }
        $this->Session->setFlash(
            '<span></span>' . __('Title was not deleted'),
            'default',
            array('class' => 'error-box error-message')
        );
        return $this->redirect(array('action' => 'index'));
    }
}
