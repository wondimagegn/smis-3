<?php
namespace App\Controller;

use Cake\Event\Event;

class DormitoryBlocksController extends AppController
{

    public $name = 'DormitoryBlocks';
    public $menuOptions = array(
        'parent' => 'dormitory',
        'exclude' => array('deleteDormitory'),
        'alias' => array(
            'index' => 'List Dormitories Blocks with Dormitories',
            'add' => 'Add Dormitories Block/Dormitories '
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

        $this->Auth->allow('deleteDormitory');
    }

    public function index()
    {

        $campuses = $this->DormitoryBlock->Campus->find('list');
        $this->set(compact('campuses'));

        $this->DormitoryBlock->recursive = 0;
        $campus = '%';
        if (!empty($this->request->data['DormitoryBlock']['campus_id'])) {
            $campus = $this->request->data['DormitoryBlock']['campus_id'];
        }

        $conditions = array('DormitoryBlock.campus_id LIKE' => $campus);
        $this->paginate = array('conditions' => $conditions);
        $this->set('dormitoryBlocks', $this->paginate());
    }

    public function view($id = null)
    {

        if (!$id) {
            $this->Session->setFlash(__('Invalid dormitory block'));
            return $this->redirect(array('action' => 'index'));
        }
        $this->set('dormitoryBlock', $this->DormitoryBlock->read(null, $id));
    }

    public function add()
    {

        if (!empty($this->request->data)) {
            $is_dormitory_block_duplicated = 0;
            $is_dormitory_block_duplicated = $this->DormitoryBlock->find(
                'count',
                array(
                    'conditions' => array(
                        'DormitoryBlock.campus_id' => $this->request->data['DormitoryBlock']['campus_id'],
                        'DormitoryBlock.block_name' => $this->request->data['DormitoryBlock']['block_name']
                    )
                )
            );
            if ($is_dormitory_block_duplicated == 0) {
                $this->set($this->request->data);
                $this->DormitoryBlock->create();
                if ($this->DormitoryBlock->saveAll($this->request->data, array('validate' => 'first'))) {
                    $this->Session->setFlash(
                        '<span></span>' . __('The dormitory block has been saved'),
                        'default',
                        array('class' => 'success-box success-message')
                    );
                    return $this->redirect(array('action' => 'index'));
                } else {
                    $this->Session->setFlash(
                        '<span></span>' . __('The dormitory block could not be saved. Please, try again.'),
                        'default',
                        array('class' => 'error-box error-message')
                    );
                }
            } else {
                $this->Session->setFlash(
                    '<span></span> The dormitory block is available in this campus. Please, provide unique dormitory block or edit the dormitory block in ',
                    "session_flash_link",
                    array(
                        "class" => 'info-box info-message',
                        "link_text" => " this page",
                        "link_url" => array(
                            "controller" => "dormitory_blocks",
                            "action" => "index",
                            "admin" => false
                        )
                    )
                );
            }
        }
        $campuses = $this->DormitoryBlock->Campus->find('list');
        $floor_data = $this->DormitoryBlock->get_floor_data(10);
        $this->set(compact('campuses', 'floor_data'));
    }

    public function edit($id = null)
    {

        if (!$id && empty($this->request->data)) {
            $this->Session->setFlash(
                '<span></span>' . __('Invalid dormitory block'),
                'default',
                array('class' => 'error-box error-message')
            );
            return $this->redirect(array('action' => 'index'));
        }
        if (!empty($this->request->data)) {
            if ($this->DormitoryBlock->saveAll($this->request->data, array('validate' => 'first'))) {
                $this->Session->setFlash(
                    '<span></span>' . __('The dormitory block has been saved'),
                    'default',
                    array('class' => 'success-box success-message')
                );
                return $this->redirect(array('action' => 'index'));
            } else {
                $this->Session->setFlash(
                    '<span></span>' . __('The dormitory block could not be saved. Please, try again.'),
                    'default',
                    array('class' => 'error-box error-message')
                );
            }
        }
        if (empty($this->request->data)) {
            $this->request->data = $this->DormitoryBlock->read(null, $id);
        }
        $campuses = $this->DormitoryBlock->Campus->find('list');
        $floor_data = $this->DormitoryBlock->get_floor_data(10);
        $this->set(compact('campuses', 'floor_data'));
    }

    public function delete($id = null)
    {

        if (!$id) {
            $this->Session->setFlash(
                '<span></span>' . __('Invalid id for dormitory block'),
                'default',
                array('class' => 'error-box error-message')
            );
            return $this->redirect(array('action' => 'index'));
        }
        //Before Deleting the class room block check whether the block have class room or not
        //If it have class rooms, deny deletion of class room block
        $dormitory_count = 0;
        $dormitory_count = $this->DormitoryBlock->Dormitory->find('count', array(
            'conditions' =>
                array('Dormitory.dormitory_block_id' => $id)
        ));
        if ($dormitory_count == 0) {
            if ($this->DormitoryBlock->delete($id)) {
                $this->Session->setFlash(
                    '<span></span>' . __('Dormitory block deleted'),
                    'default',
                    array('class' => 'success-box success-message')
                );
                return $this->redirect(array('action' => 'index'));
            }
        } else {
            $this->Session->setFlash(
                '<span></span> ' . __(
                    'The dormitory block can not be delete since it have ' . $dormitory_count . ' dormitories under the block, Please delete the dormitories first.'
                ),
                'default',
                array('class' => 'error-box error-message')
            );
            return $this->redirect(array('action' => 'index'));
        }
        $this->Session->setFlash(
            '<span></span>' . __('Dormitory block was not deleted'),
            'default',
            array('class' => 'error-box error-message')
        );
        return $this->redirect(array('action' => 'index'));
    }

    public function deleteDormitory($id = null)
    {

        if (!$id) {
            $this->Session->setFlash(
                '<span></span>' . __('Invalid id for Dormitory'),
                'default',
                array('class' => 'error-box error-message')
            );
            return $this->redirect(array('action' => 'index'));
        }
        //Before delete dormitory check this dormitory is not ever been used in dormitory assignment
        $is_dormitory_ever_used = $this->DormitoryBlock->Dormitory->DormitoryAssignment->is_dormitory_ever_used($id);
        if ($is_dormitory_ever_used == false) {
            if ($this->DormitoryBlock->Dormitory->delete($id)) {
                $this->Session->setFlash(
                    '<span></span> ' . __('The Dormitory deleted'),
                    'default',
                    array('class' => 'success-box success-message')
                );
                return $this->redirect(array('action' => 'index'));
            }
        } else {
            $this->Session->setFlash(
                '<span></span> ' . __('The dormitory can not be delete since it used in dormitory assignment.'),
                'default',
                array('class' => 'error-box error-message')
            );
            return $this->redirect(array('action' => 'index'));
        }
        $this->Session->setFlash('<span></span> ' . __('The Dormitory was not deleted'), 'default', array(
            'class' => 'error-box error-message'
        ));
        return $this->redirect(array('action' => 'index'));
    }
}
