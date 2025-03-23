<?php

namespace App\Controller;

use App\Controller\AppController;

use Cake\Event\Event;
use Cake\ORM\TableRegistry;
use Cake\Core\Configure;

    class SecurityController extends AppController {
        public $name = "Security";
        public $uses = array();
        public $menuOptions = array(
                'exclude'=>array('index'),
                 'weight'=>-1000000,
        );
        public function beforeFilter(Event $event) {
            parent::beforeFilter($event);

        }

        public function index() {

        }

	}
?>
