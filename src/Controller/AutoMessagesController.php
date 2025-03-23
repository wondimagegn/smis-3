<?php

namespace App\Controller;

use App\Controller\AppController;
use Cake\Event\Event;
use Cake\ORM\TableRegistry;
use Cake\Core\Configure;

class AutoMessagesController extends AppController
{

    public $name = 'AutoMessages';
    public $menuOptions = array(
        'controllerButton' => false,
        'exclude' => array('*')
    );
    public function initialize()
    {
        parent::initialize();
        $this->loadComponent('RequestHandler');
    }


    public function beforeFilter(Event $event)
    {
        parent::beforeFilter($event);
        $this->Auth->Allow('markAsUnread');

        if ($this->request->getSession()->check('Message.auth')) {
            $this->request->getSession()->delete('Message.auth');
        }

        if ($this->Auth->user() && $this->request->getParam('action') === 'login') {
            return $this->redirect($this->Auth->logout());
        }
    }



    public function markAsUnread($id = null)
    {
        $this->request->allowMethod(['post', 'put']);
        $this->viewBuilder()->setClassName('Json');

        try {

            $message = $this->AutoMessages->get($id);
            $message->is_read = 1;

            if ($this->AutoMessages->save($message)) {
                $autoMessages = $this->AutoMessages->getMessages($this->Auth->user('id'));
                $response = [
                    'status' => 'success',
                    'auto_messages' => $autoMessages
                ];
            } else {
                $response = [
                    'status' => 'error',
                    'message' => __('The auto message could not be updated.'),
                    'errors' => $message->getErrors()
                ];
            }
        } catch (\Exception $e) {
            $response = [
                'status' => 'error',
                'message' => $e->getMessage()
            ];
        }
        $this->set($response);
        $this->set('_serialize', array_keys($response));
    }
}
