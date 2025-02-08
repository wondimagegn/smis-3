<div class="resultEntryAssignments view">
<h2><?php echo __('Result Entry Assignment'); ?></h2>
	<dl>
		<dt><?php echo __('Id'); ?></dt>
		<dd>
			<?php echo h($resultEntryAssignment['ResultEntryAssignment']['id']); ?>
			&nbsp;
		</dd>
		<dt><?php echo __('Student'); ?></dt>
		<dd>
			<?php echo $this->Html->link($resultEntryAssignment['Student']['id'], array('controller' => 'students', 'action' => 'view', $resultEntryAssignment['Student']['id'])); ?>
			&nbsp;
		</dd>
		<dt><?php echo __('Minute Number'); ?></dt>
		<dd>
			<?php echo h($resultEntryAssignment['ResultEntryAssignment']['minute_number']); ?>
			&nbsp;
		</dd>
		<dt><?php echo __('Published Course'); ?></dt>
		<dd>
			<?php echo $this->Html->link($resultEntryAssignment['PublishedCourse']['id'], array('controller' => 'published_courses', 'action' => 'view', $resultEntryAssignment['PublishedCourse']['id'])); ?>
			&nbsp;
		</dd>
		<dt><?php echo __('Course Registration'); ?></dt>
		<dd>
			<?php echo $this->Html->link($resultEntryAssignment['CourseRegistration']['id'], array('controller' => 'course_registrations', 'action' => 'view', $resultEntryAssignment['CourseRegistration']['id'])); ?>
			&nbsp;
		</dd>
		<dt><?php echo __('Course Add'); ?></dt>
		<dd>
			<?php echo $this->Html->link($resultEntryAssignment['CourseAdd']['id'], array('controller' => 'course_adds', 'action' => 'view', $resultEntryAssignment['CourseAdd']['id'])); ?>
			&nbsp;
		</dd>
		<dt><?php echo __('Result'); ?></dt>
		<dd>
			<?php echo h($resultEntryAssignment['ResultEntryAssignment']['result']); ?>
			&nbsp;
		</dd>
		<dt><?php echo __('Created'); ?></dt>
		<dd>
			<?php echo h($resultEntryAssignment['ResultEntryAssignment']['created']); ?>
			&nbsp;
		</dd>
		<dt><?php echo __('Modified'); ?></dt>
		<dd>
			<?php echo h($resultEntryAssignment['ResultEntryAssignment']['modified']); ?>
			&nbsp;
		</dd>
	</dl>
</div>
<div class="actions">
	<h3><?php echo __('Actions'); ?></h3>
	<ul>
		<li><?php echo $this->Html->link(__('Edit Result Entry Assignment'), array('action' => 'edit', $resultEntryAssignment['ResultEntryAssignment']['id'])); ?> </li>
		<li><?php echo $this->Form->postLink(__('Delete Result Entry Assignment'), array('action' => 'delete', $resultEntryAssignment['ResultEntryAssignment']['id']), array('confirm' => __('Are you sure you want to delete # %s?', $resultEntryAssignment['ResultEntryAssignment']['id']))); ?> </li>
		<li><?php echo $this->Html->link(__('List Result Entry Assignments'), array('action' => 'index')); ?> </li>
		<li><?php echo $this->Html->link(__('New Result Entry Assignment'), array('action' => 'add')); ?> </li>
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
