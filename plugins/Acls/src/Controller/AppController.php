<?php

namespace Acls\Controller;

use Cake\Controller\Controller;

class AppController extends Controller
{
    /**
     * Initialization method
     */
    public function initialize(): void
    {
        parent::initialize();

        // ✅ Load commonly used components
        $this->loadComponent('RequestHandler');
        $this->loadComponent('Flash');

    }
}
