<?php
class Backup extends AppModel
{
	var $name = 'Backup';
	function getLatestBackups($limit = 10)
	{
		$backups = $this->find('all', array('order' => array('Backup.created' => 'DESC'), 'limit' => $limit));
		
		if (!empty($backups)) {
			foreach ($backups as &$backup) {
				if (file_exists($backup['Backup']['location'] . DS . $backup['Backup']['name'])) {
					$backup['Backup']['file_exists'] = true;
				} else {
					$backup['Backup']['file_exists'] = false;
				}
			}
		}
		return $backups;
	}
}
