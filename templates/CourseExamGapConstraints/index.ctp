<?php ?>
<div class="box">
     <div class="box-body">
       <div class="row">
	  <div class="large-12 columns">
             
<div class="courseExamGapConstraints index">
	<h2><?php echo __('Course Exam Gap Constraints');?></h2>
	<table cellpadding="0" cellspacing="0">
	<tr>
			<th><?php echo $this->Paginator->sort('No.');?></th>
			<th><?php echo $this->Paginator->sort('Course');?></th>
			<th><?php echo $this->Paginator->sort('Section');?></th>
			<th><?php echo $this->Paginator->sort('gap_before_exam');?></th>
			<th class="actions"><?php echo __('Actions');?></th>
	</tr>
	<?php
	$i = 0;
	$count =1;
	foreach ($courseExamGapConstraints as $courseExamGapConstraint):
		$class = null;
		if ($i++ % 2 == 0) {
			$class = ' class="altrow"';
		}
	?>
	<tr<?php echo $class;?>>
		<td><?php echo $count++; ?>&nbsp;</td>
		<td>
			<?php echo 	$this->Html->link($courseExamGapConstraint['PublishedCourse']['Course']['course_code_title'].'('.$courseExamGapConstraint['PublishedCourse']['Course']['course_code'].' - Chr. '.$courseExamGapConstraint['PublishedCourse']['Course']['credit'].')',array('controller' => 'published_courses','action' =>'view',$courseExamGapConstraint['PublishedCourse']['Course']['id'])); ?>
		</td>
		<td><?php echo $courseExamGapConstraint['PublishedCourse']['Section']['name']; ?>&nbsp;</td>
		<td><?php echo $courseExamGapConstraint['CourseExamGapConstraint']['gap_before_exam']; ?>&nbsp;</td>
		<td class="actions">
			<?php echo $this->Html->link(__('View'), array('action' => 'view', $courseExamGapConstraint['CourseExamGapConstraint']['id'])); ?>
			<!-- <?php echo $this->Html->link(__('Edit'), array('action' => 'edit', $courseExamGapConstraint['CourseExamGapConstraint']['id'])); ?> -->
			<?php echo $this->Html->link(__('Delete'), array('action' => 'delete', $courseExamGapConstraint['CourseExamGapConstraint']['id']), null, sprintf(__('Are you sure you want to delete # %s?'), $courseExamGapConstraint['CourseExamGapConstraint']['id'])); ?>
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
	  </div> <!-- end of columns 12 -->
	</div> <!-- end of row --->
      </div> <!-- end of box-body -->
</div><!-- end of box -->
