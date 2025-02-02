<div class="excludedCourseFromTranscripts view">
<h2><?php echo __('Excluded Course From Transcript');?></h2>
	<dl><?php $i = 0; $class = ' class="altrow"';?>
		<dt<?php if ($i % 2 == 0) echo $class;?>><?php echo __('Id'); ?></dt>
		<dd<?php if ($i++ % 2 == 0) echo $class;?>>
			<?php echo $excludedCourseFromTranscript['ExcludedCourseFromTranscript']['id']; ?>
			&nbsp;
		</dd>
		<dt<?php if ($i % 2 == 0) echo $class;?>><?php echo __('Course Registration'); ?></dt>
		<dd<?php if ($i++ % 2 == 0) echo $class;?>>
			<?php echo $this->Html->link($excludedCourseFromTranscript['CourseRegistration']['id'], array('controller' => 'course_registrations', 'action' => 'view', $excludedCourseFromTranscript['CourseRegistration']['id'])); ?>
			&nbsp;
		</dd>
		<dt<?php if ($i % 2 == 0) echo $class;?>><?php echo __('Course Exemption'); ?></dt>
		<dd<?php if ($i++ % 2 == 0) echo $class;?>>
			<?php echo $this->Html->link($excludedCourseFromTranscript['CourseExemption']['id'], array('controller' => 'course_exemptions', 'action' => 'view', $excludedCourseFromTranscript['CourseExemption']['id'])); ?>
			&nbsp;
		</dd>
		<dt<?php if ($i % 2 == 0) echo $class;?>><?php echo __('Minute Number'); ?></dt>
		<dd<?php if ($i++ % 2 == 0) echo $class;?>>
			<?php echo $excludedCourseFromTranscript['ExcludedCourseFromTranscript']['minute_number']; ?>
			&nbsp;
		</dd>
		<dt<?php if ($i % 2 == 0) echo $class;?>><?php echo __('Created'); ?></dt>
		<dd<?php if ($i++ % 2 == 0) echo $class;?>>
			<?php echo $excludedCourseFromTranscript['ExcludedCourseFromTranscript']['created']; ?>
			&nbsp;
		</dd>
		<dt<?php if ($i % 2 == 0) echo $class;?>><?php echo __('Modified'); ?></dt>
		<dd<?php if ($i++ % 2 == 0) echo $class;?>>
			<?php echo $excludedCourseFromTranscript['ExcludedCourseFromTranscript']['modified']; ?>
			&nbsp;
		</dd>
	</dl>
</div>
<div class="actions">
	<h3><?php echo __('Actions'); ?></h3>
	<ul>
		<li><?php echo $this->Html->link(__('Edit Excluded Course From Transcript'), array('action' => 'edit', $excludedCourseFromTranscript['ExcludedCourseFromTranscript']['id'])); ?> </li>
		<li><?php echo $this->Html->link(__('Delete Excluded Course From Transcript'), array('action' => 'delete', $excludedCourseFromTranscript['ExcludedCourseFromTranscript']['id']), null, sprintf(__('Are you sure you want to delete # %s?'), $excludedCourseFromTranscript['ExcludedCourseFromTranscript']['id'])); ?> </li>
		<li><?php echo $this->Html->link(__('List Excluded Course From Transcripts'), array('action' => 'index')); ?> </li>
		<li><?php echo $this->Html->link(__('New Excluded Course From Transcript'), array('action' => 'add')); ?> </li>
		<li><?php echo $this->Html->link(__('List Course Registrations'), array('controller' => 'course_registrations', 'action' => 'index')); ?> </li>
		<li><?php echo $this->Html->link(__('New Course Registration'), array('controller' => 'course_registrations', 'action' => 'add')); ?> </li>
		<li><?php echo $this->Html->link(__('List Course Exemptions'), array('controller' => 'course_exemptions', 'action' => 'index')); ?> </li>
		<li><?php echo $this->Html->link(__('New Course Exemption'), array('controller' => 'course_exemptions', 'action' => 'add')); ?> </li>
	</ul>
</div>
