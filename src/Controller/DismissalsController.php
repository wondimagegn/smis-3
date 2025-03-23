<?php

namespace App\Controller;

use App\Controller\AppController;

use Cake\Event\Event;
use Cake\ORM\TableRegistry;
use Cake\Core\Configure;

class DismissalsController extends AppController
{

    public $name = 'Dismissals';


    public function index()
    {

        $this->Dismissal->recursive = 0;
        $this->set('dismissals', $this->paginate());
    }

    public function view($id = null)
    {

        if (!$id) {
            $this->Session->setFlash(__('Invalid dismissal'));
            return $this->redirect(array('action' => 'index'));
        }
        $this->set('dismissal', $this->Dismissal->read(null, $id));
    }

    public function add()
    {

        if (!empty($this->request->data)) {
            $this->Dismissal->create();
            if ($this->Dismissal->save($this->request->data)) {
                $this->Session->setFlash(__('The dismissal has been saved'));
                return $this->redirect(array('action' => 'index'));
            } else {
                $this->Session->setFlash(__('The dismissal could not be saved. Please, try again.'));
            }
        }
        $students = $this->Dismissal->Student->find('list');
        $this->set(compact('students'));
    }

    public function edit($id = null)
    {

        if (!$id && empty($this->request->data)) {
            $this->Session->setFlash(__('Invalid dismissal'));
            return $this->redirect(array('action' => 'index'));
        }
        if (!empty($this->request->data)) {
            if ($this->Dismissal->save($this->request->data)) {
                $this->Session->setFlash(__('The dismissal has been saved'));
                return $this->redirect(array('action' => 'index'));
            } else {
                $this->Session->setFlash(__('The dismissal could not be saved. Please, try again.'));
            }
        }
        if (empty($this->request->data)) {
            $this->request->data = $this->Dismissal->read(null, $id);
        }
        $students = $this->Dismissal->Student->find('list');
        $this->set(compact('students'));
    }

    public function delete($id = null)
    {

        if (!$id) {
            $this->Session->setFlash(__('Invalid id for dismissal'));
            return $this->redirect(array('action' => 'index'));
        }
        if ($this->Dismissal->delete($id)) {
            $this->Session->setFlash(__('Dismissal deleted'));
            return $this->redirect(array('action' => 'index'));
        }
        $this->Session->setFlash(__('Dismissal was not deleted'));
        return $this->redirect(array('action' => 'index'));
    }
}
