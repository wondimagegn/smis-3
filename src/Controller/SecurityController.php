<?php 
    
    class SecurityController extends AppController {
        public $name = "Security";
        public $uses = array();
        public $menuOptions = array(
                'exclude'=>array('index'),
                 'weight'=>-1000000,
        );
        public function beforeFilter(){
            parent::beforeFilter();

        }

        public function index() {

        }
   
	}		
?>
