<div class="excludedPublishedCourseExams view">
<h2><?php echo __('Excluded Published Course Exam');?></h2>
	<dl><?php $i = 0; $class = ' class="altrow"';?>
		<dt<?php if ($i % 2 == 0) echo $class;?>><?php echo __('Id'); ?></dt>
		<dd<?php if ($i++ % 2 == 0) echo $class;?>>
			<?php echo $excludedPublishedCourseExam['ExcludedPublishedCourseExam']['id']; ?>
			&nbsp;
		</dd>
		<dt<?php if ($i % 2 == 0) echo $class;?>><?php echo __('Published Course'); ?></dt>
		<dd<?php if ($i++ % 2 == 0) echo $class;?>>
			<?php echo $this->Html->link($excludedPublishedCourseExam['PublishedCourse']['id'], array('controller' => 'published_courses', 'action' => 'view', $excludedPublishedCourseExam['PublishedCourse']['id'])); ?>
			&nbsp;
		</dd>
	</dl>
</div>
<div class="actions">
	<h3><?php echo __('Actions'); ?></h3>
	<ul>
		<li><?php echo $this->Html->link(__('Edit Excluded Published Course Exam'), array('action' => 'edit', $excludedPublishedCourseExam['ExcludedPublishedCourseExam']['id'])); ?> </li>
		<li><?php echo $this->Html->link(__('Delete Excluded Published Course Exam'), array('action' => 'delete', $excludedPublishedCourseExam['ExcludedPublishedCourseExam']['id']), null, sprintf(__('Are you sure you want to delete # %s?'), $excludedPublishedCourseExam['ExcludedPublishedCourseExam']['id'])); ?> </li>
		<li><?php echo $this->Html->link(__('List Excluded Published Course Exams'), array('action' => 'index')); ?> </li>
		<li><?php echo $this->Html->link(__('New Excluded Published Course Exam'), array('action' => 'add')); ?> </li>
		<li><?php echo $this->Html->link(__('List Published Courses'), array('controller' => 'published_courses', 'action' => 'index')); ?> </li>
		<li><?php echo $this->Html->link(__('New Published Course'), array('controller' => 'published_courses', 'action' => 'add')); ?> </li>
	</ul>
</div>
