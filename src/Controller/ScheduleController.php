<?php

namespace App\Controller;

use App\Controller\AppController;

use Cake\Event\Event;
use Cake\ORM\TableRegistry;
use Cake\Core\Configure;

    class ScheduleController extends AppController {
            public $name = "Schedule";
            public $uses = array();
            public $menuOptions = array(

                'exclude'=>array('index'),
                'weight'=>2000000,
            );

	    public function beforeFilter(Event $event) {
        parent::beforeFilter($event);

         $this->Auth->allow('index');
		}

		public function index(){

		}

    }
?>
