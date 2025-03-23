<div class="examRoomCourseConstraints form">
<?php echo $this->Form->create('ExamRoomCourseConstraint');?>
	<fieldset>
		<legend><?php echo __('Edit Exam Room Course Constraint'); ?></legend>
	<?php
		echo $this->Form->input('id');
		echo $this->Form->input('class_room_id');
		echo $this->Form->input('published_course_id');
		echo $this->Form->input('active');
	?>
	</fieldset>
<?php echo $this->Form->end(__('Submit'));?>
</div>
<div class="actions">
	<h3><?php echo __('Actions'); ?></h3>
	<ul>

		<li><?php echo $this->Html->link(__('Delete'), array('action' => 'delete', $this->Form->value('ExamRoomCourseConstraint.id')), null, sprintf(__('Are you sure you want to delete # %s?'), $this->Form->value('ExamRoomCourseConstraint.id'))); ?></li>
		<li><?php echo $this->Html->link(__('List Exam Room Course Constraints'), array('action' => 'index'));?></li>
		<li><?php echo $this->Html->link(__('List Class Rooms'), array('controller' => 'class_rooms', 'action' => 'index')); ?> </li>
		<li><?php echo $this->Html->link(__('New Class Room'), array('controller' => 'class_rooms', 'action' => 'add')); ?> </li>
		<li><?php echo $this->Html->link(__('List Published Courses'), array('controller' => 'published_courses', 'action' => 'index')); ?> </li>
		<li><?php echo $this->Html->link(__('New Published Course'), array('controller' => 'published_courses', 'action' => 'add')); ?> </li>
	</ul>
</div>