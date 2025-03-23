<?php
namespace App\Controller;

use Cake\Event\Event;
use Cake\ORM\TableRegistry;
use Cake\Core\Configure;

class ContactsController extends AppController
{

    public $name = 'Contacts';

    public function index()
    {

        $this->Contact->recursive = 0;
        $this->set('contacts', $this->paginate());
    }

    public function view($id = null)
    {

        if (!$id) {
            $this->Session->setFlash(__('Invalid contact'));
            return $this->redirect(array('action' => 'index'));
        }
        $this->set('contact', $this->Contact->read(null, $id));
    }

    public function add()
    {

        if (!empty($this->request->data)) {
            $this->Contact->create();
            if ($this->Contact->save($this->request->data)) {
                $this->Session->setFlash(__('The contact has been saved'));
                return $this->redirect(array('action' => 'index'));
            } else {
                $this->Session->setFlash(__('The contact could not be saved. Please, try again.'));
            }
        }
        $students = $this->Contact->Student->find('list');
        $staffs = $this->Contact->Staff->find('list');
        $countries = $this->Contact->Country->find('list');
        $regions = $this->Contact->Region->find('list');
        $cities = $this->Contact->City->find('list');
        $this->set(compact('students', 'staffs', 'countries', 'regions', 'cities'));
    }

    public function edit($id = null)
    {

        if (!$id && empty($this->request->data)) {
            $this->Session->setFlash(__('Invalid contact'));
            return $this->redirect(array('action' => 'index'));
        }
        if (!empty($this->request->data)) {
            if ($this->Contact->save($this->request->data)) {
                $this->Session->setFlash(__('The contact has been saved'));
                return $this->redirect(array('action' => 'index'));
            } else {
                $this->Session->setFlash(__('The contact could not be saved. Please, try again.'));
            }
        }
        if (empty($this->request->data)) {
            $this->request->data = $this->Contact->read(null, $id);
        }
        $students = $this->Contact->Student->find('list');
        $staffs = $this->Contact->Staff->find('list');
        $countries = $this->Contact->Country->find('list');
        $regions = $this->Contact->Region->find('list');
        $cities = $this->Contact->City->find('list');
        $this->set(compact('students', 'staffs', 'countries', 'regions', 'cities'));
    }

    public function delete($id = null)
    {

        if (!$id) {
            $this->Session->setFlash(__('Invalid id for contact'));
            return $this->redirect(array('action' => 'index'));
        }
        if ($this->Contact->delete($id)) {
            $this->Session->setFlash(__('Contact deleted'));
            return $this->redirect(array('action' => 'index'));
        }
        $this->Session->setFlash(__('Contact was not deleted'));
        return $this->redirect(array('action' => 'index'));
    }
}
