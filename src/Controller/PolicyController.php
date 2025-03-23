<?php

namespace App\Controller;

use App\Controller\AppController;

use Cake\Event\Event;
use Cake\ORM\TableRegistry;
use Cake\Core\Configure;
 class PolicyController extends AppController {
            public $name = 'Policy';
            public $uses = array();
            public $menuOptions = array(
                'weight'=>-1000,
                    'exclude'=>array('index')
            );


     public $paginate =[];
     public function initialize()
     {
         parent::initialize();
         $this->loadComponent('Paginator'); // Ensure Paginator is loaded

     }

     public function beforeFilter(Event $event)
     {
         parent::beforeFilter($event);
     }

            function index() {

            }
}
?>
