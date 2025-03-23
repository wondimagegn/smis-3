<?php

namespace App\Controller;

use App\Controller\AppController;


use Cake\Event\Event;
use Cake\ORM\TableRegistry;
use Cake\Core\Configure;

class DormitoryController extends AppController {
        public $name = "Dormitory";
        public $uses = array();
        public $menuOptions = array(

            'exclude'=>array('index'),
            //'weight'=>-10000000,
        );

	    function beforeFilter(Event $event) {
            parent::beforeFilter($event);
             //$this->Auth->allow(array('*'));
             $this->Auth->allow('index');
		}

		function index(){

		}

}
?>
