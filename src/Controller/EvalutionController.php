<?php

namespace App\Controller;

use App\Controller\AppController;

use Cake\Event\Event;
use Cake\ORM\TableRegistry;
use Cake\Core\Configure;
class EvalutionController extends AppController
{
    public $name = "Evalution";
    public $uses = array();
    public $menuOptions = array(
        'exclude' => array('index'),
        'weight' => -10000000,
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



    public function index()
    {

    }
}
