<div class="courseDrops form">
<?php echo $this->Form->create('CourseDrop');?>
	<fieldset>
		<legend><?php echo __('Edit Course Drop'); ?></legend>
	<?php
		echo $this->Form->input('id');
		echo $this->Form->input('year_level_id');
		echo $this->Form->input('semester');
		echo $this->Form->input('academic_year');
		echo $this->Form->input('approval');
		echo $this->Form->input('approved_by');
		echo $this->Form->input('minute_number');
		echo $this->Form->input('forced');
		echo $this->Form->input('student_id');
		echo $this->Form->input('course_id');
	?>
	</fieldset>
<?php echo $this->Form->end(__('Submit'));?>
</div>
<div class="actions">
	<h3><?php echo __('Actions'); ?></h3>
	<ul>

		<li><?php echo $this->Html->link(__('Delete'), array('action' => 'delete', $this->Form->value('CourseDrop.id')), null, sprintf(__('Are you sure you want to delete # %s?'), $this->Form->value('CourseDrop.id'))); ?></li>
		<li><?php echo $this->Html->link(__('List Course Drops'), array('action' => 'index'));?></li>
		<li><?php echo $this->Html->link(__('List Year Levels'), array('controller' => 'year_levels', 'action' => 'index')); ?> </li>
		<li><?php echo $this->Html->link(__('New Year Level'), array('controller' => 'year_levels', 'action' => 'add')); ?> </li>
		<li><?php echo $this->Html->link(__('List Students'), array('controller' => 'students', 'action' => 'index')); ?> </li>
		<li><?php echo $this->Html->link(__('New Student'), array('controller' => 'students', 'action' => 'add')); ?> </li>
		<li><?php echo $this->Html->link(__('List Courses'), array('controller' => 'courses', 'action' => 'index')); ?> </li>
		<li><?php echo $this->Html->link(__('New Course'), array('controller' => 'courses', 'action' => 'add')); ?> </li>
	</ul>
</div>