<div class="withdrawals index">
	<h2><?php echo __('Withdrawals');?></h2>
	<table cellpadding="0" cellspacing="0">
	<tr>
			<th><?php echo $this->Paginator->sort('id');?></th>
			<th><?php echo $this->Paginator->sort('student_id');?></th>
			<th><?php echo $this->Paginator->sort('reason');?></th>
			<th><?php echo $this->Paginator->sort('acceptance_date');?></th>
			<th><?php echo $this->Paginator->sort('forced_withdrawal');?></th>
			<th><?php echo $this->Paginator->sort('minute_number');?></th>
			<th><?php echo $this->Paginator->sort('created');?></th>
			<th><?php echo $this->Paginator->sort('modified');?></th>
			<th class="actions"><?php echo __('Actions');?></th>
	</tr>
	<?php
	$i = 0;
	foreach ($withdrawals as $withdrawal):
		$class = null;
		if ($i++ % 2 == 0) {
			$class = ' class="altrow"';
		}
	?>
	<tr<?php echo $class;?>>
		<td><?php echo $withdrawal['Withdrawal']['id']; ?>&nbsp;</td>
		<td>
			<?php echo $this->Html->link($withdrawal['Student']['id'], array('controller' => 'students', 'action' => 'view', $withdrawal['Student']['id'])); ?>
		</td>
		<td><?php echo $withdrawal['Withdrawal']['reason']; ?>&nbsp;</td>
		<td><?php echo $withdrawal['Withdrawal']['acceptance_date']; ?>&nbsp;</td>
		<td><?php echo $withdrawal['Withdrawal']['forced_withdrawal']; ?>&nbsp;</td>
		<td><?php echo $withdrawal['Withdrawal']['minute_number']; ?>&nbsp;</td>
		<td><?php echo $withdrawal['Withdrawal']['created']; ?>&nbsp;</td>
		<td><?php echo $withdrawal['Withdrawal']['modified']; ?>&nbsp;</td>
		<td class="actions">
			<?php echo $this->Html->link(__('View'), array('action' => 'view', $withdrawal['Withdrawal']['id'])); ?>
			<?php echo $this->Html->link(__('Edit'), array('action' => 'edit', $withdrawal['Withdrawal']['id'])); ?>
			<?php echo $this->Html->link(__('Delete'), array('action' => 'delete', $withdrawal['Withdrawal']['id']), null, sprintf(__('Are you sure you want to delete # %s?'), $withdrawal['Withdrawal']['id'])); ?>
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
		<li><?php echo $this->Html->link(__('New Withdrawal'), array('action' => 'add')); ?></li>
		<li><?php echo $this->Html->link(__('List Students'), array('controller' => 'students', 'action' => 'index')); ?> </li>
		<li><?php echo $this->Html->link(__('New Student'), array('controller' => 'students', 'action' => 'add')); ?> </li>
	</ul>
</div>