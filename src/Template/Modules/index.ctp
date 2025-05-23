<div class="modules index">
	<h2><?php echo __('Modules');?></h2>
	<table cellpadding="0" cellspacing="0">
	<tr>
			<th><?php echo $this->Paginator->sort('id');?></th>
			<th><?php echo $this->Paginator->sort('parent_id');?></th>
			<th><?php echo $this->Paginator->sort('name');?></th>
			<th><?php echo $this->Paginator->sort('url');?></th>
			<th><?php echo $this->Paginator->sort('order');?></th>
			<th><?php echo $this->Paginator->sort('status');?></th>
			<th><?php echo $this->Paginator->sort('is_menu');?></th>
			<th class="actions"><?php echo __('Actions');?></th>
	</tr>
	<?php
	$i = 0;
	foreach ($modules as $module):
		$class = null;
		if ($i++ % 2 == 0) {
			$class = ' class="altrow"';
		}
	?>
	<tr<?php echo $class;?>>
		<td><?php echo $module['Module']['id']; ?>&nbsp;</td>
		<td><?php echo $module['Module']['parent_id']; ?>&nbsp;</td>
		<td><?php echo $module['Module']['name']; ?>&nbsp;</td>
		<td><?php echo $module['Module']['url']; ?>&nbsp;</td>
		<td><?php echo $module['Module']['order']; ?>&nbsp;</td>
		<td><?php echo $module['Module']['status']; ?>&nbsp;</td>
		<td><?php echo $module['Module']['is_menu']; ?>&nbsp;</td>
		<td class="actions">
			<?php echo $this->Html->link(__('View'), array('action' => 'view', $module['Module']['id'])); ?>
			<?php echo $this->Html->link(__('Edit'), array('action' => 'edit', $module['Module']['id'])); ?>
			<?php echo $this->Html->link(__('Delete'), array('action' => 'delete', $module['Module']['id']), null, sprintf(__('Are you sure you want to delete # %s?'), $module['Module']['id'])); ?>
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
		<li><?php echo $this->Html->link(__('New Module'), array('action' => 'add')); ?></li>
		<li><?php echo $this->Html->link(__('List Role Module Maps'), array('controller' => 'role_module_maps', 'action' => 'index')); ?> </li>
		<li><?php echo $this->Html->link(__('New Role Module Map'), array('controller' => 'role_module_maps', 'action' => 'add')); ?> </li>
	</ul>
</div>