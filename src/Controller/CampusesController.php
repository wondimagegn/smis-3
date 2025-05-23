<?php
namespace App\Controller;

use Cake\Event\Event;
use Cake\ORM\TableRegistry;
use Cake\Core\Configure;

class CampusesController extends AppController
{

    public $name = 'Campuses';
    public $menuOptions = array(
        'parent' => 'mainDatas',
        'exclude' => array('index'),
        'weight' => -2,
        'alias' => array(
            'add' => 'Add Campus',
        )
    );

    public function index()
    {

        $this->Campus->recursive = 0;
        $this->set('campuses', $this->paginate());
    }

    public function view($id = null)
    {

        if (!$id) {
            $this->Session->setFlash(
                '<span></span>' . __('Invalid campus'),
                'default',
                array('class' => 'error-box error-message')
            );
            return $this->redirect(array('action' => 'index'));
        }
        $this->set('campus', $this->Campus->read(null, $id));
    }

    public function add()
    {

        $this->set($this->request->data);
        if (!empty($this->request->data)) {
            $this->Campus->create();
            if ($this->Campus->save($this->request->data)) {
                $this->Session->setFlash(
                    '<span></span>' . __('The campus has been saved'),
                    'default',
                    array('class' => 'success-box success-message')
                );

                return $this->redirect(array('action' => 'index'));
            } else {
                $this->Session->setFlash(
                    '<span></span>' . __('The campus could not be saved. Please, try again.'),
                    'default',
                    array('class' => 'error-box error-message')
                );
            }
        }
    }

    public function edit($id = null)
    {

        if (!$id && empty($this->request->data)) {
            $this->Session->setFlash(
                '<span></span>' . __('Invalid campus'),
                'default',
                array('class' => 'error-box error-message')
            );
            return $this->redirect(array('action' => 'index'));
        }
        if (!empty($this->request->data)) {
            if ($this->Campus->save($this->request->data)) {
                $this->Session->setFlash(
                    '<span></span>' . __('The campus has been saved'),
                    'default',
                    array('class' => 'success-box success-message')
                );
                return $this->redirect(array('action' => 'index'));
            } else {
                $this->Session->setFlash(
                    '<span></span>' .
                    __('The campus could not be saved. Please, try again.'),
                    'default',
                    array('class' => 'error-box error-message')
                );
            }
        }
        if (empty($this->request->data)) {
            $this->request->data = $this->Campus->read(null, $id);
        }
    }

    public function delete($id = null)
    {

        if (!$id) {
            $this->Session->setFlash(__('Invalid id for campus'));
            return $this->redirect(array('action' => 'index'));
        }
        if ($this->Campus->delete($id)) {
            $this->Session->setFlash(
                '<span></span>' . __('Campus deleted'),
                'default',
                array('class' => 'success-box success-message')
            );
            return $this->redirect(array('action' => 'index'));
        }
        $this->Session->setFlash(
            '<span></span>' . __('Campus was not deleted'),
            'default',
            array('class' => 'error-box error-message')
        );
        return $this->redirect(array('action' => 'index'));
    }
}
