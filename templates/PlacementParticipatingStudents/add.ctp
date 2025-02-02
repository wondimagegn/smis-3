<div class="placementParticipatingStudents form">
<?php echo $this->Form->create('PlacementParticipatingStudent'); ?>
	<fieldset>
		<legend><?php echo __('Add Placement Participating Student'); ?></legend>
	<?php
		echo $this->Form->input('accepted_student_id');
		echo $this->Form->input('student_id');
		echo $this->Form->input('program_id');
		echo $this->Form->input('program_type_id');
		echo $this->Form->input('original_college_department');
		echo $this->Form->input('academic_year');
		echo $this->Form->input('round');
		echo $this->Form->input('total_placement_weight');
		echo $this->Form->input('female_placement_weight');
		echo $this->Form->input('disability_weight');
		echo $this->Form->input('developing_region_weight');
		echo $this->Form->input('placement_round_participant_id');
		echo $this->Form->input('placement_type');
		echo $this->Form->input('status');
		echo $this->Form->input('remark');
	?>
	</fieldset>
<?php echo $this->Form->end(__('Submit')); ?>
</div>
<div class="actions">
	<h3><?php echo __('Actions'); ?></h3>
	<ul>

		<li><?php echo $this->Html->link(__('List Placement Participating Students'), array('action' => 'index')); ?></li>
		<li><?php echo $this->Html->link(__('List Accepted Students'), array('controller' => 'accepted_students', 'action' => 'index')); ?> </li>
		<li><?php echo $this->Html->link(__('New Accepted Student'), array('controller' => 'accepted_students', 'action' => 'add')); ?> </li>
		<li><?php echo $this->Html->link(__('List Students'), array('controller' => 'students', 'action' => 'index')); ?> </li>
		<li><?php echo $this->Html->link(__('New Student'), array('controller' => 'students', 'action' => 'add')); ?> </li>
		<li><?php echo $this->Html->link(__('List Placement Participating Programs'), array('controller' => 'placement_participating_programs', 'action' => 'index')); ?> </li>
		<li><?php echo $this->Html->link(__('New Placement Participating Program'), array('controller' => 'placement_participating_programs', 'action' => 'add')); ?> </li>
	</ul>
</div>
