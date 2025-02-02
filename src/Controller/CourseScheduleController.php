<?php 
  
    class CourseScheduleController extends AppController {
            var $name = "CourseSchedule";
            var $uses = array();
            var $menuOptions = array(
                'parent' => 'schedule',
                'exclude'=>array('index'),
                //'weight'=>-10000000,
            );
		function beforeFilter(){
                 parent::beforeFilter();
		}
		
		function index(){
		
		}    
    
    }
?>
