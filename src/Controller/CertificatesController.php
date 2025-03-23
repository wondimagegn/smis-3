<?php

namespace App\Controller;

use App\Controller\AppController;
use Cake\Event\Event;
use Cake\ORM\TableRegistry;
use Cake\Core\Configure;
class CertificatesController extends AppController {
	var $name = "Certificates";
	var $uses = array();
	var $menuOptions = array(
		'parent' => 'graduation',
		'weight'=>3,
		'exclude' => array('index'),
	);
    public function beforeFilter(Event $event)
    {
        parent::beforeFilter($event);

    }

	function index() {

	}
}
?>
