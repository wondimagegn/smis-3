<?php 
 class RegistrationsController extends AppController {
            public $name = 'Registrations';

            public $uses = array();
            public $menuOptions = array(
                'weight'=>-1000,
                    'exclude'=>array('index')
            );
           
            public $components = array('AcademicYear');
    
            public function beforeRender() {
               
                $current_acyear=$this->AcademicYear->current_academicyear();
                $this->set(compact('current_acyear'));
                
	    }
	        
            public function beforeFilter() {
                     parent::beforeFilter();        
            }
                   
            
            public function index() {
               return $this->redirect(array('controller'=>'courseRegistrations','action' => 'index'));
            }
}
?>
