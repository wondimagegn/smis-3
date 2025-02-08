<div class="gradeScalePublishedCourses form">
<?php echo $this->Form->create('GradeScalePublishedCourse');?>
	<fieldset>
		<legend><?php echo __('Edit Grade Scale Published Course'); ?></legend>
	<?php
		echo $this->Form->input('id');
		echo $this->Form->input('grade_scale_id');
		echo $this->Form->input('published_course_id');
		echo $this->Form->input('academic_year');
		echo $this->Form->input('semester');
	?>
	</fieldset>
<?php echo $this->Form->end(__('Submit'));?>
</div>
<div class="actions">
	<h3><?php echo __('Actions'); ?></h3>
	<ul>

		<li><?php echo $this->Html->link(__('Delete'), array('action' => 'delete', $this->Form->value('GradeScalePublishedCourse.id')), null, sprintf(__('Are you sure you want to delete # %s?'), $this->Form->value('GradeScalePublishedCourse.id'))); ?></li>
		<li><?php echo $this->Html->link(__('List Grade Scale Published Courses'), array('action' => 'index'));?></li>
		<li><?php echo $this->Html->link(__('List Grade Scales'), array('controller' => 'grade_scales', 'action' => 'index')); ?> </li>
		<li><?php echo $this->Html->link(__('New Grade Scale'), array('controller' => 'grade_scales', 'action' => 'add')); ?> </li>
		<li><?php echo $this->Html->link(__('List Published Courses'), array('controller' => 'published_courses', 'action' => 'index')); ?> </li>
		<li><?php echo $this->Html->link(__('New Published Course'), array('controller' => 'published_courses', 'action' => 'add')); ?> </li>
	</ul>
</div>