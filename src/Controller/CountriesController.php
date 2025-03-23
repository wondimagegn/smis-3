<?php
namespace App\Controller;
use App\Controller\AppController;


use Cake\Event\Event;
use Cake\ORM\TableRegistry;
use Cake\Core\Configure;

class CountriesController extends AppController
{

    public $name = 'Countries';
    public $menuOptions = array(
        'parent' => 'mainDatas',
        'alias' => array(
            'index' => 'View Countries',
            'add' => 'Add Country',
        )
    );

    public function beforeFilter(Event $event)
    {

        parent::beforeFilter($event);
    }

    public function index()
    {

        $this->Country->recursive = 0;
        $this->set('countries', $this->paginate());
    }

    public function view($id = null)
    {

        if (!$id) {
            $this->Session->setFlash(__('Invalid country'));
            return $this->redirect(array('action' => 'index'));
        }
        $this->set('country', $this->Country->read(null, $id));
    }

    public function add()
    {

        if (!empty($this->request->data)) {
            $this->Country->create();
            if ($this->Country->save($this->request->data)) {
                $this->Session->setFlash(
                    '<span></span>' . __('The country has been saved'),
                    'default',
                    array('class' => 'success-box success-message')
                );
                return $this->redirect(array('action' => 'index'));
            } else {
                $this->Session->setFlash(
                    '<span></span>' . __('The country could not be saved. Please, try again.'),
                    'default',
                    array('class' => 'error-box error-message')
                );
            }
        }
    }

    public function edit($id = null)
    {

        if (!$id && empty($this->request->data)) {
            $this->Session->setFlash(__('Invalid country'));
            return $this->redirect(array('action' => 'index'));
        }
        if (!empty($this->request->data)) {
            if ($this->Country->save($this->request->data)) {
                $this->Session->setFlash(
                    '<span></span>' . __('The country has been saved'),
                    'default',
                    array('class' => 'success-box success-message')
                );
                return $this->redirect(array('action' => 'index'));
            } else {
                $this->Session->setFlash(
                    '<span></span>' . __('The country could not be saved. Please, try again.'),
                    'default',
                    array('class' => 'error-box error-message')
                );
            }
        }
        if (empty($this->request->data)) {
            $this->request->data = $this->Country->read(null, $id);
        }
    }

    public function delete($id = null)
    {

        if (!$id) {
            $this->Session->setFlash(__('Invalid id for country'));
            return $this->redirect(array('action' => 'index'));
        }
        // is country related to student
        if ($this->Country->canItBeDeleted($id)) {
            if ($this->Country->delete($id)) {
                $this->Session->setFlash(
                    '<span></span>' . __('Country deleted'),
                    'default',
                    array('class' => 'success-box success-message')
                );
                $this->redirect(array('action' => 'index'));
            }
        } else {
            $this->Session->setFlash(
                '<span></span>' . __('You can not delete this country it is related to student,region, contact.'),
                'default',
                array('class' => 'error-box error-message')
            );
            $this->redirect(array('action' => 'index'));
        }
        $this->Session->setFlash(
            '<span></span>' . __('Country was not deleted'),
            'default',
            array('class' => 'error-box error-message')
        );
        return $this->redirect(array('action' => 'index'));
    }

}
