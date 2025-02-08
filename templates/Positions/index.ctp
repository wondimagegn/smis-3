<?php ?>
<div class="box">
     <div class="box-header bg-transparent">
  	 <h6 class="box-title">
		<?php echo __('Positions');?>
	     </h6>
     </div>
     <div class="box-body">
       <div class="row">
	  <div class="large-12 columns">
             <table cellpadding="0" cellspacing="0">
	<tr>
			<th>S.N<u>o</u></th>
			<th><?php echo $this->Paginator->sort('position');?></th>
			<th><?php echo $this->Paginator->sort('description');?></th>
			<th class="actions"><?php echo __('Actions');?></th>
	</tr>
	<?php
	$i = 0;
	$start = $this->Paginator->counter('%start%');
	foreach ($positions as $position):
		$class = null;
		if ($i++ % 2 == 0) {
			$class = ' class="altrow"';
		}
	?>
	<tr<?php echo $class;?>>
		<td><?php echo $start++; ?>&nbsp;</td>
		<td><?php echo $position['Position']['position']; ?>&nbsp;</td>
		<td><?php echo $position['Position']['description']; ?>&nbsp;</td>
		<td class="actions">
			<?php echo $this->Html->link(__('View'), array('action' => 'view', $position['Position']['id'])); ?>
			<?php echo $this->Html->link(__('Edit'), array('action' => 'edit', $position['Position']['id'])); ?>
			<?php echo $this->Html->link(__('Delete'), array('action' => 'delete', $position['Position']['id']), null, sprintf(__('Are you sure you want to delete # %s?'), $position['Position']['id'])); ?>
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
	</div>
     </div>
</div>
