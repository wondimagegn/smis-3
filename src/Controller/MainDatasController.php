<?php 
 class MainDatasController extends AppController {
            var $name = 'MainDatas';
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
