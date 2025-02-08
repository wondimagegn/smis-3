<?php ?>
<div class="box">
     <div class="box-header bg-transparent">
  	 <h6 class="box-title">
		<?php echo __('University Name Management');?>
	     </h6>
     </div>
     <div class="box-body">
       <div class="row">
	  <div class="large-12 columns">
		<table cellpadding="0" cellspacing="0">

	<tr>
			<th>S.N<u>o</u></th>
			<th><?php echo $this->Paginator->sort('name');?></th>
			<th><?php echo $this->Paginator->sort('amharic_name');?></th>
			<th><?php echo $this->Paginator->sort('short_name');?></th>
			<th><?php echo $this->Paginator->sort('amharic_short_name');?></th>
			<th><?php echo $this->Paginator->sort('academic_year');?></th>
		
			<th class="actions"><?php echo __('Actions');?></th>
	</tr>
	<?php
	$i = 0;
	$start = $this->Paginator->counter('%start%');
	foreach ($universities as $university):
		$class = null;
		if ($i++ % 2 == 0) {
			$class = ' class="altrow"';
		}
	?>
	<tr<?php echo $class;?>>
		<td><?php echo $start++; ?>&nbsp;</td>
		<td><?php echo $university['University']['name']; ?>&nbsp;</td>
		<td><?php echo $university['University']['amharic_name']; ?>&nbsp;</td>
		<td><?php echo $university['University']['short_name']; ?>&nbsp;</td>
		<td><?php echo $university['University']['amharic_short_name']; ?>&nbsp;</td>
		<td><?php echo $university['University']['academic_year']; ?>&nbsp;</td>
		<td class="actions">
			<?php echo $this->Html->link(__('View'), array('action' => 'view', $university['University']['id'])); ?>
			<?php echo $this->Html->link(__('Edit'), array('action' => 'edit', $university['University']['id'])); ?>
			<?php echo $this->Html->link(__('Delete'), array('action' => 'delete', $university['University']['id']), null, sprintf(__('Are you sure you want to delete "%s" university name?'), $university['University']['name'])); ?>
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
