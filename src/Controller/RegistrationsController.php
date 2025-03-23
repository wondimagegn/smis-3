<?php

namespace App\Controller;

use App\Controller\AppController;


use Cake\Event\Event;
use Cake\ORM\TableRegistry;
use Cake\Core\Configure;
 class RegistrationsController extends AppController {
            public $name = 'Registrations';

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

            public function beforeRender(Event $event) {

                $current_acyear=$this->AcademicYear->currentAcademicYear();
                $this->set(compact('current_acyear'));

	    }



            public function index() {
               return $this->redirect(array('controller'=>'courseRegistrations','action' => 'index'));
            }
}
?>
