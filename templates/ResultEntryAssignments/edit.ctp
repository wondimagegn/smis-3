<div class="resultEntryAssignments form">
<?php echo $this->Form->create('ResultEntryAssignment'); ?>
	<fieldset>
		<legend><?php echo __('Edit Result Entry Assignment'); ?></legend>
	<?php
		echo $this->Form->input('id');
		echo $this->Form->input('student_id');
		echo $this->Form->input('minute_number');
		echo $this->Form->input('published_course_id');
		echo $this->Form->input('course_registration_id');
		echo $this->Form->input('course_add_id');
		echo $this->Form->input('result');
	?>
	</fieldset>
<?php echo $this->Form->end(__('Submit')); ?>
</div>
<div class="actions">
	<h3><?php echo __('Actions'); ?></h3>
	<ul>

		<li><?php echo $this->Form->postLink(__('Delete'), array('action' => 'delete', $this->Form->value('ResultEntryAssignment.id')), array('confirm' => __('Are you sure you want to delete # %s?', $this->Form->value('ResultEntryAssignment.id')))); ?></li>
		<li><?php echo $this->Html->link(__('List Result Entry Assignments'), array('action' => 'index')); ?></li>
		<li><?php echo $this->Html->link(__('List Students'), array('controller' => 'students', 'action' => 'index')); ?> </li>
		<li><?php echo $this->Html->link(__('New Student'), array('controller' => 'students', 'action' => 'add')); ?> </li>
		<li><?php echo $this->Html->link(__('List Published Courses'), array('controller' => 'published_courses', 'action' => 'index')); ?> </li>
		<li><?php echo $this->Html->link(__('New Published Course'), array('controller' => 'published_courses', 'action' => 'add')); ?> </li>
		<li><?php echo $this->Html->link(__('List Course Registrations'), array('controller' => 'course_registrations', 'action' => 'index')); ?> </li>
		<li><?php echo $this->Html->link(__('New Course Registration'), array('controller' => 'course_registrations', 'action' => 'add')); ?> </li>
		<li><?php echo $this->Html->link(__('List Course Adds'), array('controller' => 'course_adds', 'action' => 'index')); ?> </li>
		<li><?php echo $this->Html->link(__('New Course Add'), array('controller' => 'course_adds', 'action' => 'add')); ?> </li>
	</ul>
</div>
