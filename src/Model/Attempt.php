<?php
class Attempt extends AppModel
{
	var $name = 'Attempt';
	var $displayField = 'ip';

	public function count($ip, $username, $action)
	{
		return $this->find('count', array(
			'conditions' => array(
				'ip' => $ip,
				'username' => $username,
				'action' => $action,
				'expires >' => date('Y-m-d H:i:s')
			)
		));
	}

	public function limit($ip, $username, $action, $limit)
	{
		return ($this->count($ip, $username, $action) < $limit);
	}

	public function fail($ip, $username, $action, $duration)
	{
		$this->create(array('ip' => $ip, 'username' => $username, 'action' => $action, 'expires' => date('Y-m-d H:i:s', strtotime($duration))));
		return $this->save();
	}

	public function reset($ip, $username, $action)
	{
		return $this->deleteAll(array('ip' => $ip, 'username' => $username, 'action' => $action), false, false);
	}

	public function cleanup()
	{
		return $this->deleteAll(array('expires <' => date('Y-m-d H:i:s')), false, false);
	}
}
?>