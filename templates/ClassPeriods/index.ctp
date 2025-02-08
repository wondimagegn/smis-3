<?php ?>
<div class="box">
     <div class="box-body">
       <div class="row">
	  <div class="large-12 columns">
            
<div class="classPeriods index">
<?php echo $this->Form->create('ClassPeriod');?>
	<div class="smallheading"><?php echo __('List of Class Periods'); ?></div>
	<?php echo "<div class='font'>".$college_name."</div>"; ?>
	<table cellpadding="0" cellspacing="0">
	<?php 
		echo '<tr><td>'.$this->Form->input('programs',array('empty'=>'All')).'</td>';
			
		echo '<td >'.$this->Form->input('programTypes',array('empty'=>'All')).'</td></tr>'; 
		echo '<tr><td colspan=2>'.$this->Form->end(array('label'=>'Search','class'=>'tiny radius button bg-blue')).'</td></tr>'; 
	?>
	</table>
	<table cellpadding="0" cellspacing="0">
	<tr>
			<th><?php echo 'S.N<u>o</u>';?></th>
			<th><?php echo $this->Paginator->sort('week_day');?></th>
			<th><?php echo $this->Paginator->sort('periods');?></th>
			<!-- <th><?php echo $this->Paginator->sort('college_id');?></th> -->
			<th><?php echo $this->Paginator->sort('program_id');?></th>
			<th><?php echo $this->Paginator->sort('program_type_id');?></th>
			<th class="actions"><?php echo __('Actions');?></th>
	</tr>
	<?php
	$i = 0;
	$count = $this->Paginator->counter('%start%');
	foreach ($classPeriods as $classPeriod):
		$class = null;
		if ($i++ % 2 == 0) {
			$class = ' class="altrow"';
		}
		$week_day_name = null;
		switch($classPeriod['ClassPeriod']['week_day']){
			case 1: $week_day_name ="Sunday"; break;
			case 2: $week_day_name ="Monday"; break;
			case 3: $week_day_name ="Tuesday"; break;
			case 4: $week_day_name ="Wednesday"; break;
			case 5: $week_day_name ="Thursday"; break;
			case 6: $week_day_name ="Friday"; break;
			case 7: $week_day_name ="Saturday"; break;
			default : $week_day_name =null;
		}
	?>
	<tr<?php echo $class;?>>
		<td><?php echo $count++; ?>&nbsp;</td>
		<td><?php echo $classPeriod['ClassPeriod']['week_day'].' ('.$week_day_name.')'; ?>&nbsp;</td>
		<td><?php echo $classPeriod['PeriodSetting']['period'].' ('.$this->Format->humanize_hour($classPeriod['PeriodSetting']['hour']).')'; ?>&nbsp;</td>

		<td><?php echo $classPeriod['Program']['name']; ?>&nbsp;</td>
		<td><?php echo $classPeriod['ProgramType']['name']; ?>&nbsp;</td>
		<td class="actions">
			<!--<?php echo $this->Html->link(__('View'), array('action' => 'view', $classPeriod['ClassPeriod']['id'])); ?>

			<?php echo $this->Html->link(__('Edit'), array('action' => 'edit', $classPeriod['ClassPeriod']['id'])); ?> -->
			<?php echo $this->Html->link(__('Delete'), array('action' => 'delete', $classPeriod['ClassPeriod']['id']), null, sprintf(__('Are you sure you want to delete # %s?'), $classPeriod['ClassPeriod']['id'])); ?>
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
