<?php
namespace app\Controller\Component;

use Cake\Controller\Component;
use Cake\Controller\Controller;
use Cake\Core\Configure;
class TicketmasterComponent extends Component{
	public $sitename='Arba Minch University Student Management Information System.';
	//var $linkdomain='smis.dev';
	//how many hours to honor token
	public $hours=24;

	public function __construct(ComponentRegistry $collection,$settings = array()) {
		parent::__construct($collection, $settings);
	}
	public function initialize(Controller $controller) {
	       
	}
        public function shutdown(Controller $controller) {

	}
    	/*
     	*  Startup - Link the component to the controller.
     	*/ 
    	function startup(Controller $controller)
    	{
		$this->controller = $controller;    	
    	}	
	function getExpirationDate(){
		$date=strftime('%c');
		$date=strtotime($date);
		$date+=($this->hours*60*60);
		$expired=date('Y-m-d H:i:s',$date);
		return $expired;
 
	}
 
	function createMessage($token){
        $url = Configure::read('SMIS.url');
		$ms='Password Reset Request';
		$ms=' Your email has been used in a password reset request at '.$this->sitename.'';
		$ms.=' If you did not initiate this request, you can safely ignore this email.';
		$ms.='  <br/> ';
		$ms.='<a href="http://'.$url.'/users/useticket/'.$token.'">Click here to Reset Password</a>';
		$ms.='  <br/> ';
 
		$ms=wordwrap($ms,70);
 
		return $ms;
 
	}
 
	function purgeTickets(){
		$this->controller->Ticket->deleteAll(array('Ticket.expires <='=> Date('Y-m-d h:i:s')));
 
	}	
 
	/*
	 * clean ALL ticks for this email
	 */
	function voidTicket($hash){
		$this->controller->Ticket->deleteAll(array('hash' => $hash));
	}
 
	function checkTicket($hash){
		$this->purgeTickets();
		$ret=false;
		$tick=$this->controller->Ticket->findByHash($hash);
 
		if(empty($tick)){
			//no more ticket			
		}else{
			$ret=$tick;
		}
		return $ret;
	}
	/**
	 * BeforeRender Callback.
	 *
	 */
	public function beforeRender(Controller $controller) {
	    
		
	}
}
?>
