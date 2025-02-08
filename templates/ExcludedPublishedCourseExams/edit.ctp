<div class="excludedPublishedCourseExams form">
<?php echo $this->Form->create('ExcludedPublishedCourseExam');?>
	<fieldset>
 		<legend><?php echo __('Edit Excluded Published Course Exam'); ?></legend>
	<?php
		echo $this->Form->input('id');
		echo $this->Form->input('published_course_id');
	?>
	</fieldset>
<?php echo $this->Form->end(__('Submit'));?>
</div>
<div class="actions">
	<h3><?php echo __('Actions'); ?></h3>
	<ul>

		<li><?php echo $this->Html->link(__('Delete'), array('action' => 'delete', $this->Form->value('ExcludedPublishedCourseExam.id')), null, sprintf(__('Are you sure you want to delete # %s?'), $this->Form->value('ExcludedPublishedCourseExam.id'))); ?></li>
		<li><?php echo $this->Html->link(__('List Excluded Published Course Exams'), array('action' => 'index'));?></li>
		<li><?php echo $this->Html->link(__('List Published Courses'), array('controller' => 'published_courses', 'action' => 'index')); ?> </li>
		<li><?php echo $this->Html->link(__('New Published Course'), array('controller' => 'published_courses', 'action' => 'add')); ?> </li>
	</ul>
</div>