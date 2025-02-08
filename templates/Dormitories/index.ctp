<div class="dormitories index">
	<h2><?php echo __('Dormitories');?></h2>
	<table cellpadding="0" cellspacing="0">
	<tr>
			<th><?php echo $this->Paginator->sort('id');?></th>
			<th><?php echo $this->Paginator->sort('dormitory_block_id');?></th>
			<th><?php echo $this->Paginator->sort('dorm_number');?></th>
			<th><?php echo $this->Paginator->sort('floor');?></th>
			<th><?php echo $this->Paginator->sort('capacity');?></th>
			<th><?php echo $this->Paginator->sort('available');?></th>
			<th><?php echo $this->Paginator->sort('created');?></th>
			<th><?php echo $this->Paginator->sort('modified');?></th>
			<th class="actions"><?php echo __('Actions');?></th>
	</tr>
	<?php
	$i = 0;
	foreach ($dormitories as $dormitory):
		$class = null;
		if ($i++ % 2 == 0) {
			$class = ' class="altrow"';
		}
	?>
	<tr<?php echo $class;?>>
		<td><?php echo $dormitory['Dormitory']['id']; ?>&nbsp;</td>
		<td>
			<?php echo $this->Html->link($dormitory['DormitoryBlock']['id'], array('controller' => 'dormitory_blocks', 'action' => 'view', $dormitory['DormitoryBlock']['id'])); ?>
		</td>
		<td><?php echo $dormitory['Dormitory']['dorm_number']; ?>&nbsp;</td>
		<td><?php echo $dormitory['Dormitory']['floor']; ?>&nbsp;</td>
		<td><?php echo $dormitory['Dormitory']['capacity']; ?>&nbsp;</td>
		<td><?php echo $dormitory['Dormitory']['available']; ?>&nbsp;</td>
		<td><?php echo $dormitory['Dormitory']['created']; ?>&nbsp;</td>
		<td><?php echo $dormitory['Dormitory']['modified']; ?>&nbsp;</td>
		<td class="actions">
			<?php echo $this->Html->link(__('View'), array('action' => 'view', $dormitory['Dormitory']['id'])); ?>
			<?php echo $this->Html->link(__('Edit'), array('action' => 'edit', $dormitory['Dormitory']['id'])); ?>
			<?php echo $this->Html->link(__('Delete'), array('action' => 'delete', $dormitory['Dormitory']['id']), null, sprintf(__('Are you sure you want to delete # %s?'), $dormitory['Dormitory']['id'])); ?>
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
		<li><?php echo $this->Html->link(__('New Dormitory'), array('action' => 'add')); ?></li>
		<li><?php echo $this->Html->link(__('List Dormitory Blocks'), array('controller' => 'dormitory_blocks', 'action' => 'index')); ?> </li>
		<li><?php echo $this->Html->link(__('New Dormitory Block'), array('controller' => 'dormitory_blocks', 'action' => 'add')); ?> </li>
	</ul>
</div>
