<?php

namespace App\Controller;

use App\Controller\AppController;


use Cake\Event\Event;
use Cake\ORM\TableRegistry;
use Cake\Core\Configure;
    class ExamConstraintController extends AppController {
            var $name = "ExamConstraint";
            var $uses = array();
            var $menuOptions = array(
                'parent' => 'examSchedule',
                'exclude'=>array('index'),
                //'weight'=>-10000000,
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

		function index(){

		}

    }
?>
