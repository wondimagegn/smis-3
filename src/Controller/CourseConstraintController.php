<?php

namespace App\Controller;

use Cake\Event\Event;
use Cake\ORM\TableRegistry;
use Cake\Core\Configure;

    class CourseConstraintController extends AppController {
            var $name = "CourseConstraint";
            var $uses = array();
            var $menuOptions = array(
                'parent' => 'courseSchedule',
                'exclude'=>array('index'),
                //'weight'=>-10000000,
            );
		function beforeFilter(Event $event){
            parent::beforeFilter($event);
		}

		function index(){

		}

    }
?>
