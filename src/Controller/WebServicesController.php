<?php 
App::import('Vendor','nusoap');
class WebServicesController extends AppController {
  
   var $name = 'Webservices';
   var $uses = array();
   function beforeFilter () {
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
