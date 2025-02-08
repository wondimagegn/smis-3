<div class="examResults form">
<?php echo $this->Form->create('ExamResult');?>
	<fieldset>
		<legend><?php echo __('Edit Exam Result'); ?></legend>
	<?php
		echo $this->Form->input('id');
		echo $this->Form->input('grade');
		echo $this->Form->input('grade_submission_date');
		echo $this->Form->input('student_id');
		echo $this->Form->input('grade_scale_id');
		echo $this->Form->input('published_course_id');
	?>
	</fieldset>
<?php echo $this->Form->end(__('Submit'));?>
</div>
<div class="actions">
	<h3><?php echo __('Actions'); ?></h3>
	<ul>

		<li><?php echo $this->Html->link(__('Delete'), array('action' => 'delete', $this->Form->value('ExamResult.id')), null, sprintf(__('Are you sure you want to delete # %s?'), $this->Form->value('ExamResult.id'))); ?></li>
		<li><?php echo $this->Html->link(__('List Exam Results'), array('action' => 'index'));?></li>
		<li><?php echo $this->Html->link(__('List Students'), array('controller' => 'students', 'action' => 'index')); ?> </li>
		<li><?php echo $this->Html->link(__('New Student'), array('controller' => 'students', 'action' => 'add')); ?> </li>
		<li><?php echo $this->Html->link(__('List Grade Scales'), array('controller' => 'grade_scales', 'action' => 'index')); ?> </li>
		<li><?php echo $this->Html->link(__('New Grade Scale'), array('controller' => 'grade_scales', 'action' => 'add')); ?> </li>
		<li><?php echo $this->Html->link(__('List Published Courses'), array('controller' => 'published_courses', 'action' => 'index')); ?> </li>
		<li><?php echo $this->Html->link(__('New Published Course'), array('controller' => 'published_courses', 'action' => 'add')); ?> </li>
	</ul>
</div>