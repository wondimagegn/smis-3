<div class="placementEntranceExamResultEntries form">
<?php echo $this->Form->create('PlacementEntranceExamResultEntry'); ?>
	<fieldset>
		<legend><?php echo __('Edit Placement Entrance Exam Result Entry'); ?></legend>
	<?php
		echo $this->Form->input('id');
		echo $this->Form->input('accepted_student_id');
		echo $this->Form->input('student_id');
		echo $this->Form->input('result');
		echo $this->Form->input('placement_round_participant_id');
	?>
	</fieldset>
<?php echo $this->Form->end(__('Submit')); ?>
</div>
<div class="actions">
	<h3><?php echo __('Actions'); ?></h3>
	<ul>

		<li><?php echo $this->Form->postLink(__('Delete'), array('action' => 'delete', $this->Form->value('PlacementEntranceExamResultEntry.id')), array('confirm' => __('Are you sure you want to delete # %s?', $this->Form->value('PlacementEntranceExamResultEntry.id')))); ?></li>
		<li><?php echo $this->Html->link(__('List Placement Entrance Exam Result Entries'), array('action' => 'index')); ?></li>
		<li><?php echo $this->Html->link(__('List Accepted Students'), array('controller' => 'accepted_students', 'action' => 'index')); ?> </li>
		<li><?php echo $this->Html->link(__('New Accepted Student'), array('controller' => 'accepted_students', 'action' => 'add')); ?> </li>
		<li><?php echo $this->Html->link(__('List Students'), array('controller' => 'students', 'action' => 'index')); ?> </li>
		<li><?php echo $this->Html->link(__('New Student'), array('controller' => 'students', 'action' => 'add')); ?> </li>
		<li><?php echo $this->Html->link(__('List Placement Round Participants'), array('controller' => 'placement_round_participants', 'action' => 'index')); ?> </li>
		<li><?php echo $this->Html->link(__('New Placement Round Participant'), array('controller' => 'placement_round_participants', 'action' => 'add')); ?> </li>
	</ul>
</div>
