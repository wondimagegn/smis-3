<?php

namespace App\Controller;

use App\Controller\AppController;

use Cake\Event\Event;
use Cake\ORM\TableRegistry;
use Cake\Core\Configure;
class WebServicesController extends AppController {

   var $name = 'Webservices';
   var $uses = array();
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
        $this->Auth->Allow('index','helloWorld');
    }

   function index (){

   //code here
        $server = new soap_server;
        $server->register('helloWorld');
        $HTTP_RAW_POST_DATA = isset($HTTP_RAW_POST_DATA) ? $HTTP_RAW_POST_DATA : '';
        $server->service($HTTP_RAW_POST_DATA);


   }
   function helloWorld($params) {

     return $params;
   }

}

?>
