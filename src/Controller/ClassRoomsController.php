<?php

namespace App\Controller;

use App\Controller\AppController;
use Cake\Event\Event;
use Cake\ORM\TableRegistry;
use Cake\Core\Configure;

class ClassRoomsController extends AppController
{

    public $name = 'ClassRooms';
    public $menuOptions = array(
        'controllerButton' => false,
    );

    public function index()
    {

        $this->ClassRoom->recursive = 0;
        $this->set('classRooms', $this->paginate());
    }

    public function view($id = null)
    {

        if (!$id) {
            $this->Session->setFlash(__('Invalid class room'));
            return $this->redirect(array('action' => 'index'));
        }
        $this->set('classRoom', $this->ClassRoom->read(null, $id));
    }

    public function add()
    {

        if (!empty($this->request->data)) {
            $this->ClassRoom->create();
            if ($this->ClassRoom->save($this->request->data)) {
                $this->Session->setFlash(__('The class room has been saved'));
                return $this->redirect(array('action' => 'index'));
            } else {
                $this->Session->setFlash(__('The class room could not be saved. Please, try again.'));
            }
        }
        $classRoomBlocks = $this->ClassRoom->ClassRoomBlock->find('list');
        $this->set(compact('classRoomBlocks'));
    }

    public function edit($id = null)
    {

        if (!$id && empty($this->request->data)) {
            $this->Session->setFlash(__('Invalid class room'));
            return $this->redirect(array('action' => 'index'));
        }
        if (!empty($this->request->data)) {
            if ($this->ClassRoom->save($this->request->data)) {
                $this->Session->setFlash(__('The class room has been saved'));
                return $this->redirect(array('action' => 'index'));
            } else {
                $this->Session->setFlash(__('The class room could not be saved. Please, try again.'));
            }
        }
        if (empty($this->request->data)) {
            $this->request->data = $this->ClassRoom->read(null, $id);
        }
        $classRoomBlocks = $this->ClassRoom->ClassRoomBlock->find('list');
        $this->set(compact('classRoomBlocks'));
    }

    public function delete($id = null)
    {

        if (!$id) {
            $this->Session->setFlash(__('Invalid id for class room'));
            return $this->redirect(array('controller' => 'class_room_blocks', 'action' => 'view', $id));
        }
        //Before delete class room check this class room is not used in any constranint or schedule
        $is_ever_used = $this->ClassRoom->is_this_class_room_used_in_others_related_table($id);
        if ($is_ever_used == false) {
            if ($this->ClassRoom->delete($id)) {
                $this->Session->setFlash(__('Class room deleted'));
                return $this->redirect(array('controller' => 'class_room_blocks', 'action' => 'view', $id));
            }
            $this->Session->setFlash(__('Class room was not deleted'));
            return $this->redirect(array('controller' => 'class_room_blocks', 'action' => 'view', $id));
        } else {
            $error = $this->ClassRoom->invalidFields();
            if (isset($error['delete_class_rom'])) {
                $this->Session->setFlash(
                    '<span></span>' . __('You can not delete this class room, Since ' . $error['delete_class_rom'][0]),
                    'default',
                    array('class' => 'error-box error-message')
                );
                return $this->redirect(array('controller' => 'classRoomBlocks', 'action' => 'view', $id));
            }
        }
    }
}

?>
