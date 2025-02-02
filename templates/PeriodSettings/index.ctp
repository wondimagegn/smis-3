<?php ?>
<div class="box">
     <div class="box-body">
       <div class="row">
	  <div class="large-12 columns">
            
<div class="periodSettings index">
	<div class="smallheading"><?php echo __('Period Setting Lists'); ?></div>
	<?php echo "<div class='font'>".$college_name."</div>"; ?>
	<table cellpadding="0" cellspacing="0">
	<tr>
			<th><?php echo 'S.N<u>o</u>';?></th>
			
			<th><?php echo $this->Paginator->sort('period');?></th>
			<th><?php echo $this->Paginator->sort('Starting Time');?></th>
			<th class="actions"><?php echo __('Actions');?></th>
	</tr>
	<?php
	$i = 0;
	$count = $this->Paginator->counter('%start%');
	foreach ($periodSettings as $periodSetting):
		$class = null;
		if ($i++ % 2 == 0) {
			$class = ' class="altrow"';
		}
	?>
	<tr<?php echo $class;?>>
		<td><?php echo $count++; ?>&nbsp;</td>
		
		<td><?php echo $periodSetting['PeriodSetting']['period']; ?>&nbsp;</td>
		<td><?php echo $this->Format->humanize_hour($periodSetting['PeriodSetting']['hour']); ?>&nbsp;</td>
		<td class="actions">
			
			<?php echo $this->Html->link(__('Delete'), array('action' => 'delete', $periodSetting['PeriodSetting']['id']), null, sprintf(__('Are you sure you want to delete # %s?'), $periodSetting['PeriodSetting']['hour'])); ?>
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
