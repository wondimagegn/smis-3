<div class="dormitoryBlocks index">
<?php echo $this->Form->create('DormitoryBlock');?>
	<div class="smallheading"><?php echo __('List Of Dormitory Blocks and Dormitories');?></div>

	<table cellpadding="0" cellspacing="0">
	<?php 
		echo "<tr><td width='50%'>".$this->Form->input('campus_id',array('type'=>'select', 'empty'=>'All'))."</td></tr>";

		echo '<tr><td colspan=2>'.$this->Form->submit(__('Search'), array('div' => false,'class'=>'tiny radius button bg-blue')).'</td></tr>'; 
	?>
	</table>
	<table cellpadding="0" cellspacing="0">
	<tr>
		
			<th class="font"><?php echo "S.N<u>o</u>";?></th>
			<th class="font"><?php echo $this->Paginator->sort('block');?></th>
			<th class="font"><?php echo $this->Paginator->sort('type');?></th>
			<th class="font"><?php echo $this->Paginator->sort('campus');?></th>
			<th class="font"><?php echo $this->Paginator->sort('location');?></th>
			<th class="font"><?php echo $this->Paginator->sort('telephone_number');?></th>
			<th class="font"><?php echo $this->Paginator->sort('alt_telephone_number');?></th>
			<th class="actions"><?php echo __('Actions');?></th>
	</tr>
	<?php
	$i = 0;
	$start = $this->Paginator->counter('%start%');
	foreach ($dormitoryBlocks as $dormitoryBlock):
		$class = null;
		if ($i++ % 2 == 0) {
			$class = ' class="altrow"';
		}
	?>
	<tr<?php echo $class;?>>
		<td><?php echo $start++; ?>&nbsp;</td>
		<td><?php echo $dormitoryBlock['DormitoryBlock']['block_name']; ?>&nbsp;</td>
		<td><?php echo $dormitoryBlock['DormitoryBlock']['type']; ?>&nbsp;</td>
		<td><?php echo $dormitoryBlock['Campus']['name']; ?>&nbsp;</td>
		<td><?php echo $dormitoryBlock['DormitoryBlock']['location']; ?>&nbsp;</td>
		<td><?php echo $dormitoryBlock['DormitoryBlock']['telephone_number']; ?>&nbsp;</td>
		<td><?php echo $dormitoryBlock['DormitoryBlock']['alt_telephone_number']; ?>&nbsp;</td>
		<td class="actions">
			<?php echo $this->Html->link(__('View'), array('action' => 'view', $dormitoryBlock['DormitoryBlock']['id'])); ?> 
			<?php echo $this->Html->link(__('Edit'), array('action' => 'edit', $dormitoryBlock['DormitoryBlock']['id'])); ?>
			<?php echo $this->Html->link(__('Delete'), array('action' => 'delete', $dormitoryBlock['DormitoryBlock']['id']), null, sprintf(__('Are you sure you want to delete block %s?'), $dormitoryBlock['DormitoryBlock']['block_name'])); ?>
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
