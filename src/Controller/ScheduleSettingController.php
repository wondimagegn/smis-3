<?php

namespace App\Controller;

use App\Controller\AppController;

use Cake\Event\Event;
use Cake\ORM\TableRegistry;
use Cake\Core\Configure;
    class ScheduleSettingController extends AppController {
            var $name = "ScheduleSetting";
            var $uses = array();
            var $menuOptions = array(
                'parent' => 'schedule',
                'exclude'=>array('index'),
                //'weight'=>-10000000,
            );
		function beforeFilter(Event $event){
        parent::beforeFilter($event);
         //$this->Auth->allow(array('*'));
         //$this->Auth->allow('index');
		}

		function index(){

		}

    }
?>
