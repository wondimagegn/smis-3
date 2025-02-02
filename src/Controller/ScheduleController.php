<?php 
  
    class ScheduleController extends AppController {
            public $name = "Schedule";
            public $uses = array();
            public $menuOptions = array(
                
                'exclude'=>array('index'),
                'weight'=>2000000,
            );
			
	    public function beforeFilter(){
        parent::beforeFilter();
         
         $this->Auth->allow('index');  
		}
		
		public function index(){
		
		}
    
    }
?>
