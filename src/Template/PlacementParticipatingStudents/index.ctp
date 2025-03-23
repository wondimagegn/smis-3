<div class="placementParticipatingStudents index">
	<h2><?php echo __('Placement Participating Students'); ?></h2>
	<table cellpadding="0" cellspacing="0">
	<thead>
	<tr>
			<th><?php echo $this->Paginator->sort('id'); ?></th>
			<th><?php echo $this->Paginator->sort('accepted_student_id'); ?></th>
			<th><?php echo $this->Paginator->sort('student_id'); ?></th>
			<th><?php echo $this->Paginator->sort('program_id'); ?></th>
			<th><?php echo $this->Paginator->sort('program_type_id'); ?></th>
			<th><?php echo $this->Paginator->sort('original_college_department'); ?></th>
			<th><?php echo $this->Paginator->sort('academic_year'); ?></th>
			<th><?php echo $this->Paginator->sort('round'); ?></th>
			<th><?php echo $this->Paginator->sort('total_placement_weight'); ?></th>
			<th><?php echo $this->Paginator->sort('female_placement_weight'); ?></th>
			<th><?php echo $this->Paginator->sort('disability_weight'); ?></th>
			<th><?php echo $this->Paginator->sort('developing_region_weight'); ?></th>
			<th><?php echo $this->Paginator->sort('placement_round_participant_id'); ?></th>
			<th><?php echo $this->Paginator->sort('placement_type'); ?></th>
			<th><?php echo $this->Paginator->sort('status'); ?></th>
			<th><?php echo $this->Paginator->sort('remark'); ?></th>
			<th><?php echo $this->Paginator->sort('created'); ?></th>
			<th><?php echo $this->Paginator->sort('modified'); ?></th>
			<th class="actions"><?php echo __('Actions'); ?></th>
	</tr>
	</thead>
	<tbody>
	<?php foreach ($placementParticipatingStudents as $placementParticipatingStudent): ?>
	<tr>
		<td><?php echo h($placementParticipatingStudent['PlacementParticipatingStudent']['id']); ?>&nbsp;</td>
		<td>
			<?php echo $this->Html->link($placementParticipatingStudent['AcceptedStudent']['id'], array('controller' => 'accepted_students', 'action' => 'view', $placementParticipatingStudent['AcceptedStudent']['id'])); ?>
		</td>
		<td>
			<?php echo $this->Html->link($placementParticipatingStudent['Student']['id'], array('controller' => 'students', 'action' => 'view', $placementParticipatingStudent['Student']['id'])); ?>
		</td>
		<td><?php echo h($placementParticipatingStudent['PlacementParticipatingStudent']['program_id']); ?>&nbsp;</td>
		<td><?php echo h($placementParticipatingStudent['PlacementParticipatingStudent']['program_type_id']); ?>&nbsp;</td>
		<td><?php echo h($placementParticipatingStudent['PlacementParticipatingStudent']['original_college_department']); ?>&nbsp;</td>
		<td><?php echo h($placementParticipatingStudent['PlacementParticipatingStudent']['academic_year']); ?>&nbsp;</td>
		<td><?php echo h($placementParticipatingStudent['PlacementParticipatingStudent']['round']); ?>&nbsp;</td>
		<td><?php echo h($placementParticipatingStudent['PlacementParticipatingStudent']['total_placement_weight']); ?>&nbsp;</td>
		<td><?php echo h($placementParticipatingStudent['PlacementParticipatingStudent']['female_placement_weight']); ?>&nbsp;</td>
		<td><?php echo h($placementParticipatingStudent['PlacementParticipatingStudent']['disability_weight']); ?>&nbsp;</td>
		<td><?php echo h($placementParticipatingStudent['PlacementParticipatingStudent']['developing_region_weight']); ?>&nbsp;</td>
		<td><?php echo h($placementParticipatingStudent['PlacementParticipatingStudent']['placement_round_participant_id']); ?>&nbsp;</td>
		<td><?php echo h($placementParticipatingStudent['PlacementParticipatingStudent']['placement_type']); ?>&nbsp;</td>
		<td><?php echo h($placementParticipatingStudent['PlacementParticipatingStudent']['status']); ?>&nbsp;</td>
		<td><?php echo h($placementParticipatingStudent['PlacementParticipatingStudent']['remark']); ?>&nbsp;</td>
		<td><?php echo h($placementParticipatingStudent['PlacementParticipatingStudent']['created']); ?>&nbsp;</td>
		<td><?php echo h($placementParticipatingStudent['PlacementParticipatingStudent']['modified']); ?>&nbsp;</td>
		<td class="actions">
			<?php echo $this->Html->link(__('View'), array('action' => 'view', $placementParticipatingStudent['PlacementParticipatingStudent']['id'])); ?>
			<?php echo $this->Html->link(__('Edit'), array('action' => 'edit', $placementParticipatingStudent['PlacementParticipatingStudent']['id'])); ?>
			<?php echo $this->Form->postLink(__('Delete'), array('action' => 'delete', $placementParticipatingStudent['PlacementParticipatingStudent']['id']), array('confirm' => __('Are you sure you want to delete # %s?', $placementParticipatingStudent['PlacementParticipatingStudent']['id']))); ?>
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
<div class="actions">
	<h3><?php echo __('Actions'); ?></h3>
	<ul>
		<li><?php echo $this->Html->link(__('New Placement Participating Student'), array('action' => 'add')); ?></li>
		<li><?php echo $this->Html->link(__('List Accepted Students'), array('controller' => 'accepted_students', 'action' => 'index')); ?> </li>
		<li><?php echo $this->Html->link(__('New Accepted Student'), array('controller' => 'accepted_students', 'action' => 'add')); ?> </li>
		<li><?php echo $this->Html->link(__('List Students'), array('controller' => 'students', 'action' => 'index')); ?> </li>
		<li><?php echo $this->Html->link(__('New Student'), array('controller' => 'students', 'action' => 'add')); ?> </li>
		<li><?php echo $this->Html->link(__('List Placement Participating Programs'), array('controller' => 'placement_participating_programs', 'action' => 'index')); ?> </li>
		<li><?php echo $this->Html->link(__('New Placement Participating Program'), array('controller' => 'placement_participating_programs', 'action' => 'add')); ?> </li>
	</ul>
</div>
