<div class="staffStudies index">
	<h2><?php echo __('Staff Studies'); ?></h2>
	<table cellpadding="0" cellspacing="0">
	<thead>
	<tr>
			<th><?php echo $this->Paginator->sort('id'); ?></th>
			<th><?php echo $this->Paginator->sort('staff_id'); ?></th>
			<th><?php echo $this->Paginator->sort('education'); ?></th>
			<th><?php echo $this->Paginator->sort('leave_date'); ?></th>
			<th><?php echo $this->Paginator->sort('return_date'); ?></th>
			<th><?php echo $this->Paginator->sort('committement_signed'); ?></th>
			<th><?php echo $this->Paginator->sort('specialization'); ?></th>
			<th><?php echo $this->Paginator->sort('country_id'); ?></th>
			<th><?php echo $this->Paginator->sort('university_joined'); ?></th>
			<th><?php echo $this->Paginator->sort('created'); ?></th>
			<th><?php echo $this->Paginator->sort('modified'); ?></th>
			<th class="actions"><?php echo __('Actions'); ?></th>
	</tr>
	</thead>
	<tbody>
	<?php foreach ($staffStudies as $staffStudy): ?>
	<tr>
		<td><?php echo h($staffStudy['StaffStudy']['id']); ?>&nbsp;</td>
		<td>
			<?php echo $this->Html->link($staffStudy['Staff']['id'], array('controller' => 'staffs', 'action' => 'view', $staffStudy['Staff']['id'])); ?>
		</td>
		<td><?php echo h($staffStudy['StaffStudy']['education']); ?>&nbsp;</td>
		<td><?php echo h($staffStudy['StaffStudy']['leave_date']); ?>&nbsp;</td>
		<td><?php echo h($staffStudy['StaffStudy']['return_date']); ?>&nbsp;</td>
		<td><?php echo h($staffStudy['StaffStudy']['committement_signed']); ?>&nbsp;</td>
		<td><?php echo h($staffStudy['StaffStudy']['specialization']); ?>&nbsp;</td>
		<td>
			<?php echo $this->Html->link($staffStudy['Country']['name'], array('controller' => 'countries', 'action' => 'view', $staffStudy['Country']['id'])); ?>
		</td>
		<td><?php echo h($staffStudy['StaffStudy']['university_joined']); ?>&nbsp;</td>
		<td><?php echo h($staffStudy['StaffStudy']['created']); ?>&nbsp;</td>
		<td><?php echo h($staffStudy['StaffStudy']['modified']); ?>&nbsp;</td>
		<td class="actions">
			<?php echo $this->Html->link(__('View'), array('action' => 'view', $staffStudy['StaffStudy']['id'])); ?>
			<?php echo $this->Html->link(__('Edit'), array('action' => 'edit', $staffStudy['StaffStudy']['id'])); ?>
			<?php echo $this->Form->postLink(__('Delete'), array('action' => 'delete', $staffStudy['StaffStudy']['id']), array('confirm' => __('Are you sure you want to delete # %s?', $staffStudy['StaffStudy']['id']))); ?>
		</td>
	</tr>
<?php endforeach; ?>
	</tbody>
	</table>
	<p>
	<?php
	echo $this->Paginator->counter(array(
		'format' => __('Page {:page} of {:pages}, showing {:current} records out of {:count} total, starting on record {:start}, ending on {:end}')
	));
	?>	</p>
	<div class="paging">
	<?php
		echo $this->Paginator->prev('< ' . __('previous'), array(), null, array('class' => 'prev disabled'));
		echo $this->Paginator->numbers(array('separator' => ''));
		echo $this->Paginator->next(__('next') . ' >', array(), null, array('class' => 'next disabled'));
	?>
	</div>
</div>
