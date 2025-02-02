<?php
/**
 * Attempt Component Class
 * 
 * Based on http://bakery.cakephp.org/articles/aep_/2006/11/04/brute-force-protection
 * 
 * @author Thomas Heymann
 * @version	0.1
 * @license	http://www.opensource.org/licenses/mit-license.php The MIT License
 * @package app
 * @subpackage app.controllers.components
 **/
App::uses('Component', 'Controller');
App::uses('Router', 'Routing');
App::uses('CakeSession', 'Model/Datasource');

class AttemptComponent extends Component
{

	var $components = array(
		'RequestHandler'
	);

	// Called before the Controller::beforeFilter().
	// function initialize(&$controller, $options) {
	// }

	public function __construct(ComponentCollection $collection, $settings = array())
	{
		parent::__construct($collection, $settings);
	}

	// Called after the Controller::beforeFilter() and before the controller action
	function startup(Controller $controller)
	{
		$this->controller = $controller;
		$this->Attempt = ClassRegistry::init('Attempt');
	}

	public function count($username, $action)
	{
		return $this->Attempt->count($this->RequestHandler->getClientIP(), $username, $action);
	}

	public function limit($username, $action, $limit = 5)
	{
		return $this->Attempt->limit($this->RequestHandler->getClientIP(), $username, $action, $limit);
	}

	public function fail($username, $action, $duration = '+10 minutes')
	{
		return $this->Attempt->fail($this->RequestHandler->getClientIP(), $username, $action, $duration);
	}

	public function reset($action, $username)
	{
		return $this->Attempt->reset($this->RequestHandler->getClientIP(), $username, $action);
	}

	public function cleanup()
	{
		return $this->Attempt->cleanup();
	}
}
?>