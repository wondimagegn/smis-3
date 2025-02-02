<?php 
    class ExamConstraintViewController extends AppController {
            var $name = "ExamConstraintView";
            var $uses = array();
            var $menuOptions = array(
                'parent' => 'examSchedule',
                'exclude'=>array('index'),
                //'weight'=>-10000000,
            );
		function beforeFilter(){
        parent::beforeFilter();
         //$this->Auth->allow(array('*'));
         //$this->Auth->allow('index');  
		}
		
		function index(){
		
		}    
    
    }
?>
