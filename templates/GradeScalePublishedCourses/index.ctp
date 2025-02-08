<div class="gradeScalePublishedCourses index">
	<h2><?php echo __('Grade Scale Published Courses');?></h2>
	<table cellpadding="0" cellspacing="0">
	<tr>
			<th><?php echo $this->Paginator->sort('id');?></th>
			<th><?php echo $this->Paginator->sort('grade_scale_id');?></th>
			<th><?php echo $this->Paginator->sort('published_course_id');?></th>
			<th><?php echo $this->Paginator->sort('academic_year');?></th>
			<th><?php echo $this->Paginator->sort('semester');?></th>
			<th class="actions"><?php echo __('Actions');?></th>
	</tr>
	<?php
	$i = 0;
	foreach ($gradeScalePublishedCourses as $gradeScalePublishedCourse):
		$class = null;
		if ($i++ % 2 == 0) {
			$class = ' class="altrow"';
		}
	?>
	<tr<?php echo $class;?>>
		<td><?php echo $gradeScalePublishedCourse['GradeScalePublishedCourse']['id']; ?>&nbsp;</td>
		<td>
			<?php echo $this->Html->link($gradeScalePublishedCourse['GradeScale']['name'], array('controller' => 'grade_scales', 'action' => 'view', $gradeScalePublishedCourse['GradeScale']['id'])); ?>
		</td>
		<td>
			<?php echo $this->Html->link($gradeScalePublishedCourse['PublishedCourse']['id'], array('controller' => 'published_courses', 'action' => 'view', $gradeScalePublishedCourse['PublishedCourse']['id'])); ?>
		</td>
		<td><?php echo $gradeScalePublishedCourse['GradeScalePublishedCourse']['academic_year']; ?>&nbsp;</td>
		<td><?php echo $gradeScalePublishedCourse['GradeScalePublishedCourse']['semester']; ?>&nbsp;</td>
		<td class="actions">
			<?php echo $this->Html->link(__('View'), array('action' => 'view', $gradeScalePublishedCourse['GradeScalePublishedCourse']['id'])); ?>
			<?php echo $this->Html->link(__('Edit'), array('action' => 'edit', $gradeScalePublishedCourse['GradeScalePublishedCourse']['id'])); ?>
			<?php echo $this->Html->link(__('Delete'), array('action' => 'delete', $gradeScalePublishedCourse['GradeScalePublishedCourse']['id']), null, sprintf(__('Are you sure you want to delete # %s?'), $gradeScalePublishedCourse['GradeScalePublishedCourse']['id'])); ?>
		</td>
	</tr>
<?php endforeach; ?>
	</table>
	<p>
	<?php
	echo $this->Paginator->counter(array(
	'format' => __('Page %page% of %pages%, showing %current% records out of %count% total, starting on record %start%, ending on %end%')
	));
	?>	</p>

	<div class="paging">
		<?php echo $this->Paginator->prev('<< ' . __('previous'), array(), null, array('class'=>'disabled'));?>
	 | 	<?php echo $this->Paginator->numbers();?>
 |
		<?php echo $this->Paginator->next(__('next') . ' >>', array(), null, array('class' => 'disabled'));?>
	</div>
</div>
<div class="actions">
	<h3><?php echo __('Actions'); ?></h3>
	<ul>
		<li><?php echo $this->Html->link(__('New Grade Scale Published Course'), array('action' => 'add')); ?></li>
		<li><?php echo $this->Html->link(__('List Grade Scales'), array('controller' => 'grade_scales', 'action' => 'index')); ?> </li>
		<li><?php echo $this->Html->link(__('New Grade Scale'), array('controller' => 'grade_scales', 'action' => 'add')); ?> </li>
		<li><?php echo $this->Html->link(__('List Published Courses'), array('controller' => 'published_courses', 'action' => 'index')); ?> </li>
		<li><?php echo $this->Html->link(__('New Published Course'), array('controller' => 'published_courses', 'action' => 'add')); ?> </li>
	</ul>
</div>