<?php

namespace App\Controller;

use App\Controller\AppController;

use Cake\Event\Event;
use Cake\ORM\TableRegistry;
use Cake\Core\Configure;
    class CourseScheduleController extends AppController {
            public $name = "CourseSchedule";
            public $uses = array();
            public $menuOptions = array(
                'parent' => 'schedule',
                'exclude'=>array('index'),
                //'weight'=>-10000000,
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

		function index(){

		}

    }
?>
