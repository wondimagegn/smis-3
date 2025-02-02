<?php 
  
    class ScheduleSettingController extends AppController {
            var $name = "ScheduleSetting";
            var $uses = array();
            var $menuOptions = array(
                'parent' => 'schedule',
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