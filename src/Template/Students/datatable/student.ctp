<?php
foreach ($dtResults as $result) {
    $this->dtResponse['aaData'][] = array(
        $result['Student']['id'],
        $result['Student']['full_name'],
        $result['Student']['studentnumber'],
	$result['Department']['name'],
	$result['College']['name'],
	$result['Program']['name'],
	$result['ProgramType']['name'],
        $this->Html->link(__('Edit Profile', true), array('action' => 'edit', $result['Student']['id'])).' |'.$this->Html->link(__('View', true), array('action' => 'view', $result['Student']['id']));
    );
}
?>

