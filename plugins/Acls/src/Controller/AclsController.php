<?php
namespace Acls\Controller;

use Acls\Controller\AppController; // Correctly reference the plugin's AppController
use Cake\Http\Response;

class AclsController extends AppController
{
    /**
     * Initialization method
     */
    public function initialize(): void
    {
        parent::initialize();

        // ✅ Load Components if needed
        $this->loadComponent('Auth');
    }

    /**
     * beforeFilter callback
     *
     * @param \Cake\Event\EventInterface $event
     */
    public function beforeFilter(\Cake\Event\EventInterface $event)
    {
        parent::beforeFilter($event);

        // ✅ Allow all actions (optional, uncomment if needed)
        // $this->Auth->allow();

    }

    /**
     * Index method
     *
     * @return \Cake\Http\Response|null
     */
    public function index(): ?Response
    {
        // ✅ Redirect to AcosController index action
        return $this->redirect(['controller' => 'Acos', 'action' => 'index']);
    }
}
