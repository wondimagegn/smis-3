<div class="attachments index">
	<h2><?php echo __('Attachments');?></h2>
	<table cellpadding="0" cellspacing="0">
	<tr>
			<th><?php echo $this->Paginator->sort('id');?></th>
			<th><?php echo $this->Paginator->sort('model');?></th>
			<th><?php echo $this->Paginator->sort('foreign_key');?></th>
			<th><?php echo $this->Paginator->sort('dirname');?></th>
			<th><?php echo $this->Paginator->sort('basename');?></th>
			<th><?php echo $this->Paginator->sort('checksum');?></th>
			<th><?php echo $this->Paginator->sort('group');?></th>
			<th><?php echo $this->Paginator->sort('alternative');?></th>
			<th><?php echo $this->Paginator->sort('created');?></th>
			<th><?php echo $this->Paginator->sort('modified');?></th>
			<th class="actions"><?php echo __('Actions');?></th>
	</tr>
	<?php
	$i = 0;
	foreach ($attachments as $attachment):
		$class = null;
		if ($i++ % 2 == 0) {
			$class = ' class="altrow"';
		}
	?>
	<tr<?php echo $class;?>>
		<td><?php echo $attachment['Attachment']['id']; ?>&nbsp;</td>
		<td><?php echo $attachment['Attachment']['model']; ?>&nbsp;</td>
		<td><?php echo $attachment['Attachment']['foreign_key']; ?>&nbsp;</td>
		<td><?php echo $attachment['Attachment']['dirname']; ?>&nbsp;</td>
		<td><?php echo $attachment['Attachment']['basename']; ?>&nbsp;</td>
		<td><?php echo $attachment['Attachment']['checksum']; ?>&nbsp;</td>
		<td><?php echo $attachment['Attachment']['group']; ?>&nbsp;</td>
		<td><?php echo $attachment['Attachment']['alternative']; ?>&nbsp;</td>
		<td><?php echo $attachment['Attachment']['created']; ?>&nbsp;</td>
		<td><?php echo $attachment['Attachment']['modified']; ?>&nbsp;</td>
		<td class="actions">
			<?php echo $this->Html->link(__('View'), array('action' => 'view', $attachment['Attachment']['id'])); ?>
			<?php echo $this->Html->link(__('Edit'), array('action' => 'edit', $attachment['Attachment']['id'])); ?>
			<?php echo $this->Html->link(__('Delete'), array('action' => 'delete', $attachment['Attachment']['id']), null, sprintf(__('Are you sure you want to delete # %s?'), $attachment['Attachment']['id'])); ?>
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
		<li><?php echo $this->Html->link(__('New Attachment'), array('action' => 'add')); ?></li>
	</ul>
</div>