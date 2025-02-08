<div class="examRoomConstraints form">
<?php echo $this->Form->create('ExamRoomConstraint');?>
	<fieldset>
		<legend><?php echo __('Edit Exam Room Constraint'); ?></legend>
	<?php
		echo $this->Form->input('id');
		echo $this->Form->input('class_room_id');
		echo $this->Form->input('academic_year');
		echo $this->Form->input('semester');
		echo $this->Form->input('exam_date');
		echo $this->Form->input('session');
		echo $this->Form->input('active');
	?>
	</fieldset>
<?php echo $this->Form->end(__('Submit'));?>
</div>
<div class="actions">
	<h3><?php echo __('Actions'); ?></h3>
	<ul>

		<li><?php echo $this->Html->link(__('Delete'), array('action' => 'delete', $this->Form->value('ExamRoomConstraint.id')), null, sprintf(__('Are you sure you want to delete # %s?'), $this->Form->value('ExamRoomConstraint.id'))); ?></li>
		<li><?php echo $this->Html->link(__('List Exam Room Constraints'), array('action' => 'index'));?></li>
		<li><?php echo $this->Html->link(__('List Class Rooms'), array('controller' => 'class_rooms', 'action' => 'index')); ?> </li>
		<li><?php echo $this->Html->link(__('New Class Room'), array('controller' => 'class_rooms', 'action' => 'add')); ?> </li>
	</ul>
</div>