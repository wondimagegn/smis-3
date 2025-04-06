<?php
namespace Acls\Controller;

use Acls\Controller\AclsAppController;
use Cake\Event\Event;
use Cake\Event\EventInterface;

class AclsController extends AclsAppController
{
    public function initialize(): void
    {
        parent::initialize();
        // No models used
    }

    public function beforeFilter(Event $event)
    {
        parent::beforeFilter($event);
        // $this->Auth->allow('*');
    }

    public function index()
    {
        return $this->redirect(['controller' => 'Acos', 'action' => 'index']);
    }
}
