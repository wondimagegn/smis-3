<?php

namespace App\Controller;

class DormitoriesController extends AppController
{

    public $name = 'Dormitories';

    public $menuOptions = array(
        'exclude' => array('*'),
        'controllerButton' => false
    );

    public function index()
    {

        $this->Dormitory->recursive = 0;
        $this->set('dormitories', $this->paginate());
    }

    public function view($id = null)
    {

        if (!$id) {
            $this->Session->setFlash(__('Invalid dormitory'));
            return $this->redirect(array('action' => 'index'));
        }
        $this->set('dormitory', $this->Dormitory->read(null, $id));
    }

    public function add()
    {

        if (!empty($this->request->data)) {
            $this->Dormitory->create();
            if ($this->Dormitory->save($this->request->data)) {
                $this->Session->setFlash(__('The dormitory has been saved'));
                return $this->redirect(array('action' => 'index'));
            } else {
                $this->Session->setFlash(__('The dormitory could not be saved. Please, try again.'));
            }
        }
        $dormitoryBlocks = $this->Dormitory->DormitoryBlock->find('list');
        $this->set(compact('dormitoryBlocks'));
    }

    public function edit($id = null)
    {

        if (!$id && empty($this->request->data)) {
            $this->Session->setFlash(
                '<span></span>' . __('Invalid dormitory'),
                'default',
                array('class' => 'error-box error-message')
            );
            return $this->redirect(array('action' => 'index'));
        }
        if (!empty($this->request->data)) {
            if ($this->Dormitory->save($this->request->data)) {
                $this->Session->setFlash(
                    '<span></span>' . __('The dormitory has been saved'),
                    'default',
                    array('class' => 'success-box success-message')
                );
                return $this->redirect(
                    array(
                        'controller' => 'dormitory_blocks',
                        'action' => 'view',
                        $this->request->data['Dormitory']['dormitory_block_id']
                    )
                );
            } else {
                $this->Session->setFlash(
                    '<span></span>' . __('The dormitory could not be saved. Please, try again.'),
                    'default',
                    array('class' => 'error-box error-message')
                );
            }
        }
        if (empty($this->request->data)) {
            $this->request->data = $this->Dormitory->read(null, $id);
        }
        $dormitoryBlocks = $this->Dormitory->DormitoryBlock->find('list');
        $floor_data = $this->Dormitory->DormitoryBlock->get_floor_data(10);
        $this->set(compact('dormitoryBlocks', 'floor_data'));
    }

    public function delete($id = null)
    {

        if (!$id) {
            $this->Session->setFlash(
                '<span></span>' . __('Invalid id for dormitory'),
                'default',
                array('class' => 'error-box error-message')
            );
            return $this->redirect(array('action' => 'index'));
        }
        $dormitory_block_id = $this->Dormitory->field('Dormitory.dormitory_block_id', array('Dormitory.id' => $id));
        //Before delete dormitory check is every the dormitory have been used or not
        $is_dormitory_ever_used = $this->Dormitory->DormitoryAssignment->is_dormitory_ever_used($id);
        if ($is_dormitory_ever_used == false) {
            if ($this->Dormitory->delete($id)) {
                $this->Session->setFlash(
                    '<span></span>' . __('Dormitory deleted'),
                    'default',
                    array('class' => 'success-box success-message')
                );
                return $this->redirect(
                    array('controller' => 'dormitory_blocks', 'action' => 'view', $dormitory_block_id)
                );
            }
        } else {
            $this->Session->setFlash(
                '<span></span> ' . __('The dormitory can not be delete since it used in dormitory assignment.'),
                'default',
                array('class' => 'error-box error-message')
            );
            return $this->redirect(array('controller' => 'dormitory_blocks', 'action' => 'view', $dormitory_block_id));
        }
        $this->Session->setFlash(
            '<span></span>' . __('Dormitory was not deleted'),
            'default',
            array('class' => 'error-box error-message')
        );
        return $this->redirect(array('controller' => 'dormitory_blocks', 'action' => 'view', $dormitory_block_id));
    }
}
