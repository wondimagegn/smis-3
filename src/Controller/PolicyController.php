<?php 
 class PolicyController extends AppController {
            var $name = 'Policy';
            var $uses = array();
            var $menuOptions = array(
                'weight'=>-1000,
                    'exclude'=>array('index')
            );
           
            
            function beforeFilter() {
                     parent::beforeFilter();        
            }
                   
            
            function index() {
            
            }
}
?>
