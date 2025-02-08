<div class="unschedulePublishedCourses form">
<?php echo $this->Form->create('UnschedulePublishedCourse');?>
	<fieldset>
		<legend><?php echo __('Edit Unschedule Published Course'); ?></legend>
	<?php
		echo $this->Form->input('id');
		echo $this->Form->input('published_course_id');
		echo $this->Form->input('course_split_section_id');
		echo $this->Form->input('period_length');
		echo $this->Form->input('type');
		echo $this->Form->input('description');
	?>
	</fieldset>
<?php echo $this->Form->end(__('Submit'));?>
</div>
<div class="actions">
	<h3><?php echo __('Actions'); ?></h3>
	<ul>

		<li><?php echo $this->Html->link(__('Delete'), array('action' => 'delete', $this->Form->value('UnschedulePublishedCourse.id')), null, sprintf(__('Are you sure you want to delete # %s?'), $this->Form->value('UnschedulePublishedCourse.id'))); ?></li>
		<li><?php echo $this->Html->link(__('List Unschedule Published Courses'), array('action' => 'index'));?></li>
		<li><?php echo $this->Html->link(__('List Published Courses'), array('controller' => 'published_courses', 'action' => 'index')); ?> </li>
		<li><?php echo $this->Html->link(__('New Published Course'), array('controller' => 'published_courses', 'action' => 'add')); ?> </li>
		<li><?php echo $this->Html->link(__('List Course Split Sections'), array('controller' => 'course_split_sections', 'action' => 'index')); ?> </li>
		<li><?php echo $this->Html->link(__('New Course Split Section'), array('controller' => 'course_split_sections', 'action' => 'add')); ?> </li>
	</ul>
</div>