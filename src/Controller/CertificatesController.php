<?php 
class CertificatesController extends AppController {
	var $name = "Certificates";
	var $uses = array();
	var $menuOptions = array(
		'parent' => 'graduation',
		'weight'=>3,
		'exclude' => array('index'),
	);
	
	function index() {
		
	}
}
?>
