<div class="gradeScalePublishedCourses view">
<h2><?php echo __('Grade Scale Published Course');?></h2>
	<dl><?php $i = 0; $class = ' class="altrow"';?>
		<dt<?php if ($i % 2 == 0) echo $class;?>><?php echo __('Id'); ?></dt>
		<dd<?php if ($i++ % 2 == 0) echo $class;?>>
			<?php echo $gradeScalePublishedCourse['GradeScalePublishedCourse']['id']; ?>
			&nbsp;
		</dd>
		<dt<?php if ($i % 2 == 0) echo $class;?>><?php echo __('Grade Scale'); ?></dt>
		<dd<?php if ($i++ % 2 == 0) echo $class;?>>
			<?php echo $this->Html->link($gradeScalePublishedCourse['GradeScale']['name'], array('controller' => 'grade_scales', 'action' => 'view', $gradeScalePublishedCourse['GradeScale']['id'])); ?>
			&nbsp;
		</dd>
		<dt<?php if ($i % 2 == 0) echo $class;?>><?php echo __('Published Course'); ?></dt>
		<dd<?php if ($i++ % 2 == 0) echo $class;?>>
			<?php echo $this->Html->link($gradeScalePublishedCourse['PublishedCourse']['id'], array('controller' => 'published_courses', 'action' => 'view', $gradeScalePublishedCourse['PublishedCourse']['id'])); ?>
			&nbsp;
		</dd>
		<dt<?php if ($i % 2 == 0) echo $class;?>><?php echo __('Academic Year'); ?></dt>
		<dd<?php if ($i++ % 2 == 0) echo $class;?>>
			<?php echo $gradeScalePublishedCourse['GradeScalePublishedCourse']['academic_year']; ?>
			&nbsp;
		</dd>
		<dt<?php if ($i % 2 == 0) echo $class;?>><?php echo __('Semester'); ?></dt>
		<dd<?php if ($i++ % 2 == 0) echo $class;?>>
			<?php echo $gradeScalePublishedCourse['GradeScalePublishedCourse']['semester']; ?>
			&nbsp;
		</dd>
	</dl>
</div>
<div class="actions">
	<h3><?php echo __('Actions'); ?></h3>
	<ul>
		<li><?php echo $this->Html->link(__('Edit Grade Scale Published Course'), array('action' => 'edit', $gradeScalePublishedCourse['GradeScalePublishedCourse']['id'])); ?> </li>
		<li><?php echo $this->Html->link(__('Delete Grade Scale Published Course'), array('action' => 'delete', $gradeScalePublishedCourse['GradeScalePublishedCourse']['id']), null, sprintf(__('Are you sure you want to delete # %s?'), $gradeScalePublishedCourse['GradeScalePublishedCourse']['id'])); ?> </li>
		<li><?php echo $this->Html->link(__('List Grade Scale Published Courses'), array('action' => 'index')); ?> </li>
		<li><?php echo $this->Html->link(__('New Grade Scale Published Course'), array('action' => 'add')); ?> </li>
		<li><?php echo $this->Html->link(__('List Grade Scales'), array('controller' => 'grade_scales', 'action' => 'index')); ?> </li>
		<li><?php echo $this->Html->link(__('New Grade Scale'), array('controller' => 'grade_scales', 'action' => 'add')); ?> </li>
		<li><?php echo $this->Html->link(__('List Published Courses'), array('controller' => 'published_courses', 'action' => 'index')); ?> </li>
		<li><?php echo $this->Html->link(__('New Published Course'), array('controller' => 'published_courses', 'action' => 'add')); ?> </li>
	</ul>
</div>
