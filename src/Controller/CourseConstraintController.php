<?php 
  
    class CourseConstraintController extends AppController {
            var $name = "CourseConstraint";
            var $uses = array();
            var $menuOptions = array(
                'parent' => 'courseSchedule',
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