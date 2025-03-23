<div class="unschedulePublishedCourses view">
<h2><?php echo __('Unschedule Published Course');?></h2>
	<dl><?php $i = 0; $class = ' class="altrow"';?>
		<dt<?php if ($i % 2 == 0) echo $class;?>><?php echo __('Id'); ?></dt>
		<dd<?php if ($i++ % 2 == 0) echo $class;?>>
			<?php echo $unschedulePublishedCourse['UnschedulePublishedCourse']['id']; ?>
			&nbsp;
		</dd>
		<dt<?php if ($i % 2 == 0) echo $class;?>><?php echo __('Published Course'); ?></dt>
		<dd<?php if ($i++ % 2 == 0) echo $class;?>>
			<?php echo $this->Html->link($unschedulePublishedCourse['PublishedCourse']['id'], array('controller' => 'published_courses', 'action' => 'view', $unschedulePublishedCourse['PublishedCourse']['id'])); ?>
			&nbsp;
		</dd>
		<dt<?php if ($i % 2 == 0) echo $class;?>><?php echo __('Course Split Section'); ?></dt>
		<dd<?php if ($i++ % 2 == 0) echo $class;?>>
			<?php echo $this->Html->link($unschedulePublishedCourse['CourseSplitSection']['id'], array('controller' => 'course_split_sections', 'action' => 'view', $unschedulePublishedCourse['CourseSplitSection']['id'])); ?>
			&nbsp;
		</dd>
		<dt<?php if ($i % 2 == 0) echo $class;?>><?php echo __('Period Length'); ?></dt>
		<dd<?php if ($i++ % 2 == 0) echo $class;?>>
			<?php echo $unschedulePublishedCourse['UnschedulePublishedCourse']['period_length']; ?>
			&nbsp;
		</dd>
		<dt<?php if ($i % 2 == 0) echo $class;?>><?php echo __('Type'); ?></dt>
		<dd<?php if ($i++ % 2 == 0) echo $class;?>>
			<?php echo $unschedulePublishedCourse['UnschedulePublishedCourse']['type']; ?>
			&nbsp;
		</dd>
		<dt<?php if ($i % 2 == 0) echo $class;?>><?php echo __('Description'); ?></dt>
		<dd<?php if ($i++ % 2 == 0) echo $class;?>>
			<?php echo $unschedulePublishedCourse['UnschedulePublishedCourse']['description']; ?>
			&nbsp;
		</dd>
		<dt<?php if ($i % 2 == 0) echo $class;?>><?php echo __('Created'); ?></dt>
		<dd<?php if ($i++ % 2 == 0) echo $class;?>>
			<?php echo $unschedulePublishedCourse['UnschedulePublishedCourse']['created']; ?>
			&nbsp;
		</dd>
		<dt<?php if ($i % 2 == 0) echo $class;?>><?php echo __('Modified'); ?></dt>
		<dd<?php if ($i++ % 2 == 0) echo $class;?>>
			<?php echo $unschedulePublishedCourse['UnschedulePublishedCourse']['modified']; ?>
			&nbsp;
		</dd>
	</dl>
</div>
<div class="actions">
	<h3><?php echo __('Actions'); ?></h3>
	<ul>
		<li><?php echo $this->Html->link(__('Edit Unschedule Published Course'), array('action' => 'edit', $unschedulePublishedCourse['UnschedulePublishedCourse']['id'])); ?> </li>
		<li><?php echo $this->Html->link(__('Delete Unschedule Published Course'), array('action' => 'delete', $unschedulePublishedCourse['UnschedulePublishedCourse']['id']), null, sprintf(__('Are you sure you want to delete # %s?'), $unschedulePublishedCourse['UnschedulePublishedCourse']['id'])); ?> </li>
		<li><?php echo $this->Html->link(__('List Unschedule Published Courses'), array('action' => 'index')); ?> </li>
		<li><?php echo $this->Html->link(__('New Unschedule Published Course'), array('action' => 'add')); ?> </li>
		<li><?php echo $this->Html->link(__('List Published Courses'), array('controller' => 'published_courses', 'action' => 'index')); ?> </li>
		<li><?php echo $this->Html->link(__('New Published Course'), array('controller' => 'published_courses', 'action' => 'add')); ?> </li>
		<li><?php echo $this->Html->link(__('List Course Split Sections'), array('controller' => 'course_split_sections', 'action' => 'index')); ?> </li>
		<li><?php echo $this->Html->link(__('New Course Split Section'), array('controller' => 'course_split_sections', 'action' => 'add')); ?> </li>
	</ul>
</div>
