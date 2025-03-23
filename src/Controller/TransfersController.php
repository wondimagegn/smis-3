<?php

namespace App\Controller;

use App\Controller\AppController;

use Cake\Event\Event;
use Cake\ORM\TableRegistry;
use Cake\Core\Configure;
 class TransfersController extends AppController {
            public $name = 'Transfers';

            public $uses = array();
            public $menuOptions = array(
                'weight'=>-1000,
                    'exclude'=>array('index')
            );

     public $paginate =[];
     public function initialize()
     {
         parent::initialize();
         $this->loadComponent('AcademicYear');
         $this->loadComponent('Paginator'); // Ensure Paginator is loaded

     }

     public function beforeFilter(Event $event)
     {
         parent::beforeFilter($event);
     }

            function beforeRender(Event $event) {
                parent::beforeRender($event);
                $current_acyear=$this->AcademicYear->currentAcademicYear();
                $this->set(compact('current_acyear'));

	        }


            function index() {

            }
}
?>
