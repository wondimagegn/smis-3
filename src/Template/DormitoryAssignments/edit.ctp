<div class="dormitoryAssignments form">
<?php echo $this->Form->create('DormitoryAssignment');?>
	<fieldset>
		<legend><?php echo __('Edit Dormitory Assignment'); ?></legend>
	<?php
		echo $this->Form->input('id');
		echo $this->Form->input('dormitory_id');
		echo $this->Form->input('student_id');
		echo $this->Form->input('accepted_student_id');
		echo $this->Form->input('assignment_date');
		echo $this->Form->input('leave_date');
		echo $this->Form->input('received');
		echo $this->Form->input('received_date');
	?>
	</fieldset>
<?php echo $this->Form->end(__('Submit'));?>
</div>
<div class="actions">
	<h3><?php echo __('Actions'); ?></h3>
	<ul>

		<li><?php echo $this->Html->link(__('Delete'), array('action' => 'delete', $this->Form->value('DormitoryAssignment.id')), null, sprintf(__('Are you sure you want to delete # %s?'), $this->Form->value('DormitoryAssignment.id'))); ?></li>
		<li><?php echo $this->Html->link(__('List Dormitory Assignments'), array('action' => 'index'));?></li>
		<li><?php echo $this->Html->link(__('List Dormitories'), array('controller' => 'dormitories', 'action' => 'index')); ?> </li>
		<li><?php echo $this->Html->link(__('New Dormitory'), array('controller' => 'dormitories', 'action' => 'add')); ?> </li>
		<li><?php echo $this->Html->link(__('List Students'), array('controller' => 'students', 'action' => 'index')); ?> </li>
		<li><?php echo $this->Html->link(__('New Student'), array('controller' => 'students', 'action' => 'add')); ?> </li>
		<li><?php echo $this->Html->link(__('List Accepted Students'), array('controller' => 'accepted_students', 'action' => 'index')); ?> </li>
		<li><?php echo $this->Html->link(__('New Accepted Student'), array('controller' => 'accepted_students', 'action' => 'add')); ?> </li>
	</ul>
</div>