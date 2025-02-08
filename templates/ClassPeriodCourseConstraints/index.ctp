<?php ?>
<div class="box">
     <div class="box-body">
       <div class="row">
	  <div class="large-12 columns">
		
<div class="classPeriodCourseConstraints index">
	<h2><?php echo __('List of Class Period Course Constraints');?></h2>
	<table cellpadding="0" cellspacing="0">
	<tr>
			<th><?php echo 'S.N<u>o</u>';?></th>
			<th><?php echo $this->Paginator->sort('published_course_id');?></th>
			<th><?php echo $this->Paginator->sort('Academic Year');?></th>
			<th><?php echo $this->Paginator->sort('Semester');?></th>
			<th><?php echo $this->Paginator->sort('week_day');?></th>
			<th><?php echo $this->Paginator->sort('period');?></th>
			<th><?php echo $this->Paginator->sort('type');?></th>
			<th><?php echo $this->Paginator->sort('active');?></th>
			<th class="actions"><?php echo __('Actions');?></th>
	</tr>
	<?php
	$i = 0;
	$count = $this->Paginator->counter('%start%');
	foreach ($classPeriodCourseConstraints as $classPeriodCourseConstraint):
		$class = null;
		if ($i++ % 2 == 0) {
			$class = ' class="altrow"';
		}
		$week_day_name = null;
		switch($classPeriodCourseConstraint['ClassPeriod']['week_day']){
			case 1: $week_day_name ="Sunday"; break;
			case 2: $week_day_name ="Monday"; break;
			case 3: $week_day_name ="Tuesday"; break;
			case 4: $week_day_name ="Wednesday"; break;
			case 5: $week_day_name ="Thursday"; break;
			case 6: $week_day_name ="Friday"; break;
			case 7: $week_day_name ="Saturday"; break;
			default : $week_day_name =null;
		}
		$active = null;
		if($classPeriodCourseConstraint['ClassPeriodCourseConstraint']['active'] == 1){
			$active = "Assign";
		} else {
			$active = "Do Not Assign";
		}
	?>
	<tr<?php echo $class;?>>
		<td><?php echo $count++; ?>&nbsp;</td>
		<td>
			<?php echo $this->Html->link($classPeriodCourseConstraint['PublishedCourse']['Course']['course_code_title'], 
			array('controller' => 'published_courses', 'action' => 'view', 
			$classPeriodCourseConstraint['PublishedCourse']['Course']['id'])); ?>
		</td>
		<td><?php echo $classPeriodCourseConstraint['PublishedCourse']['academic_year']; ?>&nbsp;</td>
		<td><?php echo $classPeriodCourseConstraint['PublishedCourse']['semester']; ?>&nbsp;</td>
		<td><?php echo $week_day_name.' ('.$classPeriodCourseConstraint['ClassPeriod']['week_day'].')'; ?>&nbsp;</td>
		<td>
		<?php echo $classPeriodCourseConstraint['ClassPeriod']['PeriodSetting']['period'] .' ('.
			$this->Format->humanize_hour($classPeriodCourseConstraint['ClassPeriod']['PeriodSetting']['hour']).')'; ?>&nbsp;
		</td>
		<td><?php echo $classPeriodCourseConstraint['ClassPeriodCourseConstraint']['type']; ?>&nbsp;</td>
		<td><?php echo $active; ?>&nbsp;</td>
		<td class="actions">
			<!--<?php echo $this->Html->link(__('View'), array('action' => 'view', $classPeriodCourseConstraint['ClassPeriodCourseConstraint']['id'])); ?>

			<?php echo $this->Html->link(__('Edit'), array('action' => 'edit', $classPeriodCourseConstraint['ClassPeriodCourseConstraint']['id'])); ?> -->
			<?php echo $this->Html->link(__('Delete'), array('action' => 'delete', $classPeriodCourseConstraint['ClassPeriodCourseConstraint']['id']), null, sprintf(__('Are you sure you want to delete?'), $classPeriodCourseConstraint['ClassPeriodCourseConstraint']['id'])); ?>
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

