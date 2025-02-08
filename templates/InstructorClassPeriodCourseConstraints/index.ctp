<?php ?>
<div class="box">
     <div class="box-body">
       <div class="row">
	  <div class="large-12 columns">
            
<div class="instructorClassPeriodCourseConstraints index">
	<h2><?php echo __('List of Instructor Class Period Constraints');?></h2>
	<table cellpadding="0" cellspacing="0">
	<tr>
			<th><?php echo 'S.N<u>o</u>';?></th>
			<th><?php echo $this->Paginator->sort('Instructor');?></th>
			<th><?php echo $this->Paginator->sort('Position');?></th>
			<th><?php echo $this->Paginator->sort('academic_year');?></th>
			<th><?php echo $this->Paginator->sort('semester');?></th>
			<th><?php echo $this->Paginator->sort('Week Day');?></th>
			<th><?php echo $this->Paginator->sort('class_period');?></th>
			<th><?php echo $this->Paginator->sort('active');?></th>
			<th class="actions"><?php echo __('Actions');?></th>
	</tr>
	<?php
	$i = 0;
	$count = $this->Paginator->counter('%start%');
	foreach ($instructorClassPeriodCourseConstraints as $instructorClassPeriodCourseConstraint):
		$class = null;
		if ($i++ % 2 == 0) {
			$class = ' class="altrow"';
		}
		$week_day_name = null;
		switch($instructorClassPeriodCourseConstraint['ClassPeriod']['week_day']){
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
		if($instructorClassPeriodCourseConstraint['InstructorClassPeriodCourseConstraint']['active'] == 1){
			$active = "Occupied";
		} else {
			$active = "Free";
		}
	?>
	<tr<?php echo $class;?>>
		<td><?php echo $count++; ?>&nbsp;</td>
		<td><?php echo $instructorClassPeriodCourseConstraint['Staff']['Title']['title'].' '.$instructorClassPeriodCourseConstraint['Staff']['first_name'].' '.$instructorClassPeriodCourseConstraint['Staff']['middle_name'].' '.$instructorClassPeriodCourseConstraint['Staff']['last_name']; ?></td>
		<td><?php echo $instructorClassPeriodCourseConstraint['Staff']['Position']['position'];?></td>
		<td><?php echo $instructorClassPeriodCourseConstraint['InstructorClassPeriodCourseConstraint']['academic_year']; ?>&nbsp;</td>
		<td><?php echo $instructorClassPeriodCourseConstraint['InstructorClassPeriodCourseConstraint']['semester']; ?>&nbsp;</td>
		<td><?php echo $week_day_name.' ('.$instructorClassPeriodCourseConstraint['ClassPeriod']['week_day'].')'; ?></td>
		<td><?php echo $instructorClassPeriodCourseConstraint['ClassPeriod']['PeriodSetting']['period'] .' ('.$this->Format->humanize_hour($instructorClassPeriodCourseConstraint['ClassPeriod']['PeriodSetting']['hour']).')'; ?></td>
		<td><?php echo $active; ?>&nbsp;</td>
		<td class="actions">
			<!-- <?php echo $this->Html->link(__('View'), array('action' => 'view', $instructorClassPeriodCourseConstraint['InstructorClassPeriodCourseConstraint']['id'])); ?>
			<?php echo $this->Html->link(__('Edit'), array('action' => 'edit', $instructorClassPeriodCourseConstraint['InstructorClassPeriodCourseConstraint']['id'])); ?> -->
			<?php echo $this->Html->link(__('Delete'), array('action' => 'delete', $instructorClassPeriodCourseConstraint['InstructorClassPeriodCourseConstraint']['id']), null, sprintf(__('Are you sure you want to delete # %s?'), $instructorClassPeriodCourseConstraint['InstructorClassPeriodCourseConstraint']['id'])); ?>
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
