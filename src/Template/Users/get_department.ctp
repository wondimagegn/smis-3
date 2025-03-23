<?php
if (!empty($departments)) {
	echo $this->Form->input('Staff.' . $college_id . '.assigned_to', array(
		'multiple' => 'checkbox',
		'options' => $departments,
		'div' => false,
		'label' => 'Assign To/Responsible For',
		'checked' => 'checked'
	));
} ?>
