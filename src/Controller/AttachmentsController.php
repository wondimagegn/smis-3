<?php
namespace App\Controller;

use Cake\Event\Event;
use Cake\ORM\TableRegistry;
use Cake\Core\Configure;

class AttachmentsController extends AppController
{

    public $name = 'Attachments';

    public $menuOptions = array(
        'controllerButton' => false,
        'exclude' => array('*'),
    );

    public function beforeFilter(Event $event)
    {

        parent::beforeFilter($event);
        $this->Auth->allowedActions = array('delete');
    }

    public function index()
    {

        $this->Attachment->recursive = 0;
        $this->set('attachments', $this->paginate());
    }

    public function view($id = null)
    {

        if (!$id) {
            $this->Flash->error(__('Invalid attachment'));
            return $this->redirect(array('action' => 'index'));
        }

        $this->set('attachment', $this->Attachment->read(null, $id));
    }

    public function add()
    {

        if (!empty($this->request->data)) {
            $this->Attachment->create();
            if ($this->Attachment->save($this->request->data)) {
                $this->Flash->success(__('The attachment has been saved'));
                return $this->redirect(array('action' => 'index'));
            } else {
                $this->Flash->error(__('The attachment could not be saved. Please, try again.'));
            }
        }
    }

    public function edit($id = null)
    {

        if (!$id && empty($this->request->data)) {
            $this->Flash->error(__('Invalid attachment'));
            return $this->redirect(array('action' => 'index'));
        }

        if (!empty($this->request->data)) {
            if ($this->Attachment->save($this->request->data)) {
                $this->Flash->success(__('The attachment has been saved'));
                return $this->redirect(array('action' => 'index'));
            } else {
                $this->Flash->error(__('The attachment could not be saved. Please, try again.'));
            }
        }

        if (empty($this->request->data)) {
            $this->request->data = $this->Attachment->read(null, $id);
        }
    }

    public function delete($id = null, $action_controller_id = null)
    {

        $attachment_detail = explode('~', $action_controller_id);

        if (!$id) {
            $this->Flash->error(__('Invalid Attachment ID'));
            if (!empty($attachment_detail[0]) && !empty($attachment_detail[1]) && !empty($attachment_detail[2])) {
                return $this->redirect(
                    array(
                        'controller' => $attachment_detail[1],
                        'action' => $attachment_detail[0],
                        $attachment_detail[2]
                    )
                );
            }
        }


        if ($this->Attachment->delete($id)) {
            $this->Flash->success(__('Attachment deleted successfully.'));
            if (!empty($attachment_detail[0]) && !empty($attachment_detail[1]) && !empty($attachment_detail[2])) {
                $this->redirect(
                    array(
                        'controller' => $attachment_detail[1],
                        'action' => $attachment_detail[0],
                        $attachment_detail[2]
                    )
                );
            }
        }

        if (!empty($attachment_detail[0]) && !empty($attachment_detail[1]) && !empty($attachment_detail[2])) {
            $this->Flash->error(__('Attachment can not be deleted.'));
            $this->redirect(
                array('controller' => $attachment_detail[1], 'action' => $attachment_detail[0], $attachment_detail[2])
            );
        }
    }
}
