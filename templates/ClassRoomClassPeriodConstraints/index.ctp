<?php ?>
<div class="box">
     <div class="box-body">
       <div class="row">
	  <div class="large-12 columns">
             
<div class="classRoomClassPeriodConstraints index">
	<h2><?php echo __('List of Class Room Class Period Constraints');?></h2>
	<table cellpadding="0" cellspacing="0">
	<tr>
			<th><?php echo 'S.N<u>o</u>';?></th>
			<th><?php echo $this->Paginator->sort('class_room_id');?></th>
			<th><?php echo $this->Paginator->sort('Block');?></th>
			<th><?php echo $this->Paginator->sort('Campus');?></th>
			<th><?php echo $this->Paginator->sort('academic_year');?></th>
			<th><?php echo $this->Paginator->sort('semester');?></th>
			<th><?php echo $this->Paginator->sort('Week Day');?></th>
			<th><?php echo $this->Paginator->sort('Period');?></th>
			<th><?php echo $this->Paginator->sort('Option');?></th>
			<th class="actions"><?php echo __('Actions');?></th>
	</tr>
	<?php
	$i = 0;
	$count = $this->Paginator->counter('%start%');
	foreach ($classRoomClassPeriodConstraints as $classRoomClassPeriodConstraint):
		$class = null;
		if ($i++ % 2 == 0) {
			$class = ' class="altrow"';
		}
		$week_day_name = null;
		switch($classRoomClassPeriodConstraint['ClassPeriod']['week_day']){
			case 1: $week_day_name ="Sunday"; break;
			case 2: $week_day_name ="Monday"; break;
			case 3: $week_day_name ="Tuesday"; break;
			case 4: $week_day_name ="Wednesday"; break;
			case 5: $week_day_name ="Thursday"; break;
			case 6: $week_day_name ="Friday"; break;
			case 7: $week_day_name ="Saturday"; break;
			default : $week_day_name =null;
		}
		$option = null;
		if($classRoomClassPeriodConstraint['ClassRoomClassPeriodConstraint']['active'] == 1){
			$option ="Occupied";
		} else {
			$option= "Free";
		}
	?>
	<tr<?php echo $class;?>>
		<td><?php echo $count++; ?>&nbsp;</td>
		<td>
			<?php echo $this->Html->link($classRoomClassPeriodConstraint['ClassRoom']['room_code'], array('controller' => 'class_rooms', 'action' => 'view', $classRoomClassPeriodConstraint['ClassRoom']['id'])); ?>
		</td>
		<td><?php echo $classRoomClassPeriodConstraint['ClassRoom']['ClassRoomBlock']['block_code']; ?>&nbsp;</td>
		<td><?php echo $classRoomClassPeriodConstraint['ClassRoom']['ClassRoomBlock']['Campus']['name']; ?>&nbsp;</td>

		<td><?php echo $classRoomClassPeriodConstraint['ClassRoomClassPeriodConstraint']['academic_year']; ?>&nbsp;</td>
		<td><?php echo $classRoomClassPeriodConstraint['ClassRoomClassPeriodConstraint']['semester']; ?>&nbsp;</td>
		<td><?php echo $week_day_name.'('.$classRoomClassPeriodConstraint['ClassPeriod']['week_day'].')'; ?>&nbsp;</td>
		<td><?php echo $this->Format->humanize_hour($classRoomClassPeriodConstraint['ClassPeriod']['PeriodSetting']['hour']); ?>&nbsp;</td>
		<td><?php echo $option; ?>&nbsp;</td>
		<td class="actions">
			<!-- <?php echo $this->Html->link(__('View'), array('action' => 'view', $classRoomClassPeriodConstraint['ClassRoomClassPeriodConstraint']['id'])); ?>
			<?php echo $this->Html->link(__('Edit'), array('action' => 'edit', $classRoomClassPeriodConstraint['ClassRoomClassPeriodConstraint']['id'])); ?> -->
			<?php echo $this->Html->link(__('Delete'), array('action' => 'delete', $classRoomClassPeriodConstraint['ClassRoomClassPeriodConstraint']['id']), null, sprintf(__('Are you sure you want to delete # %s?'), $classRoomClassPeriodConstraint['ClassRoomClassPeriodConstraint']['id'])); ?>
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
