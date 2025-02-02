<div class="courseSubstitutionRequests view">
<h2><?php echo __('Course Substitution Request');?></h2>
	<dl><?php $i = 0; $class = ' class="altrow"';?>

		<dt<?php if ($i % 2 == 0) echo $class;?>><?php echo __('Request Date'); ?></dt>
		<dd<?php if ($i++ % 2 == 0) echo $class;?>>
			<?php echo $this->Format->short_date ($courseSubstitutionRequest['CourseSubstitutionRequest']['request_date']); ?>
			&nbsp;
		</dd>
		<dt<?php if ($i % 2 == 0) echo $class;?>><?php echo __('Student'); ?></dt>
		<dd<?php if ($i++ % 2 == 0) echo $class;?>>
			<?php echo $this->Html->link($courseSubstitutionRequest['Student']['full_name'], array('controller' => 'students', 'action' => 'view', $courseSubstitutionRequest['Student']['id'])); ?>
			&nbsp;
		</dd>
		<dt<?php if ($i % 2 == 0) echo $class;?>><?php echo __('Course For Substitued'); ?></dt>
		<dd<?php if ($i++ % 2 == 0) echo $class;?>>
			<?php echo $this->Html->link($courseSubstitutionRequest['CourseForSubstitued']['course_title'], array('controller' => 'courses', 'action' => 'view', $courseSubstitutionRequest['CourseForSubstitued']['id'])); ?>
			&nbsp;
		</dd>
		<dt<?php if ($i % 2 == 0) echo $class;?>><?php echo __('Course Be Substitued'); ?></dt>
		<dd<?php if ($i++ % 2 == 0) echo $class;?>>
			<?php echo $this->Html->link($courseSubstitutionRequest['CourseBeSubstitued']['course_title'], array('controller' => 'courses', 'action' => 'view', $courseSubstitutionRequest['CourseBeSubstitued']['id'])); ?>
			&nbsp;
		</dd>
		
	</dl>
</div>
