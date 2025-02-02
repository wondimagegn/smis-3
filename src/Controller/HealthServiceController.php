<?php 
  
    class HealthServiceController extends AppController {
            var $name = "HealthService";
            var $uses = array();
            var $menuOptions = array(
                
                'exclude'=>array('index'),
                //'weight'=>-10000000,
            );
			
	    function beforeFilter(){
        parent::beforeFilter();
         //$this->Auth->allow(array('*'));
         $this->Auth->allow('index');  
		}
		
		function index(){
		
		}
    
    }
?>
