<?php 
 class TransfersController extends AppController {
            var $name = 'Transfers';

            var $uses = array();
            var $menuOptions = array(
                'weight'=>-1000,
                    'exclude'=>array('index')
            );
           
            var $components = array('AcademicYear');
    
            function beforeRender() {
               
                $current_acyear=$this->AcademicYear->current_academicyear();
                $this->set(compact('current_acyear'));
                
	        }
	        
            function beforeFilter() {
                     parent::beforeFilter();        
            }
                   
            
            function index() {
            
            }
}
?>
